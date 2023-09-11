<?php

namespace App\Http\Controllers;

use Exception;
use App\Http\Requests\STPTest\CheckAccountBalanceRequest;
use App\Utilities\ModelUtility;
use App\Utilities\STP\Utility;
use Illuminate\Support\Facades\Http;

class CheckAccountBalanceController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(CheckAccountBalanceRequest $request)
    {
        return $this->response(function (&$response) {
            $data = [
                'empresa' => env('STP_COMPANY'),
                'cuentaOrdenante' => env('STP_ORDERING_ACCOUNT'),
                'fecha' => ModelUtility::nullSafeForString(request()->input('date'))
            ];
            $formData = $data;
            $formData['firma'] = Utility::sign($data);
            $response = Http::baseUrl(env('STP_URL'))->post("consultaSaldoCuenta", $formData);
            $response->failed() && throw new Exception(json_encode($response->json()), $response->status());
            $response = $response->json();
        });
    }
}
