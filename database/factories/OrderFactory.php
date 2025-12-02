<?php

namespace Database\Factories;

use App\Enums\OrderStatusEnum;
use App\Models\Hold;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => strtoupper(Str::random(6)),
            'total' => fake()->randomFloat(2, 10, 1000),
            'hold_id' => Hold::inRandomOrder()->first()?->id ?? Hold::factory(),
        ];
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderStatusEnum::PAID->value,
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderStatusEnum::FAILED->value,
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderStatusEnum::CANCELLED->value,
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderStatusEnum::PENDING->value,
        ]);
    }
}
