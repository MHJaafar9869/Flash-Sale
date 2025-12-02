<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'price' => fake()->numberBetween(1000, 10000),
        ];
    }

    public function inStock(?int $stock = null): static
    {
        return $this->state(fn (array $attributes) => [
            'stock' => $stock ?? fake()->numberBetween(1, 200),
        ]);
    }

    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock' => 0,
        ]);
    }
}
