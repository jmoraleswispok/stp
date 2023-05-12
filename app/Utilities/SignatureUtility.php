<?php

namespace App\Utilities;

use App\Interfaces\HttpCodeInterface;
use Exception;
use Illuminate\Support\Facades\Storage;

class SignatureUtility
{

    public static function check(&$request): bool
    {
        return true;
        try
        {
            $signed = base64_decode(request()->input('signed'));
            $request->request->remove('signed');
            $privateKey = openssl_pkey_get_private(Storage::get('key/signature-private.key'));
            openssl_private_decrypt($signed, $decrypted, $privateKey);
            $decrypted = json_decode($decrypted);

            $password = hex2bin($decrypted->password);
            $iv = hex2bin($decrypted->iv);

            $decryptedContent = openssl_decrypt(base64_decode($decrypted->content), 'AES-256-CBC', $password, OPENSSL_RAW_DATA, $iv);

            if (empty($decryptedContent))
            {
                throw new Exception("", HttpCodeInterface::FORBIDDEN);
            }
            $data = json_decode($decryptedContent,true);
            $request->request->replace($data);
            return is_array($data);
        }catch (Exception $e) {
            return false;
        }
    }

}
