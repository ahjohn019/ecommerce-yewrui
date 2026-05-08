<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Electronics', 'slug' => 'electronics', 'description' => 'Gadgets and devices'],
            ['name' => 'Accessories', 'slug' => 'accessories', 'description' => 'Cables, bags, and add-ons'],
            ['name' => 'Home', 'slug' => 'home', 'description' => 'Home and living products'],
            ['name' => 'Office', 'slug' => 'office', 'description' => 'Workspace essentials'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                $category + ['is_active' => true]
            );
        }
    }
}
