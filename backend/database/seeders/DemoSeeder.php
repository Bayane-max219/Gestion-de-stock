<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Store;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Client;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run()
    {
        // Create roles
        $adminRole = Role::create(['name' => 'admin']);
        $managerRole = Role::create(['name' => 'store_manager']);
        $cashierRole = Role::create(['name' => 'cashier']);

        // Create demo users
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@demo.com',
            'password' => Hash::make('password123')
        ]);
        $admin->roles()->attach($adminRole);

        $manager = User::create([
            'name' => 'Store Manager',
            'email' => 'manager@demo.com',
            'password' => Hash::make('password123')
        ]);
        $manager->roles()->attach($managerRole);

        $cashier = User::create([
            'name' => 'Cashier User',
            'email' => 'cashier@demo.com',
            'password' => Hash::make('password123')
        ]);
        $cashier->roles()->attach($cashierRole);

        // Create demo stores
        $mainStore = Store::create([
            'name' => 'Main Store',
            'address' => '123 Main St, City',
            'phone' => '555-0100',
            'email' => 'main@demo.com'
        ]);

        $branchStore = Store::create([
            'name' => 'Branch Store',
            'address' => '456 Branch St, City',
            'phone' => '555-0200',
            'email' => 'branch@demo.com'
        ]);

        // Create categories
        $categories = [
            'Electronics' => ['Phones', 'Computers', 'Accessories'],
            'Clothing' => ['Men', 'Women', 'Children'],
            'Home' => ['Kitchen', 'Bedroom', 'Living Room'],
            'Books' => ['Fiction', 'Non-Fiction', 'Educational']
        ];

        foreach ($categories as $main => $subs) {
            $mainCat = Category::create(['name' => $main]);
            foreach ($subs as $sub) {
                Category::create([
                    'name' => $sub,
                    'parent_id' => $mainCat->id
                ]);
            }
        }

        // Create suppliers
        $suppliers = Supplier::factory()->count(5)->create();

        // Create products with stock
        $products = Product::factory()
            ->count(50)
            ->create()
            ->each(function ($product) use ($mainStore, $branchStore, $suppliers) {
                // Assign random supplier
                $product->supplier()->associate($suppliers->random())->save();

                // Add stock to stores
                $product->storeProducts()->createMany([
                    [
                        'store_id' => $mainStore->id,
                        'quantity' => rand(10, 100)
                    ],
                    [
                        'store_id' => $branchStore->id,
                        'quantity' => rand(5, 50)
                    ]
                ]);
            });

        // Create demo clients
        Client::factory()->count(20)->create();

        // Set some products to low stock for demo
        $products->random(5)->each(function ($product) use ($mainStore) {
            $product->storeProducts()
                ->where('store_id', $mainStore->id)
                ->update(['quantity' => 2]);
        });

        // Create some products with zero stock
        $products->random(3)->each(function ($product) use ($branchStore) {
            $product->storeProducts()
                ->where('store_id', $branchStore->id)
                ->update(['quantity' => 0]);
        });
    }
}