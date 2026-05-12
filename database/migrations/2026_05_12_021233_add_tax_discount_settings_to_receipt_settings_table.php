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
        Schema::table('receipt_settings', function (Blueprint $table) {
            $table->decimal('tax_percent', 5, 2)->default(11)->after('footer_line_2');
            $table->boolean('tax_enabled')->default(true)->after('tax_percent');
            $table->boolean('discount_enabled')->default(true)->after('tax_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receipt_settings', function (Blueprint $table) {
            $table->dropColumn(['tax_percent', 'tax_enabled', 'discount_enabled']);
        });
    }
};
