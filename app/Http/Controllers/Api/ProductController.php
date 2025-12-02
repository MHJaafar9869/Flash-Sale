<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Repositories\ProductRepository\ProductRepositoryInterface;
use App\Services\ProductService;
use App\Traits\ResponseJson;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    use ResponseJson;

    public function __construct(
        protected ProductRepositoryInterface $productRepo,
        protected ProductService $productService
    ) {}

    // GET /api/products
    public function index(): JsonResponse
    {
        $products = Cache::flexible('products', [30, 60], fn () => $this->productRepo->allWithRelations('holds'));

        return $this->respondWithData(
            ProductResource::collection($products),
            'Products retrieved successfully'
        );
    }

    // GET /api/products/{id}
    public function show(int|string $id): JsonResponse
    {
        $product = $this->productService->getProduct($id);

        if (! $product->isSuccess()) {
            return $this->respondError($product->message, $product->status);
        }

        return $this->respondWithData(
            ProductResource::make($product->data),
            $product->message,
            $product->status
        );
    }
}
