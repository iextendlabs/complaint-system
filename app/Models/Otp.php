<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    protected $fillable = [
        'name',
        'email',
        'number',
        'otp',
        'tracking_id',
    ];
}
