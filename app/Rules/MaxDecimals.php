<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class MaxDecimals implements ValidationRule
{

    private $maxDecimals;

    public function __construct($maxDecimals)
    {
        $this->maxDecimals = $maxDecimals;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $pattern = '/^\d+(\.\d+)?$/';
        $decimals = strlen(substr(strrchr($value, "."), 1));
        if (!preg_match($pattern, $value))
        {
            $fail("El campo $attribute debe ser un número decimal válido.");
        } else if ($decimals > $this->maxDecimals) {
            $fail("El campo $attribute no puede tener más de {$this->maxDecimals} decimales.");
        }
    }
}
