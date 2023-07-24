<?php

namespace App\Http\Requests\STPTest;

use App\Traits\Utilities\ValidationTrait;
use Illuminate\Foundation\Http\FormRequest;

class OrderReceivedRequest extends FormRequest
{
    use ValidationTrait;
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
            'id' => [
                'required',
                'numeric',
                'digits_between:1,10'
            ],
            'fechaOperacion' => 'required|numeric|date|date_format:Ymd',
            'institucionOrdenante' => 'required|numeric',
            'institucionBeneficiaria' => 'required|numeric',
            'claveRastreo' => 'required|string',
            'monto' => [
                'required',
                'numeric',
                $this->validateDecimals(19,2)
            ],
            //'nombreOrdenante' => 'sometimes|string',
            //'tipoCuentaOrdenante' => 'sometimes|numeric',
            //'cuentaOrdenante' => 'sometimes|string',
            //'rfcCurpOrdenante' => 'sometimes|string',
            'nombreBeneficiario' => 'required|string',
            'tipoCuentaBeneficiario' => 'required|numeric',
            'cuentaBeneficiario' => 'required|string',
            //'tipoCuentaBeneficiario2' => 'sometimes|numeric',
            //'cuentaBeneficiario2' => 'sometimes|string',
            'rfcCurpBeneficiario' => 'required|string',
            'conceptoPago' => 'required|string',
            'referenciaNumerica' => 'required|numeric',
            'empresa' => 'required|string',
            'tipoPago' => 'required|numeric',
            'tsLiquidacion' => 'required|string',
            'folioCodi' => 'sometimes|string',
        ];
    }
}
