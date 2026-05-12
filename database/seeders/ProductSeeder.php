<?php

// database/seeders/ProductSeeder.php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // ── MIE GORENG ──────────────────────────────────────────────
            [
                'category' => 'Makanan',
                'name'     => 'Indomie Goreng Spesial',
                'sku'      => 'MKN-001',
                'price'    => 8000,
                'stock'    => 100,
                'image'    => 'products/indomie-goreng-spesial_big.png',
            ],
            [
                'category' => 'Makanan',
                'name'     => 'Indomie Goreng Spesial Plus',
                'sku'      => 'MKN-002',
                'price'    => 10000,
                'stock'    => 80,
                'image'    => 'products/indomie-goreng-spesial-plus_big.png',
            ],
            [
                'category' => 'Makanan',
                'name'     => 'Indomie Goreng Spesial Jumbo',
                'sku'      => 'MKN-003',
                'price'    => 12000,
                'stock'    => 70,
                'image'    => 'products/indomie-goreng-spesial-jumbo_big.png',
            ],
            [
                'category' => 'Makanan',
                'name'     => 'Indomie Goreng Pedas',
                'sku'      => 'MKN-004',
                'price'    => 8000,
                'stock'    => 90,
                'image'    => 'products/indomie-goreng-pedas_big.png',
            ],
            [
                'category' => 'Makanan',
                'name'     => 'Indomie Goreng Cabe Ijo',
                'sku'      => 'MKN-005',
                'price'    => 9000,
                'stock'    => 75,
                'image'    => 'products/indomie-goreng-cabe-ijo_big.png',
            ],
            [
                'category' => 'Makanan',
                'name'     => 'Indomie Goreng Rendang',
                'sku'      => 'MKN-006',
                'price'    => 9000,
                'stock'    => 80,
                'image'    => 'products/indomie-goreng-rendang_big.png',
            ],
            [
                'category' => 'Makanan',
                'name'     => 'Indomie Goreng Rendang Jumbo',
                'sku'      => 'MKN-007',
                'price'    => 13000,
                'stock'    => 60,
                'image'    => 'products/indomie-goreng-rendang-jumbo_big.png',
            ],
            [
                'category' => 'Makanan',
                'name'     => 'Indomie Goreng Cakalang',
                'sku'      => 'MKN-008',
                'price'    => 10000,
                'stock'    => 55,
                'image'    => 'products/indomie-goreng-cakalang_big.png',
            ],
            [
                'category' => 'Makanan',
                'name'     => 'Indomie Goreng Iga Penyet',
                'sku'      => 'MKN-009',
                'price'    => 10000,
                'stock'    => 50,
                'image'    => 'products/indomie-goreng-iga-penyet_big.png',
            ],
            [
                'category' => 'Makanan',
                'name'     => 'Indomie Goreng Sambal Rica-Rica',
                'sku'      => 'MKN-010',
                'price'    => 9000,
                'stock'    => 65,
                'image'    => 'products/indomie-goreng-sambal-ricarica_big.png',
            ],
            [
                'category' => 'Makanan',
                'name'     => 'Indomie Goreng Kriuk Pedas',
                'sku'      => 'MKN-011',
                'price'    => 10000,
                'stock'    => 50,
                'image'    => 'products/indomie-goreng-kriuk-pedas_big.png',
            ],
            [
                'category' => 'Makanan',
                'name'     => 'Indomie Goreng Krose',
                'sku'      => 'MKN-012',
                'price'    => 10000,
                'stock'    => 45,
                'image'    => 'products/indomie-goreng-krose_big.png',
            ],
            [
                'category' => 'Makanan',
                'name'     => 'Indomie Goreng Fiery Chikin',
                'sku'      => 'MKN-013',
                'price'    => 11000,
                'stock'    => 40,
                'image'    => 'products/indomie-goreng-fiery-chikin_big.png',
            ],
            [
                'category' => 'Makanan',
                'name'     => 'Indomie Goreng Aceh',
                'sku'      => 'MKN-014',
                'price'    => 10000,
                'stock'    => 45,
                'image'    => 'products/indomie-mi-goreng-aceh_big.png',
            ],
            [
                'category' => 'Makanan',
                'name'     => 'Indomie Goreng Jumbo Ayam Panggang',
                'sku'      => 'MKN-015',
                'price'    => 13000,
                'stock'    => 55,
                'image'    => 'products/indomie-goreng-jumbo-rasa-ayam-panggang_big.png',
            ],
            [
                'category' => 'Makanan',
                'name'     => 'Indomie Keriting Goreng Spesial',
                'sku'      => 'MKN-016',
                'price'    => 10000,
                'stock'    => 60,
                'image'    => 'products/indomie-keriting-goreng-rasa-spesial_big.png',
            ],
            [
                'category' => 'Makanan',
                'name'     => 'Indomie Keriting Goreng Ayam Panggang',
                'sku'      => 'MKN-017',
                'price'    => 10000,
                'stock'    => 50,
                'image'    => 'products/indomie-keriting-goreng-rasa-ayam-panggang_big.png',
            ],

            // ── HYPEABIS ─────────────────────────────────────────────────
            [
                'category' => 'Makanan',
                'name'     => 'Indomie Hypeabis Ayam Geprek',
                'sku'      => 'MKN-018',
                'price'    => 11000,
                'stock'    => 40,
                'image'    => 'products/indomie-hypeabis-ayam-geprek_big.png',
            ],
            [
                'category' => 'Makanan',
                'name'     => 'Indomie Hypeabis Seblak Hot Jeletot',
                'sku'      => 'MKN-019',
                'price'    => 11000,
                'stock'    => 35,
                'image'    => 'products/indomie-hypeabis-seblak-hot-jeletot_big.png',
            ],

            // ── MIE KUAH ─────────────────────────────────────────────────
            [
                'category' => 'Makanan',
                'name'     => 'Indomie Rasa Ayam Bawang',
                'sku'      => 'MKN-020',
                'price'    => 8000,
                'stock'    => 100,
                'image'    => 'products/indomie-rasa-ayam-bawang_big.png',
            ],
            [
                'category' => 'Makanan',
                'name'     => 'Indomie Rasa Ayam Spesial',
                'sku'      => 'MKN-021',
                'price'    => 8000,
                'stock'    => 90,
                'image'    => 'products/indomie-rasa-ayam-spesial_big.png',
            ],
            [
                'category' => 'Makanan',
                'name'     => 'Indomie Rasa Kaldu Ayam',
                'sku'      => 'MKN-022',
                'price'    => 8000,
                'stock'    => 80,
                'image'    => 'products/indomie-rasa-kaldu-ayam_big.png',
            ],
            [
                'category' => 'Makanan',
                'name'     => 'Indomie Rasa Kaldu Udang',
                'sku'      => 'MKN-023',
                'price'    => 8000,
                'stock'    => 70,
                'image'    => 'products/indomie-rasa-kaldu-udang_big.png',
            ],
            [
                'category' => 'Makanan',
                'name'     => 'Indomie Rasa Kari Ayam',
                'sku'      => 'MKN-024',
                'price'    => 8000,
                'stock'    => 80,
                'image'    => 'products/indomie-rasa-kari-ayam-dengan-bumbu-kari_big.png',
            ],
            [
                'category' => 'Makanan',
                'name'     => 'Indomie Rasa Kari Ayam Bawang Goreng',
                'sku'      => 'MKN-025',
                'price'    => 9000,
                'stock'    => 70,
                'image'    => 'products/indomie-rasa-kari-ayam-dengan-bawang-goreng_big.png',
            ],
            [
                'category' => 'Makanan',
                'name'     => 'Indomie Rasa Soto Mie',
                'sku'      => 'MKN-026',
                'price'    => 8000,
                'stock'    => 85,
                'image'    => 'products/indomie-rasa-soto-mie_big.png',
            ],
            [
                'category' => 'Makanan',
                'name'     => 'Indomie Rasa Soto Spesial',
                'sku'      => 'MKN-027',
                'price'    => 9000,
                'stock'    => 75,
                'image'    => 'products/indomie-rasa-soto-spesial_big.png',
            ],
            [
                'category' => 'Makanan',
                'name'     => 'Indomie Rasa Soto Lamongan',
                'sku'      => 'MKN-028',
                'price'    => 9000,
                'stock'    => 70,
                'image'    => 'products/indomie-kuah-rasa-soto-lamongan_big.png',
            ],
            [
                'category' => 'Makanan',
                'name'     => 'Indomie Rasa Soto Medan',
                'sku'      => 'MKN-029',
                'price'    => 9000,
                'stock'    => 65,
                'image'    => 'products/indomie-rasa-soto-medan_big.png',
            ],
            [
                'category' => 'Makanan',
                'name'     => 'Indomie Rasa Soto Banjar',
                'sku'      => 'MKN-030',
                'price'    => 9000,
                'stock'    => 60,
                'image'    => 'products/indomie-rasa-soto-banjar_big.png',
            ],
            [
                'category' => 'Makanan',
                'name'     => 'Indomie Rasa Soto Banjar Limau Kuit',
                'sku'      => 'MKN-031',
                'price'    => 9000,
                'stock'    => 55,
                'image'    => 'products/indomie-rasa-soto-banjar-limau-kuit_big.png',
            ],
            [
                'category' => 'Makanan',
                'name'     => 'Indomie Rasa Coto Makassar',
                'sku'      => 'MKN-032',
                'price'    => 9000,
                'stock'    => 60,
                'image'    => 'products/indomie-rasa-coto-makassar_big.png',
            ],
            [
                'category' => 'Makanan',
                'name'     => 'Indomie Rasa Empal Gentong',
                'sku'      => 'MKN-033',
                'price'    => 9000,
                'stock'    => 55,
                'image'    => 'products/indomie-rasa-empal-gentong_big.png',
            ],
            [
                'category' => 'Makanan',
                'name'     => 'Indomie Rasa Mi Cakalang',
                'sku'      => 'MKN-034',
                'price'    => 10000,
                'stock'    => 50,
                'image'    => 'products/indomie-rasa-mi-cakalang_big.png',
            ],
            [
                'category' => 'Makanan',
                'name'     => 'Indomie Rasa Mi Celor',
                'sku'      => 'MKN-035',
                'price'    => 10000,
                'stock'    => 45,
                'image'    => 'products/indomie-rasa-mi-celor_big.png',
            ],
            [
                'category' => 'Makanan',
                'name'     => 'Indomie Rasa Mi Kocok Bandung',
                'sku'      => 'MKN-036',
                'price'    => 10000,
                'stock'    => 45,
                'image'    => 'products/indomie-rasa-mi-kocok-bandung_big.png',
            ],

            // ── RAMEN / FUSION ───────────────────────────────────────────
            [
                'category' => 'Makanan',
                'name'     => 'Indomie Spicy Ramyeon',
                'sku'      => 'MKN-037',
                'price'    => 12000,
                'stock'    => 40,
                'image'    => 'products/indomie-spicy-ramyeon_big.png',
            ],
            [
                'category' => 'Makanan',
                'name'     => 'Indomie Tori Miso Ramen',
                'sku'      => 'MKN-038',
                'price'    => 12000,
                'stock'    => 35,
                'image'    => 'products/indomie-tori-miso-ramen_big.png',
            ],

            // ── MINUMAN ──────────────────────────────────────────────────
            ['category' => 'Minuman', 'name' => 'Es Teh Manis',       'sku' => 'MNM-001', 'price' => 4000,  'stock' => 200, 'image' => 'products/es-teh-manis.png'],
            ['category' => 'Minuman', 'name' => 'Teh Manis Hangat',   'sku' => 'MNM-002', 'price' => 4000,  'stock' => 200, 'image' => 'products/teh-manis-hangat.png'],
            ['category' => 'Minuman', 'name' => 'Kopi Hitam Panas',   'sku' => 'MNM-003', 'price' => 5000,  'stock' => 150, 'image' => 'products/es-teh-manis.png'],
            ['category' => 'Minuman', 'name' => 'Es Kopi Susu',       'sku' => 'MNM-004', 'price' => 8000,  'stock' => 100, 'image' => 'products/es-teh-manis.png'],
            ['category' => 'Minuman', 'name' => 'Susu UHT Coklat',    'sku' => 'MNM-005', 'price' => 6000,  'stock' => 80,  'image' => 'products/susu-cokelat.png'],
            ['category' => 'Minuman', 'name' => 'Aqua 600ml',         'sku' => 'MNM-006', 'price' => 4000,  'stock' => 200, 'image' => 'products/aqua.png'],
            ['category' => 'Minuman', 'name' => 'Teh Botol Sosro',    'sku' => 'MNM-007', 'price' => 5000,  'stock' => 120, 'image' => null],
            ['category' => 'Minuman', 'name' => 'Es Jeruk Peras',     'sku' => 'MNM-008', 'price' => 7000,  'stock' => 80,  'image' => null],
            ['category' => 'Minuman', 'name' => 'Yakult',             'sku' => 'MNM-009', 'price' => 5000,  'stock' => 60,  'image' => null],

            // ── TOPPING ──────────────────────────────────────────────────
            ['category' => 'Topping', 'name' => 'Tambah Telur',   'sku' => 'TOP-001', 'price' => 4000, 'stock' => 200, 'image' => null],
            ['category' => 'Topping', 'name' => 'Tambah Kornet',  'sku' => 'TOP-002', 'price' => 5000, 'stock' => 100, 'image' => null],
            ['category' => 'Topping', 'name' => 'Tambah Keju',    'sku' => 'TOP-003', 'price' => 5000, 'stock' => 80,  'image' => null],
            ['category' => 'Topping', 'name' => 'Tambah Sosis',   'sku' => 'TOP-004', 'price' => 5000, 'stock' => 80,  'image' => null],
            ['category' => 'Topping', 'name' => 'Tambah Nasi',    'sku' => 'TOP-005', 'price' => 5000, 'stock' => 150, 'image' => null],
            ['category' => 'Topping', 'name' => 'Tambah Bakso',   'sku' => 'TOP-006', 'price' => 5000, 'stock' => 60,  'image' => null],
            ['category' => 'Topping', 'name' => 'Tambah Ceker',   'sku' => 'TOP-007', 'price' => 6000, 'stock' => 50,  'image' => null],

            // ── SNACK ─────────────────────────────────────────────────────
            ['category' => 'Snack', 'name' => 'Kerupuk Putih',          'sku' => 'SNK-001', 'price' => 1000,  'stock' => 200, 'image' => null],
            ['category' => 'Snack', 'name' => 'Chitato Sapi Panggang',  'sku' => 'SNK-002', 'price' => 12000, 'stock' => 40,  'image' => null],
            ['category' => 'Snack', 'name' => 'Taro Net',               'sku' => 'SNK-003', 'price' => 3000,  'stock' => 100, 'image' => null],
            ['category' => 'Snack', 'name' => 'Twistko',                'sku' => 'SNK-004', 'price' => 2000,  'stock' => 100, 'image' => null],

            // ── ROKOK ─────────────────────────────────────────────────────
            ['category' => 'Rokok', 'name' => 'Gudang Garam Surya 12', 'sku' => 'RKK-001', 'price' => 28000, 'stock' => 100, 'image' => null],
            ['category' => 'Rokok', 'name' => 'Sampoerna Mild',        'sku' => 'RKK-002', 'price' => 30000, 'stock' => 100, 'image' => null],
            ['category' => 'Rokok', 'name' => 'Djarum Super',          'sku' => 'RKK-003', 'price' => 25000, 'stock' => 80,  'image' => null],
            ['category' => 'Rokok', 'name' => 'LA Bold',               'sku' => 'RKK-004', 'price' => 30000, 'stock' => 60,  'image' => null],
        ];

        foreach ($products as $productData) {
            $category = Category::where('name', $productData['category'])->first();

            if ($category) {
                Product::create([
                    'category_id' => $category->id,
                    'name'        => $productData['name'],
                    'sku'         => $productData['sku'],
                    'price'       => $productData['price'],
                    'stock'       => $productData['stock'],
                    'image'       => $productData['image'] ?? null,
                    'is_active'   => true,
                ]);
            }
        }

        $this->command->info('Products seeded successfully! (' . count($products) . ' products)');
    }
}