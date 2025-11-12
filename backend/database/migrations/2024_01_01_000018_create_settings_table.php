<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Insert default settings
        DB::table('settings')->insert([
            ['key' => 'stock_alert_threshold', 'value' => '10'],
            ['key' => 'overdue_payment_days', 'value' => '30'],
            ['key' => 'company_name', 'value' => 'SmartERP Pro'],
            ['key' => 'multi_store_enabled', 'value' => 'true'],
            ['key' => 'tax_rate', 'value' => '20'],
            ['key' => 'currency', 'value' => 'MGA'],
            ['key' => 'backup_enabled', 'value' => 'true'],
            ['key' => 'backup_frequency', 'value' => 'daily'],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('settings');
    }
};