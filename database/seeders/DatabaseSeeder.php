<?php

namespace Database\Seeders;

use App\Models\Hold;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([ProductSeeder::class]);

        // Product::factory(10)->inStock()->create();
        // Product::factory(10)->outOfStock()->create();

        // Hold::factory(10)->notUsed()->create();
        // Hold::factory(10)->expired()->create();
        // Hold::factory(10)->used()->create();

        // Order::factory(10)->paid()->create();
        // Order::factory(10)->failed()->create();
        // Order::factory(10)->cancelled()->create();
    }
}
