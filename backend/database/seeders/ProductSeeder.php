<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Store;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run()
    {
        // Create a category first
        $category = Category::create([
            'name' => 'Office Supplies',
            'description' => 'General office supplies and materials'
        ]);

        $products = [
            [
                'name' => 'Premium Paper A4',
                'description' => '500 sheets of high-quality A4 paper',
                'sku' => 'PAP001',
                'barcode' => '1234567890123',
                'price' => 5.99,
                'cost' => 3.50,
                'category_id' => $category->id,
                'supplier_id' => Supplier::inRandomOrder()->first()->id,
                'unit' => 'ream',
                'low_stock_threshold' => 10
            ],
            [
                'name' => 'Ballpoint Pens (Blue)',
                'description' => 'Pack of 12 blue ballpoint pens',
                'sku' => 'PEN001',
                'barcode' => '1234567890124',
                'price' => 3.99,
                'cost' => 2.00,
                'category_id' => $category->id,
                'supplier_id' => Supplier::inRandomOrder()->first()->id,
                'unit' => 'pack',
                'low_stock_threshold' => 5
            ],
            // Add more products here...
        ];

        $stores = Store::all();

        foreach ($products as $productData) {
            $product = Product::create($productData);
            
            // Add initial stock to each store
            foreach ($stores as $store) {
                $product->stores()->attach($store->id, [
                    'quantity' => rand(20, 100)
                ]);
            }
        }
    }
}