<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        $products = [
            [
                'id' => Str::uuid(),
                'name' => 'Laptop',
                'price' => 999.99,
                'stock' => 50,
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Smartphone',
                'price' => 699.99,
                'stock' => 150,
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Tablet',
                'price' => 399.99,
                'stock' => 100,
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Headphones',
                'price' => 199.99,
                'stock' => 200,
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Smartwatch',
                'price' => 249.99,
                'stock' => 80,
            ],
        ];

        data_set($products, '*.created_at', $now);
        data_set($products, '*.updated_at', $now);

        DB::table('products')->upsert(
            $products,
            'name',
            ['name', 'price', 'stock', 'updated_at']
        );
    }
}
