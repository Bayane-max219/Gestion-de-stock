<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('stock_transfers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('from_store_id');
            $table->uuid('to_store_id');
            $table->uuid('product_id');
            $table->decimal('quantity', 10, 2);
            $table->string('status');
            $table->string('reference_number')->nullable();
            $table->uuid('initiated_by');
            $table->uuid('completed_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->foreign('from_store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->foreign('to_store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('initiated_by')->references('id')->on('users');
            $table->foreign('completed_by')->references('id')->on('users');

            // Indexes
            $table->index(['from_store_id', 'to_store_id']);
            $table->index('status');
            $table->index('reference_number');
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_transfers');
    }
};