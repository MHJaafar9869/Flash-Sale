<?php

declare(strict_types=1);

namespace App\DTOs;

class HoldDto
{
    public function __construct(
        public string $productId,
        public int $qty,
    ) {}

    public static function fromRequest(int|string $productId, int $qty): self
    {
        return new self(
            productId: $productId,
            qty: $qty,
        );
    }
}
