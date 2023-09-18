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
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ReceiveController extends Controller
{
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
            $beneficiaryAccount = $request->input('cuentaBeneficiario');
            $user = User::query()->where('stp_account', $beneficiaryAccount)->firstOr(function () use ($retry) {
                $message = "Cuenta inexistente.";
                $retry->update([
                    'reason_for_rejection' => $message
                ]);
                throw new Exception(json_encode([
                    'id' => 1,
                    'mensaje' => $message
                ]));
            });
            $orderReceived->update([
                'user_id' => $user->id
            ]);
            if ($beneficiaryAccount !== env('STP_ACCOUNT_ACCEPTED')) {
                $message = "Cuenta no autorizada.";
                $retry->update([
                    'reason_for_rejection' => $message
                ]);
                throw new Exception(json_encode([
                    'id' => 2,
                    'mensaje' => $message
                ]));
            }
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
                    'id' => 1,
                    'mensaje' => $message
                ]));
            });
            if ($siapaSTP->paymenth->status !== 1) {
                $message = "El pago ya no se encuentra pendiente.";
                $retry->update([
                    'reason_for_rejection' => $message
                ]);
                throw new Exception(json_encode([
                    'id' => 2,
                    'mensaje' => $message
                ]));
            }
            $amount = floatval($request->input('monto'));
            $siapaAmount = floatval($siapaSTP->paymenth->paymenth_a) + floatval(ModelUtility::nullSafeForNumeric($siapaSTP->paymenth));

            if ($siapaAmount !== $amount) {
                $message = "Monto no autorizado.";
                $retry->update([
                    'reason_for_rejection' => $message
                ]);
                throw new Exception(json_encode([
                    'id' => 2,
                    'mensaje' => $message
                ]));
            }
            $siapaSTP->paymenth->update([
                'paymenth_at' => Carbon::now(),
                'status' => 2
            ]);
            $firestore = new SiapaFirestore('SIAPA');
            $account = $siapaSTP->paymenth->siapaUserInfo->siapaUser->account_contract;
            $firestore->set($account, $siapaSTP->paymenth->uuid,2, $siapaSTP->full_name, $amount);
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
                ])
            ]);
            $this->affectBalance($account, $amount, $reference);
            return response()->json([
                'mensaje' => "confirmar"
            ]);
        } catch (Exception $e) {
            $message = json_decode($e->getMessage(), true);
            if (empty($message)) {
                Log::error($e->getMessage());
                $message = [
                    'id' => 15,
                    'mensaje' => "No fue posible aceptar el movimiento"
                ];
            } else {
                Log::error(json_encode($e->getMessage()));
            }

            return response()->json($message,HttpCodeInterface::BAD_REQUEST);
        }
    }

    private function affectBalance($account, $import, $reference)
    {
        try
        {
            $formData = [
                'cuenta' => $account,
                'importe' => $import,
                'autoriza' => $reference
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
