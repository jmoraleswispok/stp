<?php

namespace App\Models\Siapa;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SiapaUserInfo extends Model
{
    protected $connection = 'siapa';
    use HasFactory;

    public function siapaUser(): BelongsTo{
        return $this->belongsTo(SiapaUser::class);
    }

}
