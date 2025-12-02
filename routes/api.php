<?php

use App\Http\Controllers\Api\HoldController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Products
|--------------------------------------------------------------------------
*/

Route::prefix('products')->controller(ProductController::class)->group(function () {
    Route::get('/', 'index');
    Route::get('/{id}', 'show')->whereUuid('id');
});

/*
|--------------------------------------------------------------------------
| Holds
|--------------------------------------------------------------------------
*/

Route::prefix('holds')->controller(HoldController::class)->group(function () {
    Route::get('/', 'index');
    Route::get('/{id}', 'show')->whereUuid('id');
    Route::post('/', 'store');
});

/*
|--------------------------------------------------------------------------
| Orders
|--------------------------------------------------------------------------
*/

Route::prefix('orders')->controller(OrderController::class)->group(function () {
    Route::get('/', 'index');
    Route::get('/{id}', 'show')->whereUuid('id');
    Route::post('/', 'store');
    Route::middleware('idempotency')->put('/{id}/payment/webhook', 'pay')->whereUuid('id');
});
