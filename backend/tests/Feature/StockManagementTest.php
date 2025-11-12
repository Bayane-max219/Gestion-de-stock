<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Client;

class StockManagementTest extends TestCase
{
    protected $admin;
    protected $store;
    protected $product;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = $this->createAuthenticatedAdmin();
        $this->store = $this->createTestStore();
        
        // Create a test product
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

        $this->product = Product::create([
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

        // Initialize stock
        $this->product->stores()->attach($this->store->id, ['quantity' => 0]);
    }

    /** @test */
    public function stock_increases_when_purchase_is_received()
    {
        $purchase = Purchase::create([
            'store_id' => $this->store->id,
            'supplier_id' => $this->product->supplier_id,
            'user_id' => $this->admin->id,
            'reference_number' => 'PO-001',
            'date' => now(),
            'status' => 'pending',
            'total' => 599.40
        ]);

        $purchase->items()->create([
            'product_id' => $this->product->id,
            'quantity' => 100,
            'unit_cost' => 5.99,
            'subtotal' => 599.40
        ]);

        $response = $this->postJson("/api/purchases/{$purchase->id}/receive", [
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 100
                ]
            ]
        ]);

        $response->assertStatus(200);
        
        $this->assertDatabaseHas('store_products', [
            'store_id' => $this->store->id,
            'product_id' => $this->product->id,
            'quantity' => 100
        ]);
    }

    /** @test */
    public function stock_decreases_when_sale_is_created()
    {
        // First add some stock
        $this->product->stores()->updateExistingPivot($this->store->id, ['quantity' => 100]);

        $client = Client::create([
            'name' => 'Test Client',
            'email' => 'client@test.com',
            'phone' => '1234567890',
            'address' => 'Test Address'
        ]);

        $response = $this->postJson('/api/sales', [
            'store_id' => $this->store->id,
            'client_id' => $client->id,
            'date' => now(),
            'subtotal' => 99.90,
            'tax' => 0,
            'total' => 99.90,
            'paid_amount' => 99.90,
            'payment_method' => 'cash',
            'payment_status' => 'paid',
            'status' => 'completed',
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 10,
                    'unit_price' => 9.99,
                    'subtotal' => 99.90
                ]
            ]
        ]);

        $response->assertStatus(201);
        
        $this->assertDatabaseHas('store_products', [
            'store_id' => $this->store->id,
            'product_id' => $this->product->id,
            'quantity' => 90
        ]);
    }

    /** @test */
    public function cannot_sell_more_than_available_stock()
    {
        // Set initial stock to 5
        $this->product->stores()->updateExistingPivot($this->store->id, ['quantity' => 5]);

        $client = Client::create([
            'name' => 'Test Client',
            'email' => 'client@test.com',
            'phone' => '1234567890',
            'address' => 'Test Address'
        ]);

        $response = $this->postJson('/api/sales', [
            'store_id' => $this->store->id,
            'client_id' => $client->id,
            'date' => now(),
            'subtotal' => 99.90,
            'tax' => 0,
            'total' => 99.90,
            'paid_amount' => 99.90,
            'payment_method' => 'cash',
            'payment_status' => 'paid',
            'status' => 'completed',
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 10, // Trying to sell more than available
                    'unit_price' => 9.99,
                    'subtotal' => 99.90
                ]
            ]
        ]);

        $response->assertStatus(422);
        
        $this->assertDatabaseHas('store_products', [
            'store_id' => $this->store->id,
            'product_id' => $this->product->id,
            'quantity' => 5
        ]); // Stock should remain unchanged
    }
}