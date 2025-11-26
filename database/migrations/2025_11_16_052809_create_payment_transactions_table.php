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
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pendaftar_id')->constrained('pendaftar')->onDelete('cascade');
            $table->string('order_id')->unique();
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('IDR');
            $table->string('payment_method');
            $table->enum('status', ['pending', 'paid', 'failed', 'cancelled', 'refunded', 'expired'])->default('pending');
            $table->string('gateway'); // midtrans, xendit, manual
            $table->string('snap_token')->nullable();
            $table->json('payment_data')->nullable();
            $table->json('gateway_response')->nullable();
            $table->decimal('refund_amount', 12, 2)->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            
            $table->index(['pendaftar_id', 'status']);
            $table->index(['order_id']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
