<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\HoldDto;
use App\DTOs\ResponseDto;
use App\Jobs\ReleaseHoldJob;
use App\Models\Product;
use App\Repositories\HoldRepository\HoldRepositoryInterface;
use App\Repositories\ProductRepository\ProductRepositoryInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class HoldService
{
    public function __construct(
        protected HoldRepositoryInterface $holdRepo,
        protected ProductRepositoryInterface $productRepo
    ) {}

    public function getHold(int|string $id): ResponseDto
    {
        $hold = Cache::flexible("holds:{$id}", [30, 60], fn () => $this->holdRepo->findWithRelations($id, ['product', 'order']));

        if (! $hold) {
            return ResponseDto::error()
                ->setMessage('Hold not found')
                ->setStatus(404);
        }

        return ResponseDto::success()
            ->setData($hold)
            ->setMessage('Hold retrieved successfully')
            ->setStatus(200);
    }

    public function createHold(HoldDto $holdDto): ResponseDto
    {
        return DB::transaction(function () use ($holdDto) {
            /** @var Product $product */
            $product = $this->productRepo
                ->addQuery()
                ->lockForUpdate()
                ->find($holdDto->productId);

            if (! $product) {
                return ResponseDto::error()
                    ->setMessage('Product not found')
                    ->setStatus(404);
            }

            if ($holdDto->qty > $product->stock) {
                return ResponseDto::error()
                    ->setMessage('Insufficient stock')
                    ->setStatus(400);
            }

            $product->decrement('stock', $holdDto->qty);
            $hold = $this->holdRepo->holdProduct($holdDto->productId, $holdDto->qty);

            DB::afterCommit(function () use ($product, $hold) {
                $product->refresh();
                Cache::put("products:{$product->id}", $product, 30);
                Cache::put("holds:{$hold->id}", $hold, 30);
                ReleaseHoldJob::dispatch($hold->id)->delay((int) now()->diffInSeconds($hold->expires_at, true));
            });

            return ResponseDto::success()
                ->setData($hold)
                ->setMessage('Hold created successfully')
                ->setStatus(201);
        });
    }
}
