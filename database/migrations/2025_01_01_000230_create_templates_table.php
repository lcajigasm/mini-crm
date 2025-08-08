<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('templates', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('name');
            $table->enum('channel', ['whatsapp','email','both'])->default('both');
            $table->string('whatsapp_template')->nullable();
            $table->string('subject')->nullable();
            $table->text('content_text');
            $table->longText('content_html')->nullable();
            $table->json('variables')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->index('channel');
            $table->index('active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('templates');
    }
};


