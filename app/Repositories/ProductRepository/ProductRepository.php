<?php

declare(strict_types=1);

namespace App\Repositories\ProductRepository;

use App\Models\Product;
use App\Repositories\BaseRepository\BaseRepository;

final readonly class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    public function __construct(Product $product)
    {
        parent::__construct($product);
    }
}
