<?php

declare(strict_types=1);

namespace App\Repositories\HoldRepository;

use App\Repositories\BaseRepository\BaseRepositoryInterface;

interface HoldRepositoryInterface extends BaseRepositoryInterface
{
    public function holdProduct(int|string $productId, int $qty);
}
