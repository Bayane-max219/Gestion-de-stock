<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'parent_id' => $this->parent_id,
            'is_active' => $this->is_active,
            'parent' => $this->whenLoaded('parent', function() {
                return new CategoryResource($this->parent);
            }),
            'children' => $this->whenLoaded('children', function() {
                return CategoryResource::collection($this->children);
            }),
            'products_count' => $this->whenCounted('products'),
            'products' => $this->whenLoaded('products', function() {
                return ProductResource::collection($this->products);
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}