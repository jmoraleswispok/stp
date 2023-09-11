<?php

namespace App\Http\Controllers;

use Exception;
use App\Http\Requests\OrderRequest;
use App\Interfaces\HttpCodeInterface;
use App\Models\Order;
use App\Utilities\ModelUtility;
use App\Utilities\STP\Utility;
use App\Utilities\STPUtility;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class OrderController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(OrderRequest $request)
    {
        return $this->response(function (&$response) {
            $day = Carbon::now();
            $company = env('STP_COMPANY');
            $prefix = env('STP_PREFIX_TRACE_KEY');
            $claveRastreo = "{$this->randomNumber(2)}{$prefix}{$day->format('Ymd')}{$day->timestamp}{$day->milliseconds}";
            $data = [
                'institucionContraparte' => request()->input('counterparty_institution'),
                'empresa' => $company,
                'fechaOperacion' => "",
                'folioOrigen' => "",
                'claveRastreo' => $claveRastreo,
                'institucionOperante' => request()->input('operating_institution'),
                'monto' => ModelUtility::numberFormat(request()->input('amount')),
                'tipoPago' => "1",
                'tipoCuentaOrdenante' => request()->input('ordering_account_type'),
                'nombreOrdenante' => request()->input('ordering_name'),
                'cuentaOrdenante' => request()->input('ordering_account'),
                'rfcCurpOrdenante' => request()->input('ordering_rfc_curp'),
                'tipoCuentaBeneficiario' => request()->input('beneficiary_account_type'),
                'nombreBeneficiario' => request()->input('beneficiary_name'),
                'cuentaBeneficiario' => request()->input('beneficiary_account'),
                'rfcCurpBeneficiario' => ModelUtility::nullSafeForString(request()->input('beneficiary_rfc_curp')),
                'emailBeneficiario' => "",
                'tipoCuentaBeneficiario2' => "",
                'nombreBeneficiario2' => "",
                'cuentaBeneficiario2' => "",
                'rfcCurpBeneficiario2' => "",
                'conceptoPago' => request()->input('payment_concept'),
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
            $data['firma'] = Utility::sign($data);
            $formData = [
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
            $responseStp = Http::baseUrl(env('STP_SPEI_URL'))->put("ordenPago/registra", $formData);
            $response = $responseStp->json();
            if (!empty($response['resultado']['descripcionError'])) {
                throw new Exception($response['resultado']['descripcionError'], HttpCodeInterface::BAD_REQUEST);
            }
            Order::query()->create([
                'id_ef' => $response['resultado']['id']
            ]);
        });
    }
}
