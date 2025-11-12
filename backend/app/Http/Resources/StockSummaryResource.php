<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockSummaryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'product_id' => $this['product_id'],
            'product_name' => $this['product_name'],
            'sku' => $this['sku'],
            'category' => $this['category'],
            'global_stock' => $this['global_stock'],
            'store_stocks' => $this['store_stocks'],
            'low_stock' => $this['low_stock'],
            'stock_value' => $this['stock_value'],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}