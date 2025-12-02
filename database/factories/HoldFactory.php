<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class HoldFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::inRandomOrder()->first()?->id ?? Product::factory(),
            'qty' => fake()->numberBetween(1, 5),
        ];
    }

    public function used(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_used' => true,
        ]);
    }

    public function notUsed(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_used' => false,
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => now()->subSeconds(120),
        ]);
    }

    public function notExpired(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => now()->addSeconds(120),
        ]);
    }
}
