<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('receipt_settings', 'theme_color')) {
            Schema::table('receipt_settings', function (Blueprint $table) {
                $table->string('theme_color', 30)->default('red')->after('store_logo');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('receipt_settings', 'theme_color')) {
            Schema::table('receipt_settings', function (Blueprint $table) {
                $table->dropColumn('theme_color');
            });
        }
    }
};
