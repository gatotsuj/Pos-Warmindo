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
        Schema::create('receipt_settings', function (Blueprint $table) {
            $table->id();
            $table->string('store_name')->default('POS System');
            $table->string('store_logo')->nullable();
            $table->string('store_address')->nullable();
            $table->string('store_phone')->nullable();
            $table->string('header_line_1')->nullable();
            $table->string('header_line_2')->nullable();
            $table->string('footer_line_1')->nullable();
            $table->string('footer_line_2')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipt_settings');
    }
};

