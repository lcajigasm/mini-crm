<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_id',
        'lead_id',
        'treatment_id',
        'scheduled_at',
        'duration_minutes',
        'status',
        'session_number',
        'location',
        'notes',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    public function getEndsAtAttribute()
    {
        return $this->scheduled_at?->clone()->addMinutes($this->duration_minutes ?? 30);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function treatment()
    {
        return $this->belongsTo(Treatment::class);
    }
}


