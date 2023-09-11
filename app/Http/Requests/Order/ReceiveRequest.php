<?php

namespace App\Http\Requests\Order;

use App\Rules\MaxDecimals;
use App\Utilities\SignatureUtility;
use Illuminate\Foundation\Http\FormRequest;

class ReceiveRequest extends FormRequest
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
                'numeric'
            ],
            'fechaOperacion' => [
                'required',
                'numeric',
                'date',
                'date_format:Ymd'
            ],
            'institucionOrdenante' => [
                'required'
            ],
            'institucionBeneficiaria' => [
                'required'
            ],
            'claveRastreo' => [
                'required',
                'string'
            ],
            'monto' => [
                'required',
                'numeric',
                'min:0.01',
                'max:999999999999.99',
                new MaxDecimals(2)
            ],
            'nombreOrdenante' => [
                'sometimes',
                'nullable',
                'string'
            ],
            'tipoCuentaOrdenante' => [
                'sometimes',
                'nullable',
                'numeric'
            ],
            'cuentaOrdenante' => [
                'sometimes',
                'nullable',
                'string'
            ],
            'rfcCurpOrdenante' => [
                'sometimes',
                'nullable',
                'string'
            ],
            'nombreBeneficiario' => [
                'required',
                'string'
            ],
            'tipoCuentaBeneficiario' => [
                'required'
            ],
            'cuentaBeneficiario' => [
                'required',
                'string'
            ],
            'tipoCuentaBeneficiario2' => [
                'sometimes',
                'nullable',
                'numeric'
            ],
            'cuentaBeneficiario2' => [
                'sometimes',
                'nullable',
                'string'
            ],
            'rfcCurpBeneficiario' => [
                'required'
            ],
            'conceptoPago' => [
                'required',
                'string'
            ],
            'referenciaNumerica' => [
                'required'
            ],
            'empresa' => [
                'required',
                'string'
            ],
            'tipoPago' => [
                'required',
                'numeric'
            ],
            'tsLiquidacion' => [
                'required',
                'string'
            ],
            'folioCodi' => [
                'sometimes',
                'nullable',
                'string'
            ],
        ];
    }
}
