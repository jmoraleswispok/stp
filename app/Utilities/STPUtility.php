<?php

namespace App\Utilities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class STPUtility
{

    /**
     * @param Model $model
     * @return string
     */
    public static function generateAccountNumber(Model $model): string
    {
        $clientNumber = str_pad($model->id,7,"0", STR_PAD_LEFT);
        $accountNumber = "6463203687{$clientNumber}";
        $verifiedDigit = (new STPUtility())->getVerifiedDigit($accountNumber);
        return "$accountNumber$verifiedDigit";
    }

    public static function generateAccountNumber2($id): string
    {
        $clientNumber = str_pad($id,7,"0", STR_PAD_LEFT);
        $accountNumber = "6461803687{$clientNumber}";
        $verifiedDigit = (new STPUtility())->getVerifiedDigit($accountNumber);
//        strlen($verifiedDigit) > 1 && dd_json([
//            $accountNumber,
//            $verifiedDigit
//        ]);
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
            //$third = array(3,6,9,12,15);
            $index = $index + 1;
            $value = intval($value);
            return match (true) {
                in_array($index, $first) == true => ($value * 3) % 10,
                in_array($index, $second) == true => ($value * 7) % 10,
                default => ($value) % 10,
            };
        });
        //10 % (10 - (collect($result)->sum() % 10))
        //(10 - (collect($result)->sum() % 10) % 10)
        return 10 % (10 - (collect($result)->sum() % 10));
    }

    public static function sign(array $data)
    {
        $privateKey = (new STPUtility())->getCertified();
        $binarySign="";
        $originalString = (new STPUtility())->getOriginalString($data);
        //dd_json($originalString);
        openssl_sign($originalString, $binarySign, $privateKey, "RSA-SHA256");
        $sign = base64_encode($binarySign);
        //openssl_free_key($privateKey);
        return $sign;
    }

    private function getCertified()
    {
        return openssl_get_privatekey(Storage::get('key/privateKeyDev.pem'), env('STP_PEM_PASSWORD'));
    }

    private function getOriginalString(array $data)
    {
        $originalString = collect(array_values($data))->implode('|');
        return "||{$originalString}||";
    }





}
