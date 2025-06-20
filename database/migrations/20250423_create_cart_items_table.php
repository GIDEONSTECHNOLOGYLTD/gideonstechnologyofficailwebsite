<?php

namespace Database\Migrations;

use Database\Migration;

class CreateCartItemsTable extends Migration {
    public function up() {
        $this->schema->createRaw([
            "CREATE TABLE IF NOT EXISTS cart_items (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                product_id INTEGER NOT NULL,
                quantity INTEGER DEFAULT 1,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL,
                FOREIGN KEY (user_id) REFERENCES users(id),
                FOREIGN KEY (product_id) REFERENCES products(id)
            )",

            "CREATE INDEX IF NOT EXISTS idx_cart_items_user_id ON cart_items(user_id)",
            "CREATE INDEX IF NOT EXISTS idx_cart_items_product_id ON cart_items(product_id)"
        ]);
    }

    public function down() {
        $this->schema->drop(['cart_items']);
    }
}
