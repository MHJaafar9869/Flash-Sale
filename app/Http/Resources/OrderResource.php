<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $resource = $this->resource;

        return [
            'id' => $resource->id,
            'code' => $resource->code,
            'total' => $resource->total,
            'status' => $resource->status?->label(),
            'created_at' => $resource->created_at,
            'updated_at' => $resource->updated_at,
            'hold' => HoldResource::make($this->whenLoaded('hold')),
        ];
    }
}
