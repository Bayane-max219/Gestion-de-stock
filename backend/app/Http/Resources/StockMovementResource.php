<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockMovementResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product' => $this->whenLoaded('product', function () {
                return new ProductResource($this->product);
            }),
            'store' => $this->whenLoaded('store', function () {
                return new StoreResource($this->store);
            }),
            'type' => $this->type,
            'quantity' => $this->quantity,
            'previous_quantity' => $this->previous_quantity,
            'current_quantity' => $this->current_quantity,
            'reason' => $this->reason,
            'notes' => $this->notes,
            'reference_id' => $this->reference_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}