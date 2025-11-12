<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\ActivityLog;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ActivityLogTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_logs_product_creation()
    {
        $user = User::factory()->create();
        
        $this->actingAs($user);

        $product = Product::factory()->create();

        $log = ActivityLog::latest()->first();

        $this->assertEquals('create', $log->action);
        $this->assertEquals(Product::class, $log->entity_type);
        $this->assertEquals($product->id, $log->entity_id);
        $this->assertEquals($user->id, $log->user_id);
        $this->assertNull($log->old_values);
        $this->assertNotNull($log->new_values);
    }

    /** @test */
    public function it_logs_product_update()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['name' => 'Old Name']);
        
        $this->actingAs($user);

        $product->update(['name' => 'New Name']);

        $log = ActivityLog::latest()->first();

        $this->assertEquals('update', $log->action);
        $this->assertEquals(Product::class, $log->entity_type);
        $this->assertEquals($product->id, $log->entity_id);
        $this->assertEquals($user->id, $log->user_id);
        $this->assertEquals(['name' => 'Old Name'], $log->old_values);
        $this->assertEquals(['name' => 'New Name'], $log->new_values);
    }

    /** @test */
    public function it_logs_product_deletion()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        
        $this->actingAs($user);

        $product->delete();

        $log = ActivityLog::latest()->first();

        $this->assertEquals('delete', $log->action);
        $this->assertEquals(Product::class, $log->entity_type);
        $this->assertEquals($product->id, $log->entity_id);
        $this->assertEquals($user->id, $log->user_id);
    }

    /** @test */
    public function it_can_retrieve_activity_logs_with_filters()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        
        $this->actingAs($user);

        $response = $this->getJson('/api/activity-logs?' . http_build_query([
            'action' => 'create',
            'entity_type' => Product::class,
            'user_id' => $user->id,
            'start_date' => now()->subDay()->format('Y-m-d'),
            'end_date' => now()->format('Y-m-d')
        ]));

        $response->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'action',
                        'entity_type',
                        'entity_id',
                        'description',
                        'user' => ['id', 'name'],
                        'created_at'
                    ]
                ],
                'current_page',
                'last_page'
            ]);
    }

    /** @test */
    public function it_exports_activity_logs()
    {
        $user = User::factory()->create();
        
        $this->actingAs($user);

        $response = $this->get('/api/activity-logs/export');

        $response->assertSuccessful()
            ->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }
}