<?php
namespace Database\Migrations;

use Database\Migration;

class AddPerformanceIndexes extends Migration {
    public function up() {
        try {
            // Add indexes for users table
            $this->schema->createRaw([
                "CREATE INDEX idx_users_email ON users(email)",
                "CREATE INDEX idx_users_is_active ON users(is_active)",
                "CREATE INDEX idx_users_created_at ON users(created_at)"
            ]);

            // Add indexes for orders table
            $this->schema->createRaw([
                "CREATE INDEX idx_orders_status ON orders(status)",
                "CREATE INDEX idx_orders_total ON orders(total_amount)",
                "CREATE INDEX idx_orders_created ON orders(created_at)"
            ]);

            // Add indexes for order_items table
            $this->schema->createRaw([
                "CREATE INDEX idx_order_items_product ON order_items(product_id)",
                "CREATE INDEX idx_order_items_quantity ON order_items(quantity)",
                "CREATE INDEX idx_order_items_price ON order_items(unit_price)",
                "CREATE INDEX idx_order_items_created ON order_items(created_at)"
            ]);
        } catch (\Exception $e) {
            throw new \Exception("Error adding performance indexes: " . $e->getMessage());
        }
    }

    public function down() {
        try {
            // Drop all indexes
            $this->schema->createRaw([
                // Users
                "DROP INDEX IF EXISTS idx_users_email ON users",
                "DROP INDEX IF EXISTS idx_users_is_active ON users",
                "DROP INDEX IF EXISTS idx_users_created_at ON users",

                // Orders
                "DROP INDEX IF EXISTS idx_orders_status ON orders",
                "DROP INDEX IF EXISTS idx_orders_total ON orders",
                "DROP INDEX IF EXISTS idx_orders_created ON orders",

                // Order Items
                "DROP INDEX IF EXISTS idx_order_items_product ON order_items",
                "DROP INDEX IF EXISTS idx_order_items_quantity ON order_items",
                "DROP INDEX IF EXISTS idx_order_items_price ON order_items",
                "DROP INDEX IF EXISTS idx_order_items_created ON order_items"
            ]);
        } catch (\Exception $e) {
            throw new \Exception("Error dropping performance indexes: " . $e->getMessage());
        }
    }
}
