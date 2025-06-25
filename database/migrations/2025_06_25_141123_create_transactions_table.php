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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('transaction_id')->unique(); 
            $table->string('order_id_midtrans')->unique(); 
            $table->enum('payment_type', ['credit_card', 'bank_transfer', 'echannel', 'gopay', 'shopeepay', 'qris', 'cstore', 'akulaku', 'bca_klikpay', 'bca_klikbca', 'bri_epay', 'cimb_clicks', 'danamon_online', 'other'])->nullable();
            $table->enum('status', ['pending', 'settlement', 'capture', 'deny', 'cancel', 'expire', 'failure'])->default('pending');
            $table->decimal('gross_amount', 15, 2);
            $table->string('currency', 3)->default('IDR');
            $table->timestamp('transaction_time')->nullable();
            $table->timestamp('settlement_time')->nullable();
            $table->json('midtrans_response')->nullable(); 
            $table->string('fraud_status')->nullable();
            $table->string('status_message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
