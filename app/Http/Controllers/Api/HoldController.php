<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\DTOs\HoldDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Hold\StoreHoldRequest;
use App\Http\Resources\HoldResource;
use App\Repositories\HoldRepository\HoldRepositoryInterface;
use App\Services\HoldService;
use App\Traits\ResponseJson;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class HoldController extends Controller
{
    use ResponseJson;

    public function __construct(
        protected HoldRepositoryInterface $holdRepo,
        protected HoldService $holdService
    ) {}

    // GET /api/holds
    public function index(): JsonResponse
    {
        $holds = Cache::flexible('holds', [30, 60], fn () => $this->holdRepo->allWithRelations(['product', 'order']));

        return $this->respondWithData(
            HoldResource::collection($holds),
            'Holds retrieved successfully'
        );
    }

    // GET /api/holds/{id}
    public function show(int|string $id): JsonResponse
    {
        $hold = $this->holdService->getHold($id);

        if (! $hold->isSuccess()) {
            return $this->respondError($hold->message, $hold->status);
        }

        return $this->respondWithData(
            HoldResource::make($hold->data),
            $hold->message,
            $hold->status
        );
    }

    // POST /api/holds { product_id, qty }
    public function store(StoreHoldRequest $request): JsonResponse
    {
        $hold = HoldDto::fromRequest(
            $request->validated('product_id'),
            $request->validated('qty')
        );

        $response = $this->holdService->createHold($hold);

        if (! $response->isSuccess()) {
            return $this->respondError($response->message, $response->status);
        }

        return $this->respondWithData(
            HoldResource::make($response->data),
            $response->message,
            $response->status
        );
    }
}
