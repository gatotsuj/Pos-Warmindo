<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        DB::table('tenants')->insert([
            'id' => 1,
            'name' => 'Default Toko',
            'slug' => 'default-toko',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->after('id')->constrained()->nullOnDelete();
        });

        DB::table('users')->update(['tenant_id' => 1]);

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE users MODIFY COLUMN role VARCHAR(32) NOT NULL DEFAULT \'cashier\'');
        }

        Schema::table('categories', function (Blueprint $table) {
            $table->dropUnique(['slug']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->foreignId('tenant_id')->default(1)->after('id')->constrained()->cascadeOnDelete();
            $table->unique(['tenant_id', 'slug']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropUnique(['sku']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('tenant_id')->default(1)->after('id')->constrained()->cascadeOnDelete();
            $table->unique(['tenant_id', 'sku']);
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropUnique(['invoice_number']);
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('tenant_id')->default(1)->after('id')->constrained()->cascadeOnDelete();
            $table->unique(['tenant_id', 'invoice_number']);
        });

        Schema::table('receipt_settings', function (Blueprint $table) {
            $table->foreignId('tenant_id')->default(1)->after('id')->constrained()->cascadeOnDelete();
            $table->unique(['tenant_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receipt_settings', function (Blueprint $table) {
            $table->dropUnique(['tenant_id']);
            $table->dropForeign(['tenant_id']);
            $table->dropColumn('tenant_id');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropUnique(['tenant_id', 'invoice_number']);
            $table->dropForeign(['tenant_id']);
            $table->dropColumn('tenant_id');
            $table->unique('invoice_number');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropUnique(['tenant_id', 'sku']);
            $table->dropForeign(['tenant_id']);
            $table->dropColumn('tenant_id');
            $table->unique('sku');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropUnique(['tenant_id', 'slug']);
            $table->dropForeign(['tenant_id']);
            $table->dropColumn('tenant_id');
            $table->unique('slug');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropColumn('tenant_id');
        });

        Schema::dropIfExists('tenants');
    }
};
