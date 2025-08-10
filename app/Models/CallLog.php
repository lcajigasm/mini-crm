<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CallLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_id','lead_id','user_id','phone','direction','status','duration_seconds','started_at','notes',
    ];

    protected $casts = [
        'started_at' => 'datetime',
    ];
}




