<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
        });
    }

    protected $fillable = [
        'id_ef',
        'folio_origin',
        'status',
        'cause_return',
        'ts_liquidation'
    ];

    public function statuses()
    {
        return $this->hasMany(OrderStatus::class);
    }

}
