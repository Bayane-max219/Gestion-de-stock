<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('business_name');
            $table->enum('business_type', [
                'quincaillerie', 'pharmacie', 'epicerie', 
                'superette', 'depot', 'autre'
            ]);
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->boolean('accept_terms')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};
