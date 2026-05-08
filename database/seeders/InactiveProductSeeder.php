<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class InactiveProductSeeder extends Seeder
{
    public function run(): void
    {
        $clearance = Category::where('slug', 'clearance')->firstOrFail();

        Product::updateOrCreate(
            ['sku' => 'CLR-001'],
            [
                'category_id' => $clearance->id,
                'name' => 'Old Model Speaker',
                'slug' => 'old-model-speaker',
                'description' => 'Discontinued speaker model',
                'price' => 99.00,
                'sale_price' => 49.00,
                'stock_quantity' => 0,
                'image_path' => null,
                'is_active' => false,
            ]
        );
    }
}
