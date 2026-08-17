<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('akuntansi_akun', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->onDelete('cascade');
            $table->string('kode_akun', 20);
            $table->string('nama_akun', 150);
            $table->enum('kategori', [
                'aset_lancar',
                'aset_tetap',
                'kewajiban',
                'ekuitas',
                'pendapatan',
                'hpp',
                'beban_operasional'
            ]);
            $table->enum('posisi_normal', ['debit', 'kredit']);
            $table->boolean('is_system')->default(false);
            $table->foreignId('parent_id')->nullable()->constrained('akuntansi_akun')->onDelete('cascade');
            $table->timestamps();

            $table->index(['tenant_id', 'kode_akun']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('akuntansi_akun');
    }
};
