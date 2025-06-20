<?php
namespace Database\Migrations;

use Database\Migration;

class AddImageToProductsTable extends Migration {
    public function up() {
        // Add image column to products
        $this->schema->table('products', function($table) {
            $table->string('image')->nullable();
        });
    }

    public function down() {
        // Drop image column
        $this->schema->table('products', function($table) {
            $table->dropColumn('image');
        });
    }
}
