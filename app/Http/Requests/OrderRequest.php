<?php

namespace App\Http\Requests;

use App\Rules\MaxDecimals;
use App\Utilities\SignatureUtility;
use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
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
            'amount' => [
                'required',
                'numeric',
                'min:0.01',
                'max:999999999999.99',
                new MaxDecimals(2)
            ],
            'beneficiary_account' => [
                'required',
                'string',
                'size:18'
            ],
            'counterparty_institution' => [
                'required',
                'string'
            ],
            'operating_institution' => [
                'required',
                'string'
            ],
            'ordering_account_type' => [
                'required',
                'numeric'
            ],
            'ordering_name' => [
                'required',
                'string'
            ],
            'ordering_account' => [
                'required',
                'string',
                'size:18'
            ],
            'ordering_rfc_curp' => [
                'required',
                'string'
            ],
            'beneficiary_account_type' => [
                'required',
                'numeric'
            ],
            'beneficiary_name' => [
                'required',
                'string'
            ],
            'beneficiary_rfc_curp' => [
                'sometimes',
                'nullable',
                'string'
            ],
            'payment_concept' => [
                'required',
                'string'
            ],
        ];
    }
}
