<?php

namespace Database\Seeders;

use App\Models\Akuntansi\Akun;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class BaganAkunSeeder extends Seeder
{
    public static function seedForTenant($tenantId)
    {
        $defaultAccounts = [
            // 1. Aset
            ['kode_akun' => '1-1001', 'nama_akun' => 'Kas Kasir', 'kategori' => 'aset_lancar', 'posisi_normal' => 'debit', 'is_system' => true],
            ['kode_akun' => '1-1002', 'nama_akun' => 'Bank / QRIS Settlement', 'kategori' => 'aset_lancar', 'posisi_normal' => 'debit', 'is_system' => true],
            ['kode_akun' => '1-1003', 'nama_akun' => 'Persediaan Bahan & Barang Dagang', 'kategori' => 'aset_lancar', 'posisi_normal' => 'debit', 'is_system' => true],
            ['kode_akun' => '1-2001', 'nama_akun' => 'Peralatan & Inventaris Toko', 'kategori' => 'aset_tetap', 'posisi_normal' => 'debit', 'is_system' => true],
            ['kode_akun' => '1-2002', 'nama_akun' => 'Akumulasi Penyusutan Peralatan', 'kategori' => 'aset_tetap', 'posisi_normal' => 'kredit', 'is_system' => true],

            // 2. Kewajiban
            ['kode_akun' => '2-1001', 'nama_akun' => 'Utang Usaha / Dagang', 'kategori' => 'kewajiban', 'posisi_normal' => 'kredit', 'is_system' => true],
            ['kode_akun' => '2-1002', 'nama_akun' => 'Utang Pajak (PPN/PB1)', 'kategori' => 'kewajiban', 'posisi_normal' => 'kredit', 'is_system' => true],

            // 3. Ekuitas
            ['kode_akun' => '3-1001', 'nama_akun' => 'Modal Pemilik', 'kategori' => 'ekuitas', 'posisi_normal' => 'kredit', 'is_system' => true],
            ['kode_akun' => '3-1002', 'nama_akun' => 'Laba Ditahan', 'kategori' => 'ekuitas', 'posisi_normal' => 'kredit', 'is_system' => true],

            // 4. Pendapatan
            ['kode_akun' => '4-1001', 'nama_akun' => 'Pendapatan Penjualan Makanan & Minuman', 'kategori' => 'pendapatan', 'posisi_normal' => 'kredit', 'is_system' => true],
            ['kode_akun' => '4-1002', 'nama_akun' => 'Pendapatan Lain-lain', 'kategori' => 'pendapatan', 'posisi_normal' => 'kredit', 'is_system' => false],

            // 5. HPP
            ['kode_akun' => '5-1001', 'nama_akun' => 'Beban Pokok Penjualan (HPP)', 'kategori' => 'hpp', 'posisi_normal' => 'debit', 'is_system' => true],

            // 6. Beban Operasional
            ['kode_akun' => '6-1001', 'nama_akun' => 'Beban Gaji & Upah Karyawan', 'kategori' => 'beban_operasional', 'posisi_normal' => 'debit', 'is_system' => false],
            ['kode_akun' => '6-1002', 'nama_akun' => 'Beban Sewa Tempat / Kios', 'kategori' => 'beban_operasional', 'posisi_normal' => 'debit', 'is_system' => false],
            ['kode_akun' => '6-1003', 'nama_akun' => 'Beban Listrik, Air & Internet', 'kategori' => 'beban_operasional', 'posisi_normal' => 'debit', 'is_system' => false],
            ['kode_akun' => '6-1004', 'nama_akun' => 'Beban Perlengkapan & Es/Bahan Operasional', 'kategori' => 'beban_operasional', 'posisi_normal' => 'debit', 'is_system' => false],
            ['kode_akun' => '6-1005', 'nama_akun' => 'Beban Penyusutan Peralatan', 'kategori' => 'beban_operasional', 'posisi_normal' => 'debit', 'is_system' => false],
            ['kode_akun' => '6-1006', 'nama_akun' => 'Beban Selisih Stok & Void', 'kategori' => 'beban_operasional', 'posisi_normal' => 'debit', 'is_system' => true],
        ];

        foreach ($defaultAccounts as $acc) {
            Akun::firstOrCreate(
                [
                    'tenant_id' => $tenantId,
                    'kode_akun' => $acc['kode_akun'],
                ],
                [
                    'nama_akun' => $acc['nama_akun'],
                    'kategori' => $acc['kategori'],
                    'posisi_normal' => $acc['posisi_normal'],
                    'is_system' => $acc['is_system'],
                ]
            );
        }
    }

    public function run(): void
    {
        $tenants = Tenant::all();
        foreach ($tenants as $tenant) {
            self::seedForTenant($tenant->id);
        }
    }
}
