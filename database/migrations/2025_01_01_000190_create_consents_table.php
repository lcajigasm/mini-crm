<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->enum('channel', ['email','sms','whatsapp','call']);
            $table->boolean('granted')->default(true);
            $table->timestamp('granted_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->string('source')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['customer_id','channel']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consents');
    }
};



