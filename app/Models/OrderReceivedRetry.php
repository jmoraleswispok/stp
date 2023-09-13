<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderReceivedRetry extends Model
{
    use HasFactory;

    protected $fillable = [
        'request',
        'reason_for_rejection'
    ];
}
