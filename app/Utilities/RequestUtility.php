<?php

namespace App\Utilities;

class RequestUtility
{
    /**
     * @param array $rules
     * @param null $name
     * @return mixed
     * @description Método que obtiene el id mediante el uuid, en caso de que la columna no es requerida y no existe en el request entonces por defecto es null
     */
    public static function getIdUsingUuid(array $rules, $name = null): mixed
    {
        if(empty($name))
        {
            $name = $rules['key'];
        }
        return !$rules['required'] && request()->isNotFilled($name) ? null : $rules['model']::query()->where('uuid', request()->input($name))->first()->id;
    }

}
