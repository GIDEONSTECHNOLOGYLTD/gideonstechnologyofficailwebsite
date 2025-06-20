<?php

namespace Database\Migrations;

use Database\Migration;

class AddWishlistAndCouponsTables extends Migration {
    public function up() {
        // Create wishlists table
        $this->schema->createRaw([
            "CREATE TABLE IF NOT EXISTS wishlists (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER,
                product_id INTEGER,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
            )"
        ]);

        // Create coupons table
        $this->schema->createRaw([
            "CREATE TABLE IF NOT EXISTS coupons (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                code TEXT UNIQUE NOT NULL,
                type TEXT CHECK(type IN ('fixed', 'percentage')) NOT NULL,
                value DECIMAL(10,2) NOT NULL,
                starts_at TIMESTAMP NULL,
                expires_at TIMESTAMP NULL,
                usage_limit INTEGER NULL,
                used_count INTEGER DEFAULT 0,
                is_active INTEGER DEFAULT 1,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL
            )"
        ]);
    }

    public function down() {
        $this->schema->dropIfExists('wishlists');
        $this->schema->dropIfExists('coupons');
    }
}
