<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cash_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('cash_register_id');
            $table->decimal('amount', 15, 2);
            $table->string('transaction_type', 20);
            $table->uuid('reference_id')->nullable();
            $table->string('reference_type', 50)->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('cash_register_id')->references('id')->on('cash_registers')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('cash_transactions');
    }
};