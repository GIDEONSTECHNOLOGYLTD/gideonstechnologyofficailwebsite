<?php

namespace App\Database\Migrations;

use App\Database\Migration;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migration
     * 
     * @return void
     */
    public function upImplementation(): void
    {
        $this->execute("CREATE TABLE IF NOT EXISTS categories (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            slug TEXT NOT NULL UNIQUE,
            description TEXT,
            parent_id INTEGER,
            type TEXT NOT NULL DEFAULT 'product',
            icon TEXT,
            image TEXT,
            sort_order INTEGER DEFAULT 0,
            status TEXT NOT NULL DEFAULT 'active',
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL
        )");
        
        // Create indexes for frequently queried columns
        $this->execute("CREATE INDEX IF NOT EXISTS idx_categories_slug ON categories (slug)");
        $this->execute("CREATE INDEX IF NOT EXISTS idx_categories_parent_id ON categories (parent_id)");
        $this->execute("CREATE INDEX IF NOT EXISTS idx_categories_type ON categories (type)");
        $this->execute("CREATE INDEX IF NOT EXISTS idx_categories_status ON categories (status)");
    }
    
    /**
     * Reverse the migration
     * 
     * @return void
     */
    public function downImplementation(): void
    {
        // Drop indexes first
        $this->execute("DROP INDEX IF EXISTS idx_categories_slug");
        $this->execute("DROP INDEX IF EXISTS idx_categories_parent_id");
        $this->execute("DROP INDEX IF EXISTS idx_categories_type");
        $this->execute("DROP INDEX IF EXISTS idx_categories_status");
        
        // Drop the table
        $this->execute("DROP TABLE IF EXISTS categories");
    }
}
