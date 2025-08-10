<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('integrations', function (Blueprint $table) {
            $table->id();
            $table->enum('provider', ['hubspot','telephony','whatsapp','email']);
            $table->json('settings')->nullable();
            $table->boolean('enabled')->default(false);
            $table->timestamp('connected_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['provider']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('integrations');
    }
};



