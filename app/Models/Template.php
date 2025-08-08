<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',            // slug identifier, e.g. appointment_confirmation
        'name',           // human friendly name
        'channel',        // whatsapp|email|both
        'whatsapp_template', // provider template name (optional)
        'subject',        // for email (optional)
        'content_text',   // plain text with placeholders
        'content_html',   // optional HTML version
        'variables',      // JSON array of variable keys expected
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'variables' => 'array',
    ];
}


