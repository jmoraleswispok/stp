<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderReceived extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'stp_id',
        'approved',
        'retries',
        'request',
        'reason_for_rejection'
    ];

    public function retries()
    {
        return $this->hasMany(OrderReceivedRetry::class);
    }


}
