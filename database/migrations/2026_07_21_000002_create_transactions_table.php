<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->nullable()->constrained('merchants')->onDelete('set null');
            $table->string('client_referenceId')->unique();
            $table->string('order_id')->nullable();
            $table->string('tlid')->nullable();
            $table->string('code');
            $table->string('provider_name');
            $table->decimal('amount', 10, 2);
            $table->string('fname');
            $table->string('lname');
            $table->string('email');
            $table->string('mobile');
            $table->string('gift_message')->nullable();
            $table->string('card_no')->nullable();
            $table->string('pin')->nullable();
            $table->string('card_exp')->nullable();
            $table->enum('status', ['SUCCESS', 'FAILED', 'PENDING'])->default('PENDING');
            $table->text('message')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};
