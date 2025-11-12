<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('customer_name');
            $table->decimal('total', 10, 2);
            $table->enum('payment_method', ['cash', 'credit', 'card'])->default('cash');
            $table->timestamp('sale_date');
            $table->timestamps();
            
            $table->index(['user_id', 'sale_date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('sales');
    }
};
