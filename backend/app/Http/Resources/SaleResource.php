<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ClientResource;
use App\Http\Resources\StoreResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\SaleItemResource;

class SaleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'invoice_number' => $this->invoice_number,
            'date' => $this->date,
            'client' => new ClientResource($this->whenLoaded('client')),
            'store' => new StoreResource($this->whenLoaded('store')),
            'user' => new UserResource($this->whenLoaded('user')),
            'items' => SaleItemResource::collection($this->whenLoaded('items')),
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
            'items_count' => $this->whenLoaded('items', function () {
                return $this->items->count();
            }),
        ];
    }
}