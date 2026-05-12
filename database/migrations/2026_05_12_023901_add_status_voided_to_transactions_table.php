<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->enum('status', ['completed', 'voided'])->default('completed')->after('notes');
            $table->timestamp('voided_at')->nullable()->after('status');
            $table->foreignId('voided_by')->nullable()->constrained('users')->nullOnDelete()->after('voided_at');
            $table->text('void_reason')->nullable()->after('voided_by');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['voided_by']);
            $table->dropColumn(['status', 'voided_at', 'voided_by', 'void_reason']);
        });
    }
};
