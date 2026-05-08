<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ProductRepository implements ProductRepositoryInterface
{
    public function query(): Builder
    {
        return Product::query()->active()->with(['category', 'suppliers']);
    }

    public function findById(int $id): Product
    {
        return Product::active()->with(['category', 'suppliers'])->findOrFail($id);
    }

    public function create(array $data, array $supplierIds = []): Product
    {
        return DB::transaction(function () use ($data, $supplierIds) {
            $product = Product::create($data);

            if (! empty($supplierIds)) {
                $product->suppliers()->sync($supplierIds);
            }

            return $product->load(['category', 'suppliers']);
        });
    }

    public function update(Product $product, array $data, ?array $supplierIds = null): Product
    {
        return DB::transaction(function () use ($product, $data, $supplierIds) {
            $product->update($data);

            if (is_array($supplierIds)) {
                $product->suppliers()->sync($supplierIds);
            }

            return $product->load(['category', 'suppliers']);
        });
    }

    public function delete(Product $product): bool
    {
        return (bool) $product->delete();
    }
}
