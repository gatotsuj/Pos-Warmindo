<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('akuntansi_pengeluaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->onDelete('cascade');
            $table->string('nomor_pengeluaran', 50)->unique();
            $table->date('tanggal');
            $table->foreignId('akun_beban_id')->constrained('akuntansi_akun')->onDelete('cascade');
            $table->foreignId('akun_kas_id')->constrained('akuntansi_akun')->onDelete('cascade');
            $table->decimal('jumlah', 15, 2);
            $table->text('keterangan')->nullable();
            $table->string('bukti_foto')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('jurnal_id')->nullable()->constrained('akuntansi_jurnal')->onDelete('set null');
            $table->timestamps();

            $table->index(['tenant_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('akuntansi_pengeluaran');
    }
};
