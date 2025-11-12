<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            RoleSeeder::class,
            StoreSeeder::class,
            UserSeeder::class,
            SupplierSeeder::class,
            ProductSeeder::class,
        ]);
    }
}