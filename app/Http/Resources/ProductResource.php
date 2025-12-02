<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'name' => $resource->name,
            'price' => $resource->price,
            'stock' => $resource->stock,
            'created_at' => $resource->created_at,
            'updated_at' => $resource->updated_at,
            'holds' => HoldResource::collection($this->whenLoaded('holds')),
        ];
    }
}
