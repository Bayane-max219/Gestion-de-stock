<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('category');
            $table->decimal('buy_price', 10, 2);
            $table->decimal('sell_price', 10, 2);
            $table->integer('stock')->default(0);
            $table->string('barcode')->nullable();
            $table->text('photo')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'category']);
            $table->index('barcode');
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
};
