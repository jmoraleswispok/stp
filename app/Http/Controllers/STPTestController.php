<?php

namespace App\Http\Controllers;

use App\Utilities\ModelUtility;
use App\Utilities\STPUtility;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;


class STPTestController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        try
        {
            $data = [
                'empresa' => "WISPOK",
                'tipoOrden' => "E",
                'fechaOperacion' => ""
            ];
            return $this->successResponse(STPUtility::sign($data));
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @return JsonResponse
     */
    public function checkAccountBalance(): JsonResponse
    {
        try
        {
            $data = [
                'empresa' => "WISPOK",
                'cuentaOrdenante' => "646180368700000009",
                'fecha' => ""
            ];
            $data['firma'] = STPUtility::sign($data);
            $responseStp = Http::baseUrl(env('STP_URL'))->post("consultaSaldoCuenta", $data);
            return $this->successResponse($responseStp->json());
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @return JsonResponse
     */
    public function registerOrder(): JsonResponse
    {
        try
        {
            $day = Carbon::now();
            $claveRastreo = "{$this->randomNumber(5)}WISPOK{$day->format('Ymd')}{$day->timestamp}";
            $data = [
                'institucionContraparte' => "90646",
                'empresa' => "WISPOK",
                'fechaOperacion' => "",
                'folioOrigen' => "",
                'claveRastreo' => $claveRastreo,
                'institucionOperante' => "90646",
                'monto' => "10.00",
                'tipoPago' => "1",
                'tipoCuentaOrdenante' => "40",
                'nombreOrdenante' => "WISPOK S.A. de C.V.",
                'cuentaOrdenante' => "646180368700000009",
                'rfcCurpOrdenante' => "DOAL010304XZ6",
                'tipoCuentaBeneficiario' => "40",
                'nombreBeneficiario' => "S.A. de C.V.",
                'cuentaBeneficiario' => "646180110400000007",
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
            //dd(json_encode($data), $firma);
            $responseStp = Http::baseUrl(env('STP_SPEI_URL'))->asJson()->withBody(json_encode($data2))->put("ordenPago/registra");
            return $this->successResponse($responseStp->json());
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @return JsonResponse
     */
    public function conciliation(): JsonResponse
    {
        try
        {
            $data = [
                'empresa' => "WISPOK",
                'tipoOrden' => "E",
                'fechaOperacion' => ""
            ];
            $data2 = $data;
            $data2['page'] = 0;
            $data2['firma'] = STPUtility::sign($data);
            $responseStp = Http::baseUrl(env('STP_URL'))->post("V2/conciliacion", $data2);
            return $this->successResponse($responseStp->json());
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }

}
