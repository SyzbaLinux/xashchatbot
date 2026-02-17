<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('otps', function (Blueprint $table) {
            $table->id();
            $table->string('phone_number')->nullable()->index();
            $table->string('email')->nullable()->index();
            $table->string('otp_code');
            $table->string('type')->default('login'); // login, registration, password_reset, 2fa
            $table->enum('method', ['whatsapp', 'sms', 'email'])->default('whatsapp');
            $table->boolean('verified')->default(false);
            $table->string('ip_address')->nullable();
            $table->integer('attempts')->default(0);
            $table->timestamp('expires_at');
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index(['phone_number', 'type']);
            $table->index(['email', 'type']);
            $table->index('verified_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otps');
    }
};
