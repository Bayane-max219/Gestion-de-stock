<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Sale;
use App\Models\Store;
use App\Models\Product;
use App\Models\User;
use App\Services\InvoiceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

class InvoiceGenerationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
    }

    /** @test */
    public function it_generates_invoice_with_unique_number()
    {
        // Create test data
        $store = Store::factory()->create(['name' => 'Test Store']);
        $product = Product::factory()->create();
        $sale = Sale::factory()
            ->for($store)
            ->has(SaleItem::factory()->for($product))
            ->create();

        // Generate invoice
        $service = new InvoiceService($sale);
        $path = $service->generate();

        // Assert invoice was generated
        Storage::disk('local')->assertExists($path);

        // Assert invoice number format
        $this->assertMatchesRegularExpression(
            '/TES\d{6}\d{4}/', // TEStoreYYMMNNNN
            $sale->fresh()->invoice_number
        );
    }

    /** @test */
    public function it_maintains_unique_invoice_numbers_per_store()
    {
        $store = Store::factory()->create(['name' => 'Test Store']);
        $product = Product::factory()->create();

        // Create multiple sales
        $sale1 = Sale::factory()
            ->for($store)
            ->has(SaleItem::factory()->for($product))
            ->create();

        $sale2 = Sale::factory()
            ->for($store)
            ->has(SaleItem::factory()->for($product))
            ->create();

        // Generate invoices
        $service1 = new InvoiceService($sale1);
        $service2 = new InvoiceService($sale2);

        $path1 = $service1->generate();
        $path2 = $service2->generate();

        // Refresh models
        $sale1->refresh();
        $sale2->refresh();

        // Assert different invoice numbers
        $this->assertNotEquals($sale1->invoice_number, $sale2->invoice_number);

        // Assert sequential numbers
        $num1 = intval(substr($sale1->invoice_number, -4));
        $num2 = intval(substr($sale2->invoice_number, -4));
        $this->assertEquals($num1 + 1, $num2);
    }
}