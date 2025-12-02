<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HoldResource extends JsonResource
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
            'qty' => $resource->qty,
            'is_used' => $resource->is_used,
            'expires_at' => $resource->expires_at?->toDayDateTimeString(),
            'created_at' => $resource->created_at,
            'updated_at' => $resource->updated_at,
            'product' => ProductResource::make($this->whenLoaded('product')),
            'order' => OrderResource::make($this->whenLoaded('order')),
        ];
    }
}
