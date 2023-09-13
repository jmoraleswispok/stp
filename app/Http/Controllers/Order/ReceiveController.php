<?php

namespace App\Http\Controllers\Order;

use Exception;
use App\Firestore\SiapaFirestore;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\ReceiveRequest;
use App\Interfaces\HttpCodeInterface;
use App\Models\Siapa\PaymenthStp;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
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
            $beneficiaryAccount = $request->input('cuentaBeneficiario');
            $user = User::query()->where('stp_account', $beneficiaryAccount)->firstOr(function () {
                throw new Exception(json_encode([
                    'id' => 1,
                    'mensaje' => "Cuenta inexistente"
                ]));
            });

            $user->orderReceiveds()->create([
                'request' => json_encode($request->all())
            ]);

            if ($beneficiaryAccount !== env('STP_ACCOUNT_ACCEPTED')) {
                throw new Exception(json_encode([
                    'id' => 2,
                    'mensaje' => "Cuenta no autorizada."
                ]));
            }
            $siapaSTP = PaymenthStp::query()->with(['paymenth' => function($query) {
                return $query->with(['siapaUserInfo' => function($query) {
                    return $query->with(['siapaUser']);
                }]);
            }])->where('reference', $request->input('referenciaNumerica'))->firstOr(function () {
                throw new Exception(json_encode([
                    'id' => 1,
                    'mensaje' => "Referencia Numerica inexistente"
                ]));
            });
            if ($siapaSTP->paymenth->status !== 1) {
                throw new Exception(json_encode([
                    'id' => 2,
                    'mensaje' => "El pago ya no se encuentra pendiente."
                ]));
            }
            $amount = floatval($request->input('monto'));
            if (floatval($siapaSTP->paymenth->paymenth_a) !== $amount) {
                throw new Exception(json_encode([
                    'id' => 2,
                    'mensaje' => "Monto no autorizado."
                ]));
            }
            $siapaSTP->paymenth->update([
                'paymenth_at' => Carbon::now(),
                'status' => 2
            ]);
            $firestore = new SiapaFirestore('SIAPA');
            $firestore->set($siapaSTP->paymenth->siapaUserInfo->siapaUser->account_contract, $siapaSTP->paymenth->uuid,2, $siapaSTP->full_name, $amount);
            return response()->json([
                'mensaje' => "confirmar"
            ]);
        } catch (Exception $e) {
            $message = json_decode($e->getMessage(), true);
            Log::error(json_encode($e->getMessage()));
            return response()->json($message,HttpCodeInterface::BAD_REQUEST);
        }
    }
}
