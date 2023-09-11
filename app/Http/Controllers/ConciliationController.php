<?php

namespace App\Http\Controllers;

use Exception;
use App\Http\Requests\STPTest\ConciliationRequest;
use App\Utilities\ModelUtility;
use App\Utilities\STP\Utility;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class ConciliationController extends Controller
{
    /**
     * Handle the incoming request.
     * @throws Exception
     */
    public function __invoke(ConciliationRequest $request): JsonResponse
    {
        return $this->response(function (&$response) {
            $data = [
                'empresa' => env('STP_COMPANY'),
                'tipoOrden' => request()->input('order_type'),
                'fechaOperacion' => ModelUtility::nullSafeForString(request()->input('operation_date'))
            ];
            $formData = $data;
            $formData['page'] = 0;
            $formData['firma'] = Utility::sign($data);
            $stpResponse = Http::baseUrl(env('STP_URL'))->post("V2/conciliacion", $formData);
            $stpResponse->failed() && throw new Exception(json_encode($stpResponse->json()), $stpResponse->status());
            $response = $stpResponse->json();
        });
    }
}
