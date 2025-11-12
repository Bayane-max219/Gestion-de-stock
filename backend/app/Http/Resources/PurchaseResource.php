<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'receipt_number' => $this->receipt_number,
            'date' => $this->date,
            'supplier' => new SupplierResource($this->whenLoaded('supplier')),
            'store' => new StoreResource($this->whenLoaded('store')),
            'user' => new UserResource($this->whenLoaded('user')),
            'items' => PurchaseItemResource::collection($this->whenLoaded('items')),
            'subtotal' => $this->subtotal,
            'tax' => $this->tax,
            'discount' => $this->discount,
            'total' => $this->total,
            'paid_amount' => $this->paid_amount,
            'payment_method' => $this->payment_method,
            'payment_status' => $this->payment_status,
            'status' => $this->status,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'expected_date' => $this->expected_date,
            'delivery_date' => $this->delivery_date,
            'items_count' => $this->whenLoaded('items', function () {
                return $this->items->count();
            }),
            'returned_items_count' => $this->whenLoaded('items', function () {
                return $this->items->where('returned_quantity', '>', 0)->count();
            }),
        ];
    }
}