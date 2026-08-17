<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('akuntansi_jurnal_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jurnal_id')->constrained('akuntansi_jurnal')->onDelete('cascade');
            $table->foreignId('akun_id')->constrained('akuntansi_akun')->onDelete('cascade');
            $table->enum('jenis', ['debit', 'kredit']);
            $table->decimal('jumlah', 15, 2);
            $table->string('catatan')->nullable();
            $table->timestamps();

            $table->index(['jurnal_id', 'akun_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('akuntansi_jurnal_detail');
    }
};
