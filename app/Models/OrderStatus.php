<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderStatus extends Model
{

    use HasFactory;

    protected $fillable = [
        'folio_origin',
        'status',
        'cause_return',
        'ts_liquidation'
    ];

}
