<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'email', 'phone', 'secondary_phone', 'notes',
    ];

    public function treatments()
    {
        return $this->hasMany(Treatment::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}



