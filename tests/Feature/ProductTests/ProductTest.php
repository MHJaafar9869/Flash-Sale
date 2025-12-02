<?php

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Products', function () {
    it('has index products page', function () {
        $response = $this->get('/api/products');
        $response->assertStatus(200);
    });

    it('has show hold page', function () {
        Product::factory(5)->inStock()->create();

        $response = $this->get('/api/products');

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data');
    });

    it('has show product page', function () {
        $product = Product::factory()->inStock()->create();

        $response = $this->get("/api/products/{$product->id}");

        $response->assertStatus(200);
    });
});
