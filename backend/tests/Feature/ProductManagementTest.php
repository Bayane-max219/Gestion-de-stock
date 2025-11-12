<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;

class ProductManagementTest extends TestCase
{
    protected $admin;
    protected $store;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = $this->createAuthenticatedAdmin();
        $this->store = $this->createTestStore();
    }

    /** @test */
    public function an_admin_can_create_a_product()
    {
        $category = Category::create([
            'name' => 'Test Category',
            'description' => 'Test Description'
        ]);

        $supplier = Supplier::create([
            'name' => 'Test Supplier',
            'email' => 'supplier@test.com',
            'phone' => '1234567890',
            'address' => 'Test Address',
            'is_active' => true
        ]);

        $response = $this->postJson('/api/products', [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'sku' => 'TEST001',
            'barcode' => '1234567890123',
            'price' => 9.99,
            'cost' => 5.99,
            'category_id' => $category->id,
            'supplier_id' => $supplier->id,
            'unit' => 'piece',
            'low_stock_threshold' => 10,
            'stores' => [
                $this->store->id => 100
            ]
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
            'sku' => 'TEST001'
        ]);
        
        // Check if stock was created
        $this->assertDatabaseHas('store_products', [
            'store_id' => $this->store->id,
            'quantity' => 100
        ]);
    }

    /** @test */
    public function product_sku_must_be_unique()
    {
        $category = Category::create([
            'name' => 'Test Category',
            'description' => 'Test Description'
        ]);

        $supplier = Supplier::create([
            'name' => 'Test Supplier',
            'email' => 'supplier@test.com',
            'phone' => '1234567890',
            'address' => 'Test Address',
            'is_active' => true
        ]);

        $product = Product::create([
            'name' => 'Test Product',
            'description' => 'Test Description',
            'sku' => 'TEST001',
            'barcode' => '1234567890123',
            'price' => 9.99,
            'cost' => 5.99,
            'category_id' => $category->id,
            'supplier_id' => $supplier->id,
            'unit' => 'piece',
            'low_stock_threshold' => 10
        ]);

        $response = $this->postJson('/api/products', [
            'name' => 'Test Product 2',
            'description' => 'Test Description',
            'sku' => 'TEST001', // Same SKU
            'barcode' => '1234567890124',
            'price' => 9.99,
            'cost' => 5.99,
            'category_id' => $category->id,
            'supplier_id' => $supplier->id,
            'unit' => 'piece',
            'low_stock_threshold' => 10
        ]);

        $response->assertStatus(422);
    }
}