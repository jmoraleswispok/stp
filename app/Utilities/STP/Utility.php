<?php

namespace App\Utilities\STP;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use OpenSSLAsymmetricKey;

class Utility
{

    /**
     * @param Model $model
     * @return string
     */
    public static function generateAccountNumber(Model $model): string
    {
        $clientNumber = str_pad($model->id,7,"0", STR_PAD_LEFT);
        $orderingAccountPrefix = env('ORDERING_ACCOUNT_PREFIX');
        $accountNumber = "{$orderingAccountPrefix}{$clientNumber}";
        $verifiedDigit = (new Utility())->getVerifiedDigit($accountNumber);
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
            $index = $index + 1;
            $value = intval($value);
            return match (true) {
                in_array($index, $first) == true => ($value * 3) % 10,
                in_array($index, $second) == true => ($value * 7) % 10,
                default => ($value) % 10,
            };
        });
        return 10 % (10 - (collect($result)->sum() % 10));
    }

    /**
     * @param array $data
     * @return string
     */
    public static function sign(array $data): string
    {
        $privateKey = (new Utility())->getCertified();
        $binarySign="";
        $originalString = (new Utility())->getOriginalString($data);
        openssl_sign($originalString, $binarySign, $privateKey, "RSA-SHA256");
        return base64_encode($binarySign);
    }

    /**
     * @return OpenSSLAsymmetricKey|bool
     */
    private function getCertified(): OpenSSLAsymmetricKey|bool
    {
        return openssl_get_privatekey(Storage::get(env('STP_PEM_PATH')), env('STP_PEM_PASSWORD'));
    }

    /**
     * @param array $data
     * @return string
     */
    private function getOriginalString(array $data): string
    {
        $originalString = collect(array_values($data))->implode('|');
        return "||{$originalString}||";
    }

}
