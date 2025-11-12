<?php

namespace Tests;

use App\Models\User;
use App\Models\Role;
use App\Models\Store;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Sanctum\Sanctum;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutExceptionHandling();
    }

    protected function createAuthenticatedAdmin()
    {
        $role = Role::create([
            'name' => 'admin',
            'display_name' => 'Administrator',
            'description' => 'Full system access'
        ]);

        $user = User::create([
            'name' => 'Test Admin',
            'email' => 'test@admin.com',
            'password' => bcrypt('password'),
            'role_id' => $role->id,
            'is_active' => true
        ]);

        Sanctum::actingAs($user);

        return $user;
    }

    protected function createTestStore()
    {
        return Store::create([
            'name' => 'Test Store',
            'location' => 'Test Location',
            'phone' => '1234567890',
            'email' => 'test@store.com',
            'is_active' => true
        ]);
    }
}