<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Store;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin123'),
            'phone' => '+1234567890',
            'role_id' => Role::where('name', 'admin')->first()->id,
            'is_active' => true
        ]);

        // Assign admin to all stores
        $admin->stores()->attach(Store::pluck('id'));

        // Create commercial user
        $commercial = User::create([
            'name' => 'Sales Agent',
            'email' => 'sales@example.com',
            'password' => Hash::make('sales123'),
            'phone' => '+1234567891',
            'role_id' => Role::where('name', 'commercial')->first()->id,
            'is_active' => true
        ]);

        // Assign commercial to main store
        $commercial->stores()->attach(Store::first()->id);

        // Create warehouse user
        $warehouse = User::create([
            'name' => 'Warehouse Manager',
            'email' => 'warehouse@example.com',
            'password' => Hash::make('warehouse123'),
            'phone' => '+1234567892',
            'role_id' => Role::where('name', 'magasinier')->first()->id,
            'is_active' => true
        ]);

        // Assign warehouse to all stores
        $warehouse->stores()->attach(Store::pluck('id'));
    }
}