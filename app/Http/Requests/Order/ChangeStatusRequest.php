<?php

namespace App\Http\Requests\Order;

use App\Utilities\SignatureUtility;
use Illuminate\Foundation\Http\FormRequest;

class ChangeStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return SignatureUtility::check($this);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'id' => [
                'required',
                'string'
            ],
            'empresa' => [
                'required',
                'string'
            ],
            'folioOrigen' => [
                'sometimes',
                'nullable',
                'string'
            ],
            'estado' => [
                'required',
                'string'
            ],
            'causaDevolucion' => [
                'sometimes',
                'nullable',
                'string'
            ],
            'tsLiquidacion' => [
                'required',
                'string'
            ],
        ];
    }
}
