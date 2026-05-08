<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class ProductService extends ResponseService
{
    public function __construct(
        protected ProductRepositoryInterface $products
    ) {
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->products->query();
        $query = $this->applyFilters($query, $filters);

        return $query->latest()->paginate($perPage)->withQueryString();
    }

    public function find(int $id): Product
    {
        return $this->products->findById($id);
    }

    public function create(array $data): Product
    {
        [$productData, $supplierIds] = $this->mapProductData($data, true);

        return $this->products->create($productData, $supplierIds);
    }

    public function update(Product $product, array $data): Product
    {
        [$productData, $supplierIds] = $this->mapProductData($data);

        return $this->products->update($product, $productData, $supplierIds);
    }

    public function delete(Product $product): bool
    {
        return $this->products->delete($product);
    }

    private function applyFilters(Builder $query, array $filters): Builder
    {
        if (! empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (filled($filters['min_price'] ?? null)) {
            $query->where('price', '>=', $filters['min_price']);
        }

        if (filled($filters['max_price'] ?? null)) {
            $query->where('price', '<=', $filters['max_price']);
        }

        if (filled($filters['stock_level'] ?? null)) {
            switch ($filters['stock_level']) {
                case 'in_stock':
                    $query->where('stock_quantity', '>', 0);
                    break;
                case 'out_of_stock':
                    $query->where('stock_quantity', 0);
                    break;
                case 'low_stock':
                    $query->whereBetween('stock_quantity', [1, (int) ($filters['low_stock_threshold'] ?? 10)]);
                    break;
            }
        }

        return $query;
    }

    private function mapProductData(array $data, bool $isCreate = false): array
    {
        $supplierIds = array_key_exists('supplier_ids', $data)
            ? $data['supplier_ids']
            : ($isCreate ? [] : null);

        $mapped = [
            'category_id' => $data['category_id'] ?? null,
            'name' => $data['name'] ?? null,
            'slug' => $data['slug'] ?? null,
            'sku' => $data['sku'] ?? null,
            'description' => $data['description'] ?? null,
            'price' => $data['price'] ?? null,
            'sale_price' => $data['sale_price'] ?? null,
            'stock_quantity' => $data['stock_quantity'] ?? null,
            'image_path' => $data['image_path'] ?? null,
            'is_active' => $data['is_active'] ?? null,
        ];

        if ($isCreate && ! array_key_exists('is_active', $data)) {
            $mapped['is_active'] = true;
        }

        return [
            array_filter($mapped, static fn ($value) => $value !== null),
            $supplierIds,
        ];
    }
}
