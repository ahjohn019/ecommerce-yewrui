<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class LowStockProductSeeder extends Seeder
{
    public function run(): void
    {
        $electronics = Category::where('slug', 'electronics')->firstOrFail();

        Product::updateOrCreate(
            ['sku' => 'LOW-001'],
            [
                'category_id' => $electronics->id,
                'name' => 'USB-C Cable',
                'slug' => 'usb-c-cable',
                'description' => 'Fast charging USB-C cable',
                'price' => 19.90,
                'sale_price' => null,
                'stock_quantity' => 3,
                'image_path' => null,
                'is_active' => true,
            ]
        );
    }
}
