<?php

declare(strict_types=1);

namespace App\Repositories\HoldRepository;

use App\Models\Hold;
use App\Repositories\BaseRepository\BaseRepository;

final readonly class HoldRepository extends BaseRepository implements HoldRepositoryInterface
{
    public function __construct(Hold $hold)
    {
        parent::__construct($hold);
    }

    public function holdProduct(int|string $productId, int $qty): Hold
    {
        $hold = $this->store([
            'product_id' => $productId,
            'qty' => $qty,
            'expires_at' => now()->addMinutes(2),
        ]);

        return $hold;
    }
}
