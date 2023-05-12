<?php

namespace App\Utilities;

use App\Interfaces\HttpCodeInterface;
use Exception;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class PassportUtility implements HttpCodeInterface
{

    /**
     * @param Builder $model
     * @param array $scopes
     * @return array
     * @throws Exception
     */
    public static function token(Builder $model, array $scopes = array()): array
    {
        if (!method_exists($model,'createToken'))
        {
            throw new Exception("Método para generar token no encontrado.", self::NOT_FOUND);
        }
        $tokenResult = $model->createToken(env('APP_NAME'), $scopes);
        $expiresIn = new Carbon($tokenResult->token->expires_at);
        return [
            'token_type' => 'Bearer',
            'expires_in' => $expiresIn->diffInSeconds(Carbon::now()),
            'access_token' => $tokenResult->accessToken,
        ];
    }

}
