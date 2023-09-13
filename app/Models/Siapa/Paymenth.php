<?php

namespace App\Models\Siapa;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Paymenth extends Model
{
    protected $connection = 'siapa';
    use HasFactory;

    const PAID = 2;
    const CODI = 1;
    const ZAPPY = 2;

    protected $fillable = [
        'paymenth_at',
        'status'
    ];

    public function siapaUserInfo(): BelongsTo
    {
        return $this->belongsTo(SiapaUserInfo::class);
    }

    public function stp()
    {
        return $this->hasOne(PaymenthStp::class);
    }

}
