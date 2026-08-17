<?php

namespace App\Services;

use App\Models\Akuntansi\Akun;
use App\Models\Akuntansi\Jurnal;
use App\Models\Akuntansi\JurnalDetail;
use App\Models\Akuntansi\Pengeluaran;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class AkuntansiService
{
    /**
     * Pastikan akun default SAK ada untuk tenant ini
     */
    public static function pastikanAkunDefault($tenantId)
    {
        \Database\Seeders\BaganAkunSeeder::seedForTenant($tenantId);
    }

    /**
     * Dapatkan Akun berdasarkan Kode
     */
    public static function getAkunByKode($tenantId, string $kodeAkun)
    {
        self::pastikanAkunDefault($tenantId);
        return Akun::where('tenant_id', $tenantId)
            ->where('kode_akun', $kodeAkun)
            ->first();
    }

    /**
     * Generate Nomor Jurnal Unik (JRN-YYYYMMDD-XXXX)
     */
    public static function generateNomorJurnal($tenantId)
    {
        $todayStr = now()->format('Ymd');
        $countToday = Jurnal::where('tenant_id', $tenantId)
            ->whereDate('tanggal', now()->toDateString())
            ->count() + 1;

        return 'JRN-' . $todayStr . '-' . str_pad($countToday, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Catat Otomatis Jurnal Penjualan dari Transaksi POS Kasir
     */
    public function catatJurnalPos(Transaction $transaction)
    {
        $tenantId = $transaction->tenant_id;
        self::pastikanAkunDefault($tenantId);

        return DB::transaction(function () use ($transaction, $tenantId) {
            $nomorJurnal = self::generateNomorJurnal($tenantId);

            $jurnal = Jurnal::create([
                'tenant_id' => $tenantId,
                'nomor_jurnal' => $nomorJurnal,
                'tanggal' => $transaction->created_at ? $transaction->created_at->toDateString() : now()->toDateString(),
                'sumber_transaksi' => 'pos',
                'referensi_type' => Transaction::class,
                'referensi_id' => $transaction->id,
                'keterangan' => 'Penjualan POS Invoice: ' . $transaction->invoice_number,
                'user_id' => $transaction->user_id,
            ]);

            // 1. Tentukan Akun Kas vs Bank/QRIS
            $kodeKasBank = ($transaction->payment_method === 'cash') ? '1-1001' : '1-1002';
            $akunKasBank = self::getAkunByKode($tenantId, $kodeKasBank);
            $akunPendapatan = self::getAkunByKode($tenantId, '4-1001');

            // Debit Kas/Bank
            JurnalDetail::create([
                'jurnal_id' => $jurnal->id,
                'akun_id' => $akunKasBank->id,
                'jenis' => 'debit',
                'jumlah' => $transaction->grand_total,
                'catatan' => 'Penerimaan Penjualan Invoice ' . $transaction->invoice_number,
            ]);

            // Kredit Pendapatan Penjualan
            JurnalDetail::create([
                'jurnal_id' => $jurnal->id,
                'akun_id' => $akunPendapatan->id,
                'jenis' => 'kredit',
                'jumlah' => $transaction->grand_total,
                'catatan' => 'Pendapatan Penjualan Invoice ' . $transaction->invoice_number,
            ]);

            // 2. Pencatatan HPP & Persediaan
            // Estimasi HPP (misal total cost dari item, atau 60% jika belum diset)
            $totalHpp = 0;
            foreach ($transaction->items as $item) {
                $costPrice = ($item->product && $item->product->cost_price > 0) ? $item->product->cost_price : ($item->unit_price * 0.6);
                $totalHpp += ($costPrice * $item->quantity);
            }

            if ($totalHpp > 0) {
                $akunHpp = self::getAkunByKode($tenantId, '5-1001');
                $akunPersediaan = self::getAkunByKode($tenantId, '1-1003');

                // Debit HPP
                JurnalDetail::create([
                    'jurnal_id' => $jurnal->id,
                    'akun_id' => $akunHpp->id,
                    'jenis' => 'debit',
                    'jumlah' => $totalHpp,
                    'catatan' => 'HPP Penjualan Invoice ' . $transaction->invoice_number,
                ]);

                // Kredit Persediaan
                JurnalDetail::create([
                    'jurnal_id' => $jurnal->id,
                    'akun_id' => $akunPersediaan->id,
                    'jenis' => 'kredit',
                    'jumlah' => $totalHpp,
                    'catatan' => 'Pengurangan Persediaan Invoice ' . $transaction->invoice_number,
                ]);
            }

            return $jurnal;
        });
    }

    /**
     * Catat Otomatis Jurnal Reversal / Void Transaksi POS
     */
    public function catatJurnalVoid(Transaction $transaction)
    {
        $tenantId = $transaction->tenant_id;
        self::pastikanAkunDefault($tenantId);

        return DB::transaction(function () use ($transaction, $tenantId) {
            $nomorJurnal = self::generateNomorJurnal($tenantId);

            $jurnal = Jurnal::create([
                'tenant_id' => $tenantId,
                'nomor_jurnal' => $nomorJurnal,
                'tanggal' => now()->toDateString(),
                'sumber_transaksi' => 'void',
                'referensi_type' => Transaction::class,
                'referensi_id' => $transaction->id,
                'keterangan' => 'Pembatalan (Void) Invoice: ' . $transaction->invoice_number . ' - Alasan: ' . $transaction->void_reason,
                'user_id' => auth()->id(),
            ]);

            $kodeKasBank = ($transaction->payment_method === 'cash') ? '1-1001' : '1-1002';
            $akunKasBank = self::getAkunByKode($tenantId, $kodeKasBank);
            $akunPendapatan = self::getAkunByKode($tenantId, '4-1001');

            // Debit Pendapatan Penjualan (Pengurangan)
            JurnalDetail::create([
                'jurnal_id' => $jurnal->id,
                'akun_id' => $akunPendapatan->id,
                'jenis' => 'debit',
                'jumlah' => $transaction->grand_total,
                'catatan' => 'Pembatalan Pendapatan Invoice ' . $transaction->invoice_number,
            ]);

            // Kredit Kas/Bank (Pengembalian Uang)
            JurnalDetail::create([
                'jurnal_id' => $jurnal->id,
                'akun_id' => $akunKasBank->id,
                'jenis' => 'kredit',
                'jumlah' => $transaction->grand_total,
                'catatan' => 'Pengembalian Uang Void Invoice ' . $transaction->invoice_number,
            ]);

            return $jurnal;
        });
    }

    /**
     * Catat Jurnal Pengeluaran Operasional (Petty Cash)
     */
    public function catatJurnalPengeluaran(Pengeluaran $pengeluaran)
    {
        $tenantId = $pengeluaran->tenant_id;
        self::pastikanAkunDefault($tenantId);

        return DB::transaction(function () use ($pengeluaran, $tenantId) {
            $nomorJurnal = self::generateNomorJurnal($tenantId);

            $jurnal = Jurnal::create([
                'tenant_id' => $tenantId,
                'nomor_jurnal' => $nomorJurnal,
                'tanggal' => $pengeluaran->tanggal->toDateString(),
                'sumber_transaksi' => 'pengeluaran',
                'referensi_type' => Pengeluaran::class,
                'referensi_id' => $pengeluaran->id,
                'keterangan' => 'Pengeluaran Kas: ' . $pengeluaran->keterangan,
                'user_id' => $pengeluaran->user_id,
            ]);

            // Debit Beban
            JurnalDetail::create([
                'jurnal_id' => $jurnal->id,
                'akun_id' => $pengeluaran->akun_beban_id,
                'jenis' => 'debit',
                'jumlah' => $pengeluaran->jumlah,
                'catatan' => $pengeluaran->keterangan,
            ]);

            // Kredit Kas/Bank
            JurnalDetail::create([
                'jurnal_id' => $jurnal->id,
                'akun_id' => $pengeluaran->akun_kas_id,
                'jenis' => 'kredit',
                'jumlah' => $pengeluaran->jumlah,
                'catatan' => 'Pembayaran Kas/Bank untuk ' . $pengeluaran->keterangan,
            ]);

            $pengeluaran->update(['jurnal_id' => $jurnal->id]);

            return $jurnal;
        });
    }
}
