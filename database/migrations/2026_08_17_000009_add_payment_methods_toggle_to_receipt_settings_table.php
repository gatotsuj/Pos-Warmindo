<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('receipt_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('receipt_settings', 'is_cash_enabled')) {
                $table->boolean('is_cash_enabled')->default(true)->after('discount_enabled');
            }
            if (!Schema::hasColumn('receipt_settings', 'is_qris_enabled')) {
                $table->boolean('is_qris_enabled')->default(true)->after('is_cash_enabled');
            }
            if (!Schema::hasColumn('receipt_settings', 'is_card_enabled')) {
                $table->boolean('is_card_enabled')->default(true)->after('is_qris_enabled');
            }
        });
    }

    public function down(): void
    {
        Schema::table('receipt_settings', function (Blueprint $table) {
            $table->dropColumn(['is_cash_enabled', 'is_qris_enabled', 'is_card_enabled']);
        });
    }
};
