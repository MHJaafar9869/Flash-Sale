<?php

use App\Models\Hold;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Holds', function () {
    it('has hold index page', function () {
        $response = $this->get('/api/holds');
        $response->assertStatus(200);
    });

    it('has hold show page', function () {
        Product::factory()->inStock()->create();
        Hold::factory(5)->notExpired()->notUsed()->create();

        $response = $this->get('/api/holds');
        $response->assertStatus(200)
            ->assertJsonCount(5, 'data');
    });

    it('can create hold', function () {
        $product = Product::factory()->inStock(50)->create();
        $response = $this->post('/api/holds', [
            'product_id' => $product->id,
            'qty' => 50,
        ]);
        $hold = Hold::first();
        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'status_code' => 201,
                'message' => 'Hold created successfully',
                'data' => [
                    'id' => $hold->id,
                    'qty' => $hold->qty,
                    'is_used' => null,
                    'expires_at' => $hold->expires_at?->toDayDateTimeString(),
                    'created_at' => $hold->created_at->toISOString(),
                    'updated_at' => $hold->updated_at->toISOString(),
                ],
            ]);
    });
});
