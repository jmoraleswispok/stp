<?php

namespace App\Http\Requests\STPTest;

use App\Utilities\SignatureUtility;
use Illuminate\Foundation\Http\FormRequest;

class ConciliationRequest extends FormRequest
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
            'operation_date' => 'sometimes|string|date|date_format:Ymd',
            'order_type' => 'required|string|in:E,R'
        ];
    }

    /**
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'order_type.in' => 'El valor seleccionado para :attribute no es válido. Los valores permitidos son: E y R.',
        ];
    }

}
