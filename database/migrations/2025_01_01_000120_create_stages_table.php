<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pipeline_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->unsignedInteger('display_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['pipeline_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stages');
    }
};



