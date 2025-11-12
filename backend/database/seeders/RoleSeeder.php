<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            [
                'name' => 'admin',
                'display_name' => 'Administrator',
                'description' => 'Full system access'
            ],
            [
                'name' => 'commercial',
                'display_name' => 'Commercial Agent',
                'description' => 'Sales and customer management'
            ],
            [
                'name' => 'magasinier',
                'display_name' => 'Warehouse Manager',
                'description' => 'Stock and purchase management'
            ]
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}