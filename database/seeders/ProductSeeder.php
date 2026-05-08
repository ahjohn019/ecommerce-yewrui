<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $electronics = Category::where('slug', 'electronics')->firstOrFail();
        $accessories = Category::where('slug', 'accessories')->firstOrFail();
        $home = Category::where('slug', 'home')->firstOrFail();
        $office = Category::where('slug', 'office')->firstOrFail();

        $products = [
            [
                'category_id' => $electronics->id,
                'name' => 'Wireless Mouse',
                'slug' => 'wireless-mouse',
                'sku' => 'ELEC-001',
                'description' => 'Ergonomic wireless mouse',
                'price' => 39.90,
                'sale_price' => 29.90,
                'stock_quantity' => 25,
                'image_path' => null,
                'is_active' => true,
            ],
            [
                'category_id' => $electronics->id,
                'name' => 'Mechanical Keyboard',
                'slug' => 'mechanical-keyboard',
                'sku' => 'ELEC-002',
                'description' => 'Backlit mechanical keyboard',
                'price' => 249.00,
                'sale_price' => 199.00,
                'stock_quantity' => 12,
                'image_path' => null,
                'is_active' => true,
            ],
            [
                'category_id' => $accessories->id,
                'name' => 'Laptop Sleeve',
                'slug' => 'laptop-sleeve',
                'sku' => 'ACC-001',
                'description' => 'Protective laptop sleeve',
                'price' => 59.00,
                'sale_price' => null,
                'stock_quantity' => 18,
                'image_path' => null,
                'is_active' => true,
            ],
            [
                'category_id' => $home->id,
                'name' => 'Desk Lamp',
                'slug' => 'desk-lamp',
                'sku' => 'HME-001',
                'description' => 'Modern LED desk lamp',
                'price' => 85.00,
                'sale_price' => 75.00,
                'stock_quantity' => 20,
                'image_path' => null,
                'is_active' => true,
            ],
            [
                'category_id' => $office->id,
                'name' => 'Notebook Pack',
                'slug' => 'notebook-pack',
                'sku' => 'OFF-001',
                'description' => 'Pack of premium notebooks',
                'price' => 25.00,
                'sale_price' => null,
                'stock_quantity' => 30,
                'image_path' => null,
                'is_active' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['sku' => $product['sku']],
                $product
            );
        }
    }
}
