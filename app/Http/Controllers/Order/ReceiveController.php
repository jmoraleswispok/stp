<?php

namespace App\Http\Controllers\Order;

use App\Models\OrderReceived;
use App\Utilities\ModelUtility;
use Exception;
use App\Firestore\SiapaFirestore;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\ReceiveRequest;
use App\Interfaces\HttpCodeInterface;
use App\Models\Siapa\PaymenthStp;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ReceiveController extends Controller
{

    protected $firestore = false;
    protected $account = '';
    protected $uuid = '';
    protected $amount = 0;
    protected $stpAmount = 0;
    protected $fullName = '';
    protected $siapaSTP = null;
    protected $message = '';

    /**
     * Handle the incoming request.
     */
    public function __invoke(ReceiveRequest $request)
    {
        Log::info("ReceiveController -> request");
        Log::info(json_encode($request->all()));
        try
        {
            $orderReceived = OrderReceived::query()->where('stp_id', $request->input('id'))->first();
            if ($orderReceived) {
                $orderReceived->update([
                    'retries' => intval($orderReceived->retries) + 1
                ]);
            } else {
                $orderReceived = OrderReceived::query()->create([
                    'stp_id' => $request->input('id'),
                    'request' => json_encode($request->all())
                ]);
            }
            $retry = $orderReceived->retries()->create([
                'request' => json_encode($request->all())
            ]);

            $reference = $request->input('referenciaNumerica');
            $siapaSTP = PaymenthStp::query()->with(['paymenth' => function($query) {
                return $query->with(['siapaUserInfo' => function($query) {
                    return $query->with(['siapaUser']);
                }]);
            }])->where('reference', $reference)->firstOr(function () use ($retry) {
                $message = "Referencia numérica inexistente.";
                $retry->update([
                    'reason_for_rejection' => $message
                ]);
                throw new Exception(json_encode([
                    'mensaje' => 'devolver',
                    'id' => 14
                ]));
            });
            $this->siapaSTP = $siapaSTP;
            $this->account = $siapaSTP->paymenth->siapaUserInfo->siapaUser->account_contract;
            $this->uuid = $siapaSTP->paymenth->uuid;
            $this->stpAmount = floatval($request->input('monto'));
            $this->amount = floatval($siapaSTP->paymenth->paymenth_a);
            $this->fullName = $siapaSTP->full_name;
            $this->firestore = true;

            $beneficiaryAccount = $request->input('cuentaBeneficiario');
            $user = User::query()->where('stp_account', $beneficiaryAccount)->firstOr(function () use ($retry) {
                $this->message = "Cuenta inexistente.";
                $retry->update([
                    'reason_for_rejection' => $this->message
                ]);
                throw new Exception(json_encode([
                    'mensaje' => 'devolver',
                    'id' => 6
                ]));
            });
            $orderReceived->update([
                'user_id' => $user->id
            ]);
            if ($beneficiaryAccount !== env('STP_ACCOUNT_ACCEPTED')) {
                $this->message = "Cuenta no autorizada.";
                $retry->update([
                    'reason_for_rejection' => $this->message
                ]);
                throw new Exception(json_encode([
                    'mensaje' => 'devolver',
                    'id' => 17
                ]));
            }

            if ($siapaSTP->paymenth->status !== 1) {
                $this->message = "El pago ya no se encuentra pendiente.";
                $retry->update([
                    'reason_for_rejection' => $this->message
                ]);
                throw new Exception(json_encode([
                    'mensaje' => 'devolver',
                    'id' => 15
                ]));
            }
            $siapaAmount = round(floatval($siapaSTP->paymenth->paymenth_a) + floatval(ModelUtility::nullSafeForNumeric($siapaSTP->paymenth->tax)),2);
            if ($siapaAmount !== $this->stpAmount) {
                $this->message = "Monto no autorizado.";
                $retry->update([
                    'reason_for_rejection' => $this->message
                ]);
                throw new Exception(json_encode([
                    'mensaje' => 'devolver',
                    'id' => 16
                ]));
            }
            DB::beginTransaction();
            $siapaSTP->paymenth->update([
                'paymenth_at' => Carbon::now(),
                'status' => 2
            ]);

            $firestore = new SiapaFirestore('SIAPA');
            $firestore->set($this->account, $this->uuid,2, $this->fullName, $this->amount);

            $orderReceived->update([
                'approved' => 1
            ]);
            $siapaSTP->update([
                'stp_id' => request()->input('id'),
                'data' => json_encode([
                    'claveRastreo' => request()->input('claveRastreo'),
                    'tsLiquidacion' => request()->input('tsLiquidacion'),
                    'cuentaOrdenante' => request()->input('cuentaOrdenante'),
                    'nombreOrdenante' => request()->input('nombreOrdenante'),
                    'conceptoPago' => request()->input('conceptoPago')
                ]),
                'tracking_key' =>request()->input('claveRastreo'),
                'ordering_name' => request()->input('nombreOrdenante'),
                'ordering_inst' => request()->input('institucionOrdenante'),
                'ordering_account' => request()->input('cuentaOrdenante'),
                'ordering_rfc' => request()->input('rfcCurpOrdenante'),
                'paymenth_concept' => request()->input('conceptoPago')
            ]);

            //solo si es de siapa se enviara
            if ($siapaSTP->paymenth->siapaUserInfo->siapaUser->is_inscription == 0){
                $this->affectBalance($this->account, $this->amount, $reference, $siapaSTP->paymenth->uuid);
            }

            DB::commit();
            return response()->json([
                'mensaje' => "confirmar"
            ]);
        } catch (Exception $e) {
            $message = json_decode($e->getMessage(), true);
            if (empty($message)) {
                Log::error($e->getMessage());
                $message = [
                    'mensaje' => 'devolver',
                    'id' => 15
                ];
            } else {
                Log::error(json_encode($e->getMessage()));
            }

            if ($this->firestore) {
                $this->siapaSTP->paymenth->update([
                    'status' => 0
                ]);

                $firestore = new SiapaFirestore('SIAPA');
                $firestore->set($this->account, $this->uuid,0, $this->fullName, $this->amount, $this->message);
            }

            return response()->json($message,HttpCodeInterface::BAD_REQUEST);
        }
    }

    private function affectBalance($account, $import, $reference, $paymenth)
    {
        try
        {
            $formData = [
                'cuenta' => $account,
                'importe' => $import,
                'autoriza' => $reference,
                'payment' => $paymenth
            ];
            Log::info(json_encode([
                'FormData' => $formData
            ]));
            $response = Http::baseUrl('192.168.100.10')->post('api/siapa/payment', $formData);
            Log::info(json_encode([
                'data' => $response->json()
            ]));
        }catch (Exception $e) {
            Log::error("affectBalance");
            Log::error($e->getMessage());
        }
    }

}
