<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('merchants', function (Blueprint $table) {
            $table->id();
            $table->string('client_referenceId')->unique();
            $table->string('name')->nullable();
            $table->string('mobile');
            $table->string('email');
            $table->string('aadhaar_number');
            $table->string('pan');
            $table->string('bank_account');
            $table->string('ifsc');
            $table->string('latitude')->default('28.6139');
            $table->string('longitude')->default('77.2090');
            $table->string('consent')->default('Y');
            $table->enum('status', ['PENDING_OTP', 'VERIFIED', 'REJECTED'])->default('PENDING_OTP');
            $table->string('refid')->nullable();
            $table->text('hash')->nullable();
            $table->decimal('wallet_balance', 12, 2)->default(0.00);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('merchants');
    }
};
