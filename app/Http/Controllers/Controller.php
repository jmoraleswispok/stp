<?php

namespace App\Http\Controllers;

use Exception;
use App\Interfaces\HttpCodeInterface;
use App\Traits\Utilities\ApiResponse;
use App\Traits\Utilities\GeneralUtilities;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController implements HttpCodeInterface
{
    use AuthorizesRequests, ValidatesRequests, GeneralUtilities, ApiResponse;

    /**
     * @param $callback
     * @param bool $custom
     * @return JsonResponse
     */
    protected function response($callback, bool $custom  = false): JsonResponse
    {
        try {
            $response = array();
            $callback($response);
            if ($custom) {
                return response()->json($response);
            }
            $response['message'] = "Acción realizada con éxito.";
            return $this->successResponse($response);
        } catch (Exception $e) {
            $message = json_decode($e->getMessage()) ? json_decode($e->getMessage()) : $e->getMessage();
            return $this->errorResponse($message, $e->getCode());
        }
    }
}
