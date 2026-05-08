<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            DemoUserSeeder::class,
            CategorySeeder::class,
            ClearanceCategorySeeder::class,
            SupplierSeeder::class,
            ExtraSupplierSeeder::class,
            ProductSeeder::class,
            LowStockProductSeeder::class,
            InactiveProductSeeder::class,
            ProductSupplierSeeder::class,
        ]);
    }
}
