<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderReceived extends Model
{
    use HasFactory;
    protected $fillable = [
        'request'
    ];
}
