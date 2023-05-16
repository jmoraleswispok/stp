<?php

namespace App\Http\Controllers;

use App\Utilities\STPUtility;
use Carbon\Carbon;
use Exception;
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
                'claveRastreo' => "WISPOK00001",
                'conceptoPago' => "Prueba 1",
                'cuentaBeneficiario' => "646180110400000007",
                'cuentaOrdenante' => "646180368700000009",
                'empresa' => "WISPOK",
                'fechaOperacion' => "",
                'folioOrigen' => "",
                'institucionContraparte' => "846",
                'institucionOperante' => "90646",
                'monto' => "10",
                'nombreBeneficiario' => "Test S.A. de C.V.",
                'nombreOrdenante' => "WISPOK S.A. de C.V.",
                'referenciaNumerica' => "1234568",
                'rfcCurpBeneficiario' => "PXBK451111RO2",
                'rfcCurpOrdenante' => "DOAL010304XZ6",
                'tipoCuentaBeneficiario' => "40",
                'tipoCuentaOrdenante' => "40",
                'tipoPago' => "1"
            ];
            //$data['firma'] = STPUtility::sign($data);
            //$responseStp = Http::baseUrl(env('STP_URL'))->post("consultaSaldoCuenta", $data);
            return $this->successResponse(STPUtility::sign($data));
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }

    public function checkAccountBalance()
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





}
