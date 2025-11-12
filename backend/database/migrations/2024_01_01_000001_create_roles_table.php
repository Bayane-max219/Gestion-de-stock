<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Insert default roles
        DB::table('roles')->insert([
            ['name' => 'admin', 'description' => 'Full system access'],
            ['name' => 'commercial', 'description' => 'Sales and payment management'],
            ['name' => 'magasinier', 'description' => 'Inventory management'],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('roles');
    }
};