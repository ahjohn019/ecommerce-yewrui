<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            [
                'name' => 'Alpha Supply',
                'contact_name' => 'Amin Rahman',
                'email' => 'alpha@example.com',
                'phone' => '012-3001001',
                'address' => 'Kuala Lumpur',
            ],
            [
                'name' => 'Beta Wholesale',
                'contact_name' => 'Siti Hajar',
                'email' => 'beta@example.com',
                'phone' => '012-3001002',
                'address' => 'Petaling Jaya',
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::updateOrCreate(
                ['email' => $supplier['email']],
                $supplier + ['is_active' => true]
            );
        }
    }
}
