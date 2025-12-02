<?php

use App\Models\Hold;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

describe('Orders', function () {
    it('has order index page', function () {
        Product::factory()->inStock()->create();
        Hold::factory(5)->notExpired()->notUsed()->create();
        Order::factory()->create();

        $response = $this->get('/api/orders');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    });

    it('has order show page', function () {
        Product::factory()->inStock()->create();
        Hold::factory(5)->notExpired()->notUsed()->create();
        $order = Order::factory()->pending()->create();

        $response = $this->get("/api/orders/{$order->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'status_code' => 200,
                'message' => 'Order retrieved successfully',
                'data' => [
                    'id' => $order->id,
                    'code' => $order->code,
                    'total' => $order->total,
                    'status' => $order->status->label(),
                    'created_at' => $order->created_at->toISOString(),
                    'updated_at' => $order->updated_at->toISOString(),
                    'hold' => [
                        'id' => $order->hold->id,
                        'qty' => $order->hold->qty,
                        'is_used' => $order->hold->is_used,
                        'expires_at' => $order->hold->expires_at->toDayDateTimeString(),
                        'created_at' => $order->hold->created_at->toISOString(),
                        'updated_at' => $order->hold->updated_at->toISOString(),
                    ],
                ],
            ]);
    });

    it('can create order', function () {
        Product::factory()->inStock(200)->create();
        $hold = Hold::factory()->notExpired()->notUsed()->create();

        $response = $this->post('/api/orders', [
            'hold_id' => $hold->id,
        ]);

        $response->assertStatus(201);
    });

    it('can pay order', function () {
        Product::factory()->inStock(200)->create();
        Hold::factory()->notExpired()->notUsed()->create();

        $order = Order::factory()->pending()->create();

        $response = $this->put("/api/orders/{$order->id}/payment/webhook", [
            'status' => 'paid',
        ], ['Idempotency-Key' => Str::uuid()]);

        $response->assertStatus(200);
    });
});
