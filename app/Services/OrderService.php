<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\OrderDto;
use App\DTOs\ResponseDto;
use App\Repositories\HoldRepository\HoldRepositoryInterface;
use App\Repositories\OrderRepository\OrderRepositoryInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    public function __construct(
        protected HoldRepositoryInterface $holdRepo,
        protected OrderRepositoryInterface $orderRepo
    ) {}

    public function getOrder(string $id): ResponseDto
    {
        $order = Cache::flexible("orders:{$id}", [30, 60], fn () => $this->orderRepo->findWithRelations($id, 'hold'));

        if (! $order) {
            return ResponseDto::error()
                ->setMessage('Order not found')
                ->setStatus(404);
        }

        return ResponseDto::success()
            ->setData($order)
            ->setMessage('Order retrieved successfully')
            ->setStatus(200);
    }

    public function createOrder($holdId): ResponseDto
    {
        return DB::transaction(function () use ($holdId) {
            $hold = $this->holdRepo->find($holdId);

            if (! $hold) {
                return ResponseDto::error()
                    ->setMessage('Hold not found')
                    ->setStatus(404);
            }

            if ($hold->is_used) {
                return ResponseDto::error()
                    ->setMessage('Hold has already been used')
                    ->setStatus(400);
            }

            if (now()->diffInSeconds($hold->expires_at) <= 0) {
                return ResponseDto::error()
                    ->setMessage('Hold has been expired')
                    ->setStatus(400);
            }

            $order = $this->orderRepo->store([
                'code' => 'ORD-'.Str::random(6),
                'total' => round($hold->qty * $hold->product->price, 2),
                'hold_id' => $hold->id,
            ]);

            $hold->is_used = true;
            $hold->expires_at = null;
            $hold->save();

            DB::afterCommit(function () use ($order, $hold) {
                Cache::put("orders:{$order->id}", $order, 30);
                Cache::put("holds:{$hold->id}", $hold, 30);
            });

            return ResponseDto::success()
                ->setData($order)
                ->setMessage('Order created successfully')
                ->setStatus(201);
        });
    }

    public function processPayment(OrderDto $orderDto): ResponseDto
    {
        return DB::transaction(function () use ($orderDto) {
            $order = Cache::flexible("orders:{$orderDto->orderId}", [30, 60],
                fn () => $this->orderRepo->addQuery()
                    ->lockForUpdate()
                    ->with('hold')
                    ->find($orderDto->orderId)
            );

            if (! $order) {
                return ResponseDto::error()
                    ->setMessage('Order not found')
                    ->setStatus(404);
            }

            if ($order->status === 'paid') {
                return ResponseDto::error()
                    ->setMessage('Order has already been paid')
                    ->setStatus(400);
            }

            $order->status = $orderDto->status;
            $order->save();

            return ResponseDto::success()
                ->setData($order)
                ->setMessage('Order updated successfully')
                ->setStatus(200);
        });
    }
}
