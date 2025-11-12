<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddFulltextIndexes extends Migration
{
    public function up()
    {
        // Products fulltext search
        DB::statement('ALTER TABLE products ADD FULLTEXT search_index (name, sku, barcode, description)');

        // Sales fulltext search
        DB::statement('ALTER TABLE sales ADD FULLTEXT search_index (invoice_number, notes)');

        // Clients fulltext search
        DB::statement('ALTER TABLE clients ADD FULLTEXT search_index (name, email, phone, address)');

        // Suppliers fulltext search
        DB::statement('ALTER TABLE suppliers ADD FULLTEXT search_index (name, email, phone, address)');
    }

    public function down()
    {
        // Remove fulltext indexes
        DB::statement('ALTER TABLE products DROP INDEX search_index');
        DB::statement('ALTER TABLE sales DROP INDEX search_index');
        DB::statement('ALTER TABLE clients DROP INDEX search_index');
        DB::statement('ALTER TABLE suppliers DROP INDEX search_index');
    }
}