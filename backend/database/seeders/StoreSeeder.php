<?php

namespace Database\Seeders;

use App\Models\Store;
use Illuminate\Database\Seeder;

class StoreSeeder extends Seeder
{
    public function run()
    {
        Store::create([
            'name' => 'Main Store',
            'location' => '123 Main Street',
            'phone' => '+1234567890',
            'email' => 'store@example.com',
            'is_active' => true
        ]);

        Store::create([
            'name' => 'Branch Store',
            'location' => '456 Branch Avenue',
            'phone' => '+1234567891',
            'email' => 'branch@example.com',
            'is_active' => true
        ]);
    }
}