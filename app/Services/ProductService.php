<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\ResponseDto;
use App\Repositories\ProductRepository\ProductRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class ProductService
{
    public function __construct(
        protected ProductRepositoryInterface $productRepo
    ) {}

    public function getProduct(int|string $id): ResponseDto
    {
        $product = Cache::flexible("products:{$id}", [30, 60], fn () => $this->productRepo->findWithRelations($id, 'holds'));

        if (! $product) {
            return ResponseDto::error()
                ->setMessage('Product not found')
                ->setStatus(404);
        }

        return ResponseDto::success()
            ->setData($product)
            ->setMessage('Product retrieved successfully')
            ->setStatus(200);
    }
}
