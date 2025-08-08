<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WhatsAppMessage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_id','lead_id','user_id','phone','direction','message','external_id','status','sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];
}


