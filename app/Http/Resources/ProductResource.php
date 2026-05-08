<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'sku' => $this->sku,
            'description' => $this->description,
            'price' => $this->price,
            'sale_price' => $this->sale_price,
            'stock_quantity' => $this->stock_quantity,
            'image_path' => $this->image_path,
            'is_active' => $this->is_active,
            'category' => [
                'id' => $this->category?->id,
                'name' => $this->category?->name,
                'slug' => $this->category?->slug,
            ],
            'suppliers' => $this->suppliers->map(fn ($supplier) => [
                'id' => $supplier->id,
                'name' => $supplier->name,
                'email' => $supplier->email,
            ])->values(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
