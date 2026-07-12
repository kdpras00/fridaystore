<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            // 'cash' | 'xendit'
            $table->string('payment_method')->default('cash')->after('kembalian');
            // 'paid' | 'pending' | 'expired' | 'failed'
            $table->string('payment_status')->default('paid')->after('payment_method');
            $table->string('xendit_invoice_id')->nullable()->after('payment_status');
            $table->string('xendit_invoice_url')->nullable()->after('xendit_invoice_id');
        });
    }

    public function down(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'payment_status', 'xendit_invoice_id', 'xendit_invoice_url']);
        });
    }
};
