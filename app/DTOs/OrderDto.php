<?php

declare(strict_types=1);

namespace App\DTOs;

class OrderDto
{
    public function __construct(
        public readonly string $status,
        public readonly string $orderId,
    ) {}

    public static function fromRequest(string $status, string $orderId): self
    {
        return new self(
            orderId: $orderId,
            status: $status,
        );
    }
}
