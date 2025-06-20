<?php

namespace App\Database\Migrations;

use App\Database\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migration
     * 
     * @return void
     */
    public function upImplementation(): void
    {
        $this->execute("CREATE TABLE IF NOT EXISTS products (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            slug TEXT NOT NULL UNIQUE,
            description TEXT,
            short_description TEXT,
            price REAL NOT NULL DEFAULT 0,
            sale_price REAL,
            sku TEXT,
            stock INTEGER DEFAULT 0,
            category_id INTEGER,
            featured INTEGER DEFAULT 0,
            status TEXT NOT NULL DEFAULT 'active',
            image TEXT,
            images TEXT,
            weight REAL,
            dimensions TEXT,
            attributes TEXT,
            sort_order INTEGER DEFAULT 0,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL
        )");
        
        // Create indexes for frequently queried columns
        $this->execute("CREATE INDEX IF NOT EXISTS idx_products_slug ON products (slug)");
        $this->execute("CREATE INDEX IF NOT EXISTS idx_products_sku ON products (sku)");
        $this->execute("CREATE INDEX IF NOT EXISTS idx_products_category_id ON products (category_id)");
        $this->execute("CREATE INDEX IF NOT EXISTS idx_products_featured ON products (featured)");
        $this->execute("CREATE INDEX IF NOT EXISTS idx_products_status ON products (status)");
    }
    
    /**
     * Reverse the migration
     * 
     * @return void
     */
    public function downImplementation(): void
    {
        // Drop indexes first
        $this->execute("DROP INDEX IF EXISTS idx_products_slug");
        $this->execute("DROP INDEX IF EXISTS idx_products_sku");
        $this->execute("DROP INDEX IF EXISTS idx_products_category_id");
        $this->execute("DROP INDEX IF EXISTS idx_products_featured");
        $this->execute("DROP INDEX IF EXISTS idx_products_status");
        
        // Drop the table
        $this->execute("DROP TABLE IF EXISTS products");
    }
}
