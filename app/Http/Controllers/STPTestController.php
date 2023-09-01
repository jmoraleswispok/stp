<?php

namespace App\Http\Controllers;

use App\Firestore\SiapaFirestore;
use App\Http\Requests\STPTest\CheckAccountBalanceRequest;
use App\Http\Requests\STPTest\ConciliationRequest;
use App\Http\Requests\STPTest\OrderReceivedRequest;
use App\Http\Requests\STPTest\OrderStatusChangesRequest;
use App\Http\Requests\STPTest\RegisterOrderRequest;
use App\Interfaces\HttpCodeInterface;
use App\Models\Order;
use App\Models\Siapa\PaymenthStp;
use App\Models\User;
use App\Utilities\ModelUtility;
use App\Utilities\STPUtility;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


class STPTestController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        Log::info(json_encode($request->all()));
        try
        {
            $data = [
                'empresa' => "WISPOK",
                'tipoOrden' => "E",
                'fechaOperacion' => ""
            ];
            return $this->successResponse(STPUtility::sign($data));
        } catch (Exception $e) {
            Log::error(json_encode($e->getMessage()));
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @return JsonResponse
     */
    public function checkAccountBalance(CheckAccountBalanceRequest $request): JsonResponse
    {
        Log::info(json_encode($request->all()));
        try
        {
            $data = [
                'empresa' => env('STP_COMPANY'),
                'cuentaOrdenante' => env('STP_ORDERING_ACCOUNT'),
                'fecha' => ModelUtility::nullSafeForString($request->input('date'))
            ];
            $data['firma'] = STPUtility::sign($data);
            $responseStp = Http::baseUrl(env('STP_URL'))->post("consultaSaldoCuenta", $data);
            return $this->successResponse($responseStp->json());
        } catch (Exception $e) {
            Log::error(json_encode($e->getMessage()));
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param RegisterOrderRequest $request
     * @return JsonResponse
     */
    public function registerOrder(RegisterOrderRequest $request): JsonResponse
    {
        Log::info(json_encode($request->all()));
        try
        {
            $day = Carbon::now();
            $claveRastreo = "{$this->randomNumber(5)}WISPOK{$day->format('Ymd')}{$day->timestamp}";
            $cuentaBeneficiario = $request->has('cuentaBeneficiario') ? $request->input('cuentaBeneficiario') : '646180110400000007';
            $data = [
                'institucionContraparte' => "90646",
                'empresa' => env('STP_COMPANY'),
                'fechaOperacion' => "",
                'folioOrigen' => "",
                'claveRastreo' => $claveRastreo,
                'institucionOperante' => "90646",
                'monto' => ModelUtility::numberFormat($request->input('amount')),
                'tipoPago' => "1",
                'tipoCuentaOrdenante' => "40",
                'nombreOrdenante' => "WISPOK S.A. de C.V.",
                'cuentaOrdenante' => env('STP_ORDERING_ACCOUNT'),
                'rfcCurpOrdenante' => "DOAL010304XZ6",
                'tipoCuentaBeneficiario' => "40",
                'nombreBeneficiario' => "S.A. de C.V.",
                'cuentaBeneficiario' => $cuentaBeneficiario,
                'rfcCurpBeneficiario' => "PXBK451111RO2",
                'emailBeneficiario' => "",
                'tipoCuentaBeneficiario2' => "",
                'nombreBeneficiario2' => "",
                'cuentaBeneficiario2' => "",
                'rfcCurpBeneficiario2' => "",
                'conceptoPago' => "Prueba 1",
                'conceptoPago2' => "",
                'claveCatalogoUsuario1' => "",
                'claveCatalogoUsuario2' => "",
                'clavePago' => "",
                'referenciaCobranza' => "",
                'referenciaNumerica' => "{$this->randomNumber(7)}",
                'tipoOperacion' => "",
                'topologia' => "",
                'usuario' => "",
                'medioEntrega' => "",
                'prioridad' => "",
                'iva' => "",
            ];
            $firma = STPUtility::sign($data);
            $data['firma'] = $firma;
            $data2 = [
                "claveRastreo" => $data['claveRastreo'],
                "conceptoPago" => $data['conceptoPago'],
                "cuentaBeneficiario" => $data['cuentaBeneficiario'],
                "cuentaOrdenante" => $data['cuentaOrdenante'],
                "empresa" => $data['empresa'],
                "institucionContraparte" => $data['institucionContraparte'],
                "institucionOperante" => $data['institucionOperante'],
                "monto" => $data['monto'],
                "nombreBeneficiario" => $data['nombreBeneficiario'],
                "nombreOrdenante" => $data['nombreOrdenante'],
                "referenciaNumerica" => $data['referenciaNumerica'],
                "rfcCurpBeneficiario" => $data['rfcCurpBeneficiario'],
                "rfcCurpOrdenante" => $data['rfcCurpOrdenante'],
                "tipoCuentaBeneficiario" => $data['tipoCuentaBeneficiario'],
                "tipoCuentaOrdenante" => $data['tipoCuentaOrdenante'],
                "tipoPago" => $data['tipoPago'],
                "firma" => $data['firma']
            ];
            $responseStp = Http::baseUrl(env('STP_SPEI_URL'))->asJson()->withBody(json_encode($data2))->put("ordenPago/registra");
            $response = $responseStp->json();
            if (!empty($response['resultado']['descripcionError'])) {
                throw new Exception($response['resultado']['descripcionError'], HttpCodeInterface::BAD_REQUEST);
            }
            Order::query()->create([
                'id_ef' => $response['resultado']['id']
            ]);
            return $this->successResponse($response);
        } catch (Exception $e) {
            Log::error(json_encode($e->getMessage()));
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param ConciliationRequest $request
     * @return JsonResponse
     */
    public function conciliation(ConciliationRequest $request): JsonResponse
    {
        Log::info(json_encode($request->all()));
        try
        {
            $data = [
                'empresa' => env('STP_COMPANY'),
                'tipoOrden' => "E",
                'fechaOperacion' => ModelUtility::nullSafeForString($request->input('operation_date'))
            ];
            $data2 = $data;
            $data2['page'] = 0;
            $data2['firma'] = STPUtility::sign($data);
            $responseStp = Http::baseUrl(env('STP_URL'))->post("V2/conciliacion", $data2);
            return $this->successResponse($responseStp->json());
        } catch (Exception $e) {
            Log::error(json_encode($e->getMessage()));
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }

    public function orderStatusChanges(OrderStatusChangesRequest $request)
    {
        Log::info(json_encode($request->all()));
        $order = Order::query()->where('id_ef', $request->input('id'))->first();
        $order?->update([
            'folio_origin' => $request->input('folioOrigen'),
            'status' => $request->input('estado'),
            'cause_return' => $request->input('causaDevolucion'),
            'ts_liquidation' => $request->input('tsLiquidacion')
        ]);
        return response()->json([
            'mensaje' => "recibido"
        ]);
    }

    public function orderReceived(OrderReceivedRequest $request)
    {
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
            Log::error(json_encode($message));
            return response()->json($message,HttpCodeInterface::BAD_REQUEST);
        }
    }

}
