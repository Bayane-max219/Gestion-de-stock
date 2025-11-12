<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPerformanceIndexes extends Migration
{
    public function up()
    {
        // Products table indexes
        Schema::table('products', function (Blueprint $table) {
            $table->index('sku');
            $table->index('category_id');
            $table->index('created_at');
        });

        // Sales table indexes
        Schema::table('sales', function (Blueprint $table) {
            $table->index('invoice_number');
            $table->index('store_id');
            $table->index('client_id');
            $table->index('created_at');
        });

        // Purchases table indexes
        Schema::table('purchases', function (Blueprint $table) {
            $table->index('reference_number');
            $table->index('store_id');
            $table->index('supplier_id');
            $table->index('created_at');
        });

        // Stock movements table indexes
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->index(['store_id', 'product_id']);
            $table->index('created_at');
        });

        // Store products table indexes
        Schema::table('store_products', function (Blueprint $table) {
            $table->index(['store_id', 'product_id']);
        });
    }

    public function down()
    {
        // Products table
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['sku']);
            $table->dropIndex(['category_id']);
            $table->dropIndex(['created_at']);
        });

        // Sales table
        Schema::table('sales', function (Blueprint $table) {
            $table->dropIndex(['invoice_number']);
            $table->dropIndex(['store_id']);
            $table->dropIndex(['client_id']);
            $table->dropIndex(['created_at']);
        });

        // Purchases table
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropIndex(['reference_number']);
            $table->dropIndex(['store_id']);
            $table->dropIndex(['supplier_id']);
            $table->dropIndex(['created_at']);
        });

        // Stock movements table
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropIndex(['store_id', 'product_id']);
            $table->dropIndex(['created_at']);
        });

        // Store products table
        Schema::table('store_products', function (Blueprint $table) {
            $table->dropIndex(['store_id', 'product_id']);
        });
    }
}