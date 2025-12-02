<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\DTOs\OrderDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\PaymentStatusRequest;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Repositories\OrderRepository\OrderRepositoryInterface;
use App\Services\OrderService;
use App\Traits\ResponseJson;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class OrderController extends Controller
{
    use ResponseJson;

    public function __construct(
        protected OrderRepositoryInterface $orderRepo,
        protected OrderService $orderService
    ) {}

    // GET /api/orders
    public function index(): JsonResponse
    {
        $holds = Cache::flexible('orders', [30, 60], fn () => $this->orderRepo->allWithRelations('hold'));

        return $this->respondWithData(
            OrderResource::collection($holds),
            'Holds retrieved successfully'
        );
    }

    // GET /api/orders/{id}
    public function show(string $id): JsonResponse
    {
        $order = $this->orderService->getOrder($id);

        if (! $order->isSuccess()) {
            return $this->respondError($order->message, $order->status);
        }

        return $this->respondWithData(
            OrderResource::make($order->data),
            $order->message,
            $order->status
        );
    }

    // POST /api/orders { hold_id }
    public function store(StoreOrderRequest $request): JsonResponse
    {
        $order = $this->orderService->createOrder($request->validated('hold_id'));

        if (! $order->isSuccess()) {
            return $this->respondError($order->message, $order->status);
        }

        return $this->respondWithData(
            OrderResource::make($order->data),
            $order->message,
            $order->status
        );
    }

    // PUT /api/orders/{id}/payment/webhook { status }
    public function pay(PaymentStatusRequest $request, string $id): JsonResponse
    {
        $orderDto = OrderDto::fromRequest($request->validated('status'), $id);

        $orderPayment = $this->orderService->processPayment($orderDto);

        if (! $orderPayment->isSuccess()) {
            return $this->respondError($orderPayment->message, $orderPayment->status);
        }

        return $this->respondWithData(
            OrderResource::make($orderPayment->data),
            $orderPayment->message,
            $orderPayment->status
        );
    }
}
