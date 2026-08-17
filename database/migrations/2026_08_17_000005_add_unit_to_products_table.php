<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('products', 'unit')) {
            Schema::table('products', function (Blueprint $table) {
                $table->string('unit', 30)->default('pcs')->after('stock');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('products', 'unit')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('unit');
            });
        }
    }
};
