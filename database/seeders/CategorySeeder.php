<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $categories = [
            [
                'name' => 'Makanan',
                'slug' => 'makanan',
                'description' => 'Berbagai jenis makanan siap saji dan kemasan',
            ],
            [
                'name' => 'Minuman',
                'slug' => 'minuman',
                'description' => 'Minuman dingin, hangat, dan kemasan',
            ],
            [
                'name' => 'Snack',
                'slug' => 'snack',
                'description' => 'Makanan ringan dan cemilan',
            ],
            [
                'name' => 'Rokok',
                'slug' => 'rokok',
                'description' => 'Berbagai merek rokok',
            ],
            [
                'name' => 'Toping',
                'slug' => 'toping',
                'description' => 'Toping untuk makanan',
            ],
        ];

        foreach ($categories as $category) {
            Category::create(array_merge($category, ['tenant_id' => 1]));
        }

        $this->command->info('Categories seeded successfully! ('.count($categories).' categories)');
    }
}
