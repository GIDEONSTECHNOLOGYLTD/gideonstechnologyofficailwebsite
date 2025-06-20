<?php

namespace Database\Migrations;

use Database\Migration;
use Database\Schema;
use Database\Blueprint;

class CreateProductsTable extends Migration {
    public function up() {
        $this->schema->create('products', function($table) {
            $table->id();
            $table->string('category');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->integer('stock')->default(0);
            $table->boolean('is_featured')->default(0);
            $table->boolean('is_active')->default(1);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down() {
        $this->schema->dropIfExists('products');
    }
}
