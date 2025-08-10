<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('lead_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('from_email');
            $table->string('to_email');
            $table->string('subject')->nullable();
            $table->longText('body')->nullable();
            $table->string('message_id')->nullable();
            $table->dateTime('sent_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('from_email');
            $table->index('to_email');
            $table->index('sent_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_messages');
    }
};




