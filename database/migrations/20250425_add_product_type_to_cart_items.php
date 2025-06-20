<?php

namespace Database\Migrations;

use App\Core\Blueprint;
use Database\Migration;
use Database\Schema;

class AddProductTypeToCartItems extends Migration {
    public function up() {
        $this->schema->table('cart_items', function($table) {
            $table->string('product_type')->nullable();
        });

        // SQLite doesn't support dropping foreign keys directly
        // We need to recreate the table without the foreign key
        $this->schema->table('cart_items', function($table) {
            $table->integer('product_id')->nullable();
        });
    }

    public function down() {
        $this->schema->table('cart_items', function($table) {
            $table->dropColumn('product_type');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }
}
