<?php

namespace App\Traits\Utilities;

use App\Interfaces\HttpCodeInterface;
use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * @param $message
     * @param int $status
     * @return JsonResponse
     */
    public function successResponse($message, int $status = HttpCodeInterface::OK): JsonResponse
    {
        return $this->responseJson([
            'data' => $message,
            'status' => $status
        ], HttpCodeInterface::OK);
    }

    /**
     * @param $message
     * @param mixed $httCode
     * @param null $status
     * @return JsonResponse
     */
    public function errorResponse($message, mixed $httCode = HttpCodeInterface::INTERNAL_SERVER_ERROR, $status = null): JsonResponse
    {
        $httCode = $this->validHttpCode($httCode);
        $message = $this->validHttpMessage($message);
        return $this->responseJson([
            'error' => $message,
            'status' => empty($status) ? $httCode : $status
        ],$httCode);
    }

    /**
     * @param $code
     * @return int
     */
    private function validHttpCode($code): int
    {
        if(empty($code) || !is_numeric($code) || ($code < 100 ||  $code > 599)) {
            $code = HttpCodeInterface::INTERNAL_SERVER_ERROR;
        }
        return $code;
    }

    /**
     * @param $message
     * @return mixed|string
     */
    private function validHttpMessage($message): mixed
    {
        $message = $this->isJson($message) ? json_decode($message, true) : $message;
        return (empty($message)) ? 'Internal Server Error' : $message;
    }

    /**
     * @param $string
     * @return bool
     */
    private function isJson($string): bool
    {
        return is_string($string) && !empty(json_decode($string, true));
    }

    /**
     * @param $data
     * @param $code
     * @return JsonResponse
     */
    private function responseJson($data, $code): JsonResponse
    {
        return response()->json($data, $code);
    }

}
