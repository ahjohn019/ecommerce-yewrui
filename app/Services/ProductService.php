<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class ProductService extends ResponseService
{
    public function __construct(
        protected ProductRepositoryInterface $products,
        protected CacheService $cache
    ) {
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $cacheKey = $this->cache->cacheKey('paginate:'.md5(json_encode([
            'filters' => [
                'search' => $filters['search'] ?? null,
                'category_id' => $filters['category_id'] ?? null,
                'min_price' => $filters['min_price'] ?? null,
                'max_price' => $filters['max_price'] ?? null,
                'stock_level' => $filters['stock_level'] ?? null,
                'low_stock_threshold' => $filters['low_stock_threshold'] ?? null,
            ],
            'per_page' => $perPage,
        ], JSON_THROW_ON_ERROR)));

        return $this->cache->remember($cacheKey, CacheService::LIST_CACHE_TTL, function () use ($filters, $perPage) {
            $query = $this->products->query();
            $query = $this->applyFilters($query, $filters);

            return $query->latest()->paginate($perPage)->withQueryString();
        });
    }

    public function find(int $id): Product
    {
        $cacheKey = $this->cache->cacheKey('find:'.$id);

        return $this->cache->remember($cacheKey, CacheService::ITEM_CACHE_TTL, fn () => $this->products->findById($id));
    }

    public function create(array $data): Product
    {
        [$productData, $supplierIds] = $this->mapProductData($data, true);

        $product = $this->products->create($productData, $supplierIds);

        $this->cache->bumpVersion();

        return $product;
    }

    public function update(Product $product, array $data): Product
    {
        [$productData, $supplierIds] = $this->mapProductData($data);

        $product = $this->products->update($product, $productData, $supplierIds);

        $this->cache->bumpVersion();

        return $product;
    }

    public function delete(Product $product): bool
    {
        $deleted = $this->products->delete($product);

        $this->cache->bumpVersion();

        return $deleted;
    }

    private function applyFilters(Builder $query, array $filters): Builder
    {
        if (filled($filters['search'] ?? null)) {
            $query = $this->applySearch($query, (string) $filters['search']);
        }

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

    private function applySearch(Builder $query, string $search): Builder
    {
        return $query->whereFullText(['name', 'description'], $search);
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
