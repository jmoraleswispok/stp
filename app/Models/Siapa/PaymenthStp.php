<?php

namespace App\Models\Siapa;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymenthStp extends Model
{
    protected $connection = 'siapa';
    protected $fillable = [
        'stp_id',
        'data',
        'tracking_key',
        'ordering_name',
        'ordering_inst',
        'ordering_account',
        'ordering_rfc',
        'paymenth_concept'
    ];

    public function paymenth()
    {
        return $this->belongsTo(Paymenth::class);
    }

}
