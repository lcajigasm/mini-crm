<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailMessage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_id','lead_id','user_id','from_email','to_email','subject','body','message_id','sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];
}


