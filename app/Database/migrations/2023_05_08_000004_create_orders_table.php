<?php

namespace App\Database\Migrations;

use App\Database\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migration
     * 
     * @return void
     */
    public function upImplementation(): void
    {
        $this->execute("CREATE TABLE IF NOT EXISTS orders (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            order_number TEXT NOT NULL UNIQUE,
            user_id INTEGER,
            status TEXT NOT NULL DEFAULT 'pending',
            total REAL NOT NULL DEFAULT 0,
            subtotal REAL NOT NULL DEFAULT 0,
            tax REAL DEFAULT 0,
            shipping REAL DEFAULT 0,
            discount REAL DEFAULT 0,
            coupon_code TEXT,
            shipping_address TEXT,
            billing_address TEXT,
            payment_method TEXT,
            payment_status TEXT DEFAULT 'pending',
            notes TEXT,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL
        )");
        
        // Create order items table
        $this->execute("CREATE TABLE IF NOT EXISTS order_items (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            order_id INTEGER NOT NULL,
            product_id INTEGER,
            service_id INTEGER,
            name TEXT NOT NULL,
            price REAL NOT NULL,
            quantity INTEGER NOT NULL DEFAULT 1,
            options TEXT,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
        )");
        
        // Create indexes for frequently queried columns
        $this->execute("CREATE INDEX IF NOT EXISTS idx_orders_order_number ON orders (order_number)");
        $this->execute("CREATE INDEX IF NOT EXISTS idx_orders_user_id ON orders (user_id)");
        $this->execute("CREATE INDEX IF NOT EXISTS idx_orders_status ON orders (status)");
        $this->execute("CREATE INDEX IF NOT EXISTS idx_orders_payment_status ON orders (payment_status)");
        
        $this->execute("CREATE INDEX IF NOT EXISTS idx_order_items_order_id ON order_items (order_id)");
        $this->execute("CREATE INDEX IF NOT EXISTS idx_order_items_product_id ON order_items (product_id)");
        $this->execute("CREATE INDEX IF NOT EXISTS idx_order_items_service_id ON order_items (service_id)");
    }
    
    /**
     * Reverse the migration
     * 
     * @return void
     */
    public function downImplementation(): void
    {
        // Drop indexes first
        $this->execute("DROP INDEX IF EXISTS idx_orders_order_number");
        $this->execute("DROP INDEX IF EXISTS idx_orders_user_id");
        $this->execute("DROP INDEX IF EXISTS idx_orders_status");
        $this->execute("DROP INDEX IF EXISTS idx_orders_payment_status");
        
        $this->execute("DROP INDEX IF EXISTS idx_order_items_order_id");
        $this->execute("DROP INDEX IF EXISTS idx_order_items_product_id");
        $this->execute("DROP INDEX IF EXISTS idx_order_items_service_id");
        
        // Drop tables (order matters due to foreign key constraints)
        $this->execute("DROP TABLE IF EXISTS order_items");
        $this->execute("DROP TABLE IF EXISTS orders");
    }
}
