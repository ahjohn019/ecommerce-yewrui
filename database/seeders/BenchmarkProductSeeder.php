<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class BenchmarkProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'electronics' => Category::where('slug', 'electronics')->firstOrFail(),
            'accessories' => Category::where('slug', 'accessories')->firstOrFail(),
            'home' => Category::where('slug', 'home')->firstOrFail(),
            'office' => Category::where('slug', 'office')->firstOrFail(),
        ];

        $products = [];

        for ($i = 1; $i <= 30; $i++) {
            $categoryKey = match (true) {
                $i <= 8 => 'electronics',
                $i <= 16 => 'accessories',
                $i <= 23 => 'home',
                default => 'office',
            };

            $number = str_pad((string) $i, 2, '0', STR_PAD_LEFT);

            $products[] = [
                'category_id' => $categories[$categoryKey]->id,
                'name' => 'Benchmark Product '.$number,
                'slug' => 'benchmark-product-'.$number,
                'sku' => 'BMK-'.$number,
                'description' => 'Seeded benchmark product '.$number,
                'price' => 10.00 + ($i * 2.5),
                'sale_price' => $i % 3 === 0 ? 9.50 + ($i * 2.25) : null,
                'stock_quantity' => 20 + $i,
                'image_path' => null,
                'is_active' => true,
            ];
        }

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['sku' => $product['sku']],
                $product
            );
        }
    }
}
