<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;

interface ProductRepositoryInterface
{
    public function query(): Builder;

    public function findById(int $id): Product;

    public function create(array $data, array $supplierIds = []): Product;

    public function update(Product $product, array $data, ?array $supplierIds = null): Product;

    public function delete(Product $product): bool;
}
