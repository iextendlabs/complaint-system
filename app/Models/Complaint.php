<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    protected $fillable = [
        'name',
        'number',
        'email',
        'tracking_id',
        'complaint',
        'isConfidential',
        'declarationAccepted',
        'file',
        'status',
    ];
}
