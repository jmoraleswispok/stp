<?php

namespace App\Http\Requests\STPTest;

use Illuminate\Foundation\Http\FormRequest;

class OrderStatusChangesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'id' => "required",
            'empresa' => "required",
            'folioOrigen' => "sometime",
            'estado' => "required",
            //'causaDevolucion' => "required",
            'tsLiquidacion' => "required",
        ];
    }
}
