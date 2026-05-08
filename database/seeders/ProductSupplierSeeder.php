<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class ProductSupplierSeeder extends Seeder
{
    public function run(): void
    {
        $alpha = Supplier::where('email', 'alpha@example.com')->firstOrFail();
        $beta = Supplier::where('email', 'beta@example.com')->firstOrFail();
        $gadget = Supplier::where('email', 'gadget@example.com')->firstOrFail();
        $tools = Supplier::where('email', 'tools@example.com')->firstOrFail();

        $wirelessMouse = Product::where('sku', 'ELEC-001')->firstOrFail();
        $mechanicalKeyboard = Product::where('sku', 'ELEC-002')->firstOrFail();
        $laptopSleeve = Product::where('sku', 'ACC-001')->firstOrFail();
        $deskLamp = Product::where('sku', 'HME-001')->firstOrFail();
        $notebookPack = Product::where('sku', 'OFF-001')->firstOrFail();
        $usbCable = Product::where('sku', 'LOW-001')->firstOrFail();

        $wirelessMouse->suppliers()->syncWithoutDetaching([$alpha->id, $gadget->id]);
        $mechanicalKeyboard->suppliers()->syncWithoutDetaching([$alpha->id, $beta->id]);
        $laptopSleeve->suppliers()->syncWithoutDetaching([$beta->id, $tools->id]);
        $deskLamp->suppliers()->syncWithoutDetaching([$gadget->id]);
        $notebookPack->suppliers()->syncWithoutDetaching([$tools->id]);
        $usbCable->suppliers()->syncWithoutDetaching([$alpha->id]);
    }
}
