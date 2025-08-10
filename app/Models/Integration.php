<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Integration extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'provider','settings','enabled','connected_at',
    ];

    protected $casts = [
        'settings' => 'array',
        'enabled' => 'boolean',
        'connected_at' => 'datetime',
    ];
}




