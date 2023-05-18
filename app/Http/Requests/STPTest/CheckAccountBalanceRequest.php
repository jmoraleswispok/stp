<?php

namespace App\Http\Requests\STPTest;

use App\Utilities\SignatureUtility;
use Illuminate\Foundation\Http\FormRequest;

class CheckAccountBalanceRequest extends FormRequest
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
            'date' => 'sometimes|string|date|date_format:Ymd'
        ];
    }
}
