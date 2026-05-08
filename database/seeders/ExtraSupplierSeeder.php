<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class ExtraSupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            [
                'name' => 'Gadget House',
                'contact_name' => 'Farid Ahmad',
                'email' => 'gadget@example.com',
                'phone' => '012-3001003',
                'address' => 'Shah Alam',
            ],
            [
                'name' => 'Daily Tools',
                'contact_name' => 'Nor Aina',
                'email' => 'tools@example.com',
                'phone' => '012-3001004',
                'address' => 'Subang Jaya',
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
