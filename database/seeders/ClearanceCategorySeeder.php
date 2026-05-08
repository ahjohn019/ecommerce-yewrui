<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class ClearanceCategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::updateOrCreate(
            ['slug' => 'clearance'],
            [
                'name' => 'Clearance',
                'description' => 'Discounted and clearance items',
                'is_active' => true,
            ]
        );
    }
}
