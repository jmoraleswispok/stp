<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\ChangeStatusRequest;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ChangeStatusController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(ChangeStatusRequest $request): JsonResponse
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
}
