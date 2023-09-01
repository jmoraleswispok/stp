<?php

namespace App\Models\Siapa;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymenthStp extends Model
{
    protected $fillable = [
        'data'
    ];

    public function paymenth()
    {
        return $this->belongsTo(Paymenth::class);
    }

}
