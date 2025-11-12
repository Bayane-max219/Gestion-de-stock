<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\DemoSeeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Store;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Client;
use App\Models\StoreProduct;

class DemoSeederTest extends TestCase
{
    use RefreshDatabase;
    
    public function assertNotNull($value, $message = ''): void
    {
        if ($value === null) {
            throw new \Exception($message ?: 'Failed asserting that value is not null');
        }
    }

    public function assertTrue($condition, $message = ''): void
    {
        if (!$condition) {
            throw new \Exception($message ?: 'Failed asserting that condition is true');
        }
    }

    public function assertEquals($expected, $actual, $message = ''): void
    {
        if ($expected !== $actual) {
            throw new \Exception($message ?: "Failed asserting that {$actual} matches expected {$expected}");
        }
    }

    public function setUp(): void
    {
        parent::setUp();
        
        // Run migrations
        $this->artisan('migrate:fresh');
        
        // Run the seeder
        $this->seed(DemoSeeder::class);
    }

    public function test_demo_users_are_created_with_correct_roles()
    {
        // Admin user
        $admin = User::where('email', 'admin@demo.com')->first();
        $this->assertNotNull($admin);
        $this->assertTrue($admin->roles()->where('name', 'admin')->exists());

        // Manager user
        $manager = User::where('email', 'manager@demo.com')->first();
        $this->assertNotNull($manager);
        $this->assertTrue($manager->roles()->where('name', 'store_manager')->exists());

        // Cashier user
        $cashier = User::where('email', 'cashier@demo.com')->first();
        $this->assertNotNull($cashier);
        $this->assertTrue($cashier->roles()->where('name', 'cashier')->exists());
    }

    public function test_demo_stores_are_created()
    {
        $mainStore = Store::where('name', 'Main Store')->first();
        $branchStore = Store::where('name', 'Branch Store')->first();

        $this->assertNotNull($mainStore);
        $this->assertNotNull($branchStore);
        $this->assertEquals(2, Store::count());
    }

    public function test_categories_are_created_with_subcategories()
    {
        $mainCategories = Category::whereNull('parent_id')->get();
        $this->assertEquals(4, $mainCategories->count());

        foreach ($mainCategories as $category) {
            $this->assertTrue($category->children()->exists());
            $this->assertEquals(3, $category->children()->count());
        }
    }

    public function test_products_are_created_with_stock()
    {
        $this->assertEquals(50, Product::count());
        $this->assertEquals(5, Supplier::count());
        
        // Check if all products have supplier
        $this->assertEquals(0, Product::whereNull('supplier_id')->count());

        // Check if products have stock in stores
        $mainStore = Store::where('name', 'Main Store')->first();
        $branchStore = Store::where('name', 'Branch Store')->first();

        $mainStoreProducts = StoreProduct::where('store_id', $mainStore->id)->count();
        $branchStoreProducts = StoreProduct::where('store_id', $branchStore->id)->count();

        $this->assertEquals(50, $mainStoreProducts);
        $this->assertEquals(50, $branchStoreProducts);
    }

    public function test_low_and_zero_stock_products_exist()
    {
        $mainStore = Store::where('name', 'Main Store')->first();
        $branchStore = Store::where('name', 'Branch Store')->first();

        // Check low stock products in main store
        $lowStockCount = StoreProduct::where('store_id', $mainStore->id)
            ->where('quantity', 2)
            ->count();
        $this->assertEquals(5, $lowStockCount);

        // Check zero stock products in branch store
        $zeroStockCount = StoreProduct::where('store_id', $branchStore->id)
            ->where('quantity', 0)
            ->count();
        $this->assertEquals(3, $zeroStockCount);
    }

    public function test_clients_are_created()
    {
        $this->assertEquals(20, Client::count());
    }
}