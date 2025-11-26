<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->string('payment_type')->nullable()->after('payment_method'); // bank_transfer, qris
            $table->string('bank_code')->nullable()->after('payment_type');
            $table->string('bank_name')->nullable()->after('bank_code');
            $table->string('account_number')->nullable()->after('bank_name');
            $table->text('qr_code_url')->nullable()->after('account_number');
        });
    }

    public function down(): void
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->dropColumn(['payment_type', 'bank_code', 'bank_name', 'account_number', 'qr_code_url']);
        });
    }
};