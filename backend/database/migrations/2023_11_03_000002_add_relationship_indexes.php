<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipIndexes extends Migration
{
    public function up()
    {
        // Sale items relationship indexes
        Schema::table('sale_items', function (Blueprint $table) {
            $table->index(['sale_id', 'product_id']);
        });

        // Purchase items relationship indexes
        Schema::table('purchase_items', function (Blueprint $table) {
            $table->index(['purchase_id', 'product_id']);
        });

        // Stock movements relationship indexes for reports
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->index(['product_id', 'store_id', 'created_at']);
            $table->index(['type', 'reference_id']);
        });

        // User stores relationship index
        Schema::table('user_store', function (Blueprint $table) {
            $table->index(['user_id', 'store_id']);
        });
    }

    public function down()
    {
        // Drop sale items indexes
        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropIndex(['sale_id', 'product_id']);
        });

        // Drop purchase items indexes
        Schema::table('purchase_items', function (Blueprint $table) {
            $table->dropIndex(['purchase_id', 'product_id']);
        });

        // Drop stock movements indexes
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropIndex(['product_id', 'store_id', 'created_at']);
            $table->dropIndex(['type', 'reference_id']);
        });

        // Drop user stores index
        Schema::table('user_store', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'store_id']);
        });
    }
}