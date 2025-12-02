<?php

declare(strict_types=1);

namespace App\Repositories\OrderRepository;

use App\Models\Order;
use App\Repositories\BaseRepository\BaseRepository;

final readonly class OrderRepository extends BaseRepository implements OrderRepositoryInterface
{
    public function __construct(Order $order)
    {
        parent::__construct($order);
    }
}
