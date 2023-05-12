<?php
namespace App\Traits\Utilities;

use Illuminate\Support\Str;

trait GenerateUuid
{
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
        });
    }
}
