<?php

namespace App\Utilities;

use Illuminate\Database\Eloquent\Model;

class STPUtility
{
    /**
     * @param Model $model
     * @return string
     */
    public function generateAccountNumber(Model $model): string
    {
        $clientNumber = str_pad($model->id,7,"0", STR_PAD_LEFT);
        $accountNumber = "{$clientNumber}";
        $verifiedDigit = $this->getVerifiedDigit($accountNumber);
        return "$accountNumber$verifiedDigit";
    }

    /**
     * @param $accountNumber
     * @return int
     */
    private function getVerifiedDigit($accountNumber): int
    {
        $result = collect(str_split($accountNumber))->map(callback: function ($value, $index) {
            $first = array(1,4,7,10,13,16);
            $second = array(2,5,8,11,14,17);
            $third = array(3,6,9,12,15);
            $index = $index + 1;
            $value = intval($value);
            return match (true) {
                in_array($index, $first) == true => ($value * 3) % 10,
                in_array($index, $second) == true => ($value * 7) % 10,
                in_array($index, $third) == true => ($value) % 10,
                default => 0,
            };
        });
        return (10 - (collect($result)->sum() % 10) % 10);
    }
}
