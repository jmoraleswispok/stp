<?php

namespace App\Models\Siapa;

use App\Models\Wispok\Application\Payment as WispokPayment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Paymenth extends Model
{
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
