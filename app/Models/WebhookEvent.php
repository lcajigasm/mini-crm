<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WebhookEvent extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'provider','event_type','payload','received_at','processed_at','status','error_message',
    ];

    protected $casts = [
        'payload' => 'array',
        'received_at' => 'datetime',
        'processed_at' => 'datetime',
    ];
}




