<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_products_can_be_listed_with_pagination_and_filtering(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $categoryOne = Category::create([
            'name' => 'Electronics',
            'slug' => 'electronics',
            'description' => null,
            'is_active' => true,
        ]);

        $categoryTwo = Category::create([
            'name' => 'Books',
            'slug' => 'books',
            'description' => null,
            'is_active' => true,
        ]);

        Product::create([
            'category_id' => $categoryOne->id,
            'name' => 'Wireless Mouse',
            'slug' => 'wireless-mouse',
            'sku' => 'MOUSE-001',
            'description' => 'Mouse',
            'price' => 25.00,
            'sale_price' => null,
            'stock_quantity' => 10,
            'image_path' => null,
            'is_active' => true,
        ]);

        Product::create([
            'category_id' => $categoryTwo->id,
            'name' => 'Notebook',
            'slug' => 'notebook',
            'sku' => 'BOOK-001',
            'description' => 'Book',
            'price' => 15.00,
            'sale_price' => null,
            'stock_quantity' => 5,
            'image_path' => null,
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/products?category_id=' . $categoryOne->id . '&per_page=1');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('code', 200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Wireless Mouse');
    }

    public function test_authenticated_user_can_create_product(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $category = Category::create([
            'name' => 'Electronics',
            'slug' => 'electronics',
            'description' => null,
            'is_active' => true,
        ]);

        $supplier = Supplier::create([
            'name' => 'Main Supplier',
            'contact_name' => 'Supplier Contact',
            'email' => 'supplier@example.com',
            'phone' => '0123456789',
            'address' => 'Kuala Lumpur',
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/products', [
            'category_id' => $category->id,
            'supplier_ids' => [$supplier->id],
            'name' => 'Gaming Keyboard',
            'slug' => 'Gaming Keyboard',
            'sku' => 'KB-001',
            'description' => 'Mechanical keyboard',
            'price' => 199.99,
            'sale_price' => 149.99,
            'stock_quantity' => 20,
            'image_path' => null,
            'is_active' => true,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('code', 201)
            ->assertJsonPath('data.name', 'Gaming Keyboard');

        $this->assertDatabaseHas('products', [
            'sku' => 'KB-001',
            'name' => 'Gaming Keyboard',
        ]);

        $this->assertDatabaseHas('product_supplier', [
            'supplier_id' => $supplier->id,
        ]);
    }

    public function test_authenticated_user_can_update_product(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $category = Category::create([
            'name' => 'Electronics',
            'slug' => 'electronics',
            'description' => null,
            'is_active' => true,
        ]);

        $newCategory = Category::create([
            'name' => 'Accessories',
            'slug' => 'accessories',
            'description' => null,
            'is_active' => true,
        ]);

        $supplier = Supplier::create([
            'name' => 'Main Supplier',
            'contact_name' => 'Supplier Contact',
            'email' => 'supplier@example.com',
            'phone' => '0123456789',
            'address' => 'Kuala Lumpur',
            'is_active' => true,
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Old Name',
            'slug' => 'old-name',
            'sku' => 'PRD-001',
            'description' => 'Old description',
            'price' => 50.00,
            'sale_price' => null,
            'stock_quantity' => 5,
            'image_path' => null,
            'is_active' => true,
        ]);

        $response = $this->putJson('/api/products/' . $product->id, [
            'category_id' => $newCategory->id,
            'supplier_ids' => [$supplier->id],
            'name' => 'New Name',
            'slug' => 'new-name',
            'sku' => 'PRD-001',
            'description' => 'Updated description',
            'price' => 60.00,
            'stock_quantity' => 8,
            'is_active' => true,
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('code', 200)
            ->assertJsonPath('data.name', 'New Name')
            ->assertJsonPath('data.category.id', $newCategory->id);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'New Name',
            'category_id' => $newCategory->id,
        ]);
    }

    public function test_authenticated_user_can_soft_delete_product(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $category = Category::create([
            'name' => 'Electronics',
            'slug' => 'electronics',
            'description' => null,
            'is_active' => true,
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Delete Me',
            'slug' => 'delete-me',
            'sku' => 'DEL-001',
            'description' => 'To be deleted',
            'price' => 10.00,
            'sale_price' => null,
            'stock_quantity' => 1,
            'image_path' => null,
            'is_active' => true,
        ]);

        $response = $this->deleteJson('/api/products/' . $product->id);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('code', 200);

        $this->assertSoftDeleted('products', [
            'id' => $product->id,
        ]);

        $this->getJson('/api/products/' . $product->id)->assertNotFound();
    }
}
