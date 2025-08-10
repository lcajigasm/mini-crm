<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Consent extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_id','channel','granted','granted_at','revoked_at','source',
    ];

    protected $casts = [
        'granted' => 'boolean',
        'granted_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];
}




