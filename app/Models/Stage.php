<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['pipeline_id','name','slug','display_order'];

    public function pipeline()
    {
        return $this->belongsTo(Pipeline::class);
    }
}


