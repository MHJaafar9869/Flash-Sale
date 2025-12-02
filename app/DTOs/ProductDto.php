<?php

declare(strict_types=1);

namespace App\DTOs;

class ProductDto
{
    public function __construct(
        public readonly string $name,
        public readonly int $price,
        public readonly int $stock
    ) {}
}
