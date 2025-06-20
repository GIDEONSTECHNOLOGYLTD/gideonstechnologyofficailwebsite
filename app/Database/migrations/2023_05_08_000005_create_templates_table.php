<?php

namespace App\Database\Migrations;

use App\Database\Migration;

class CreateTemplatesTable extends Migration
{
    /**
     * Run the migration
     * 
     * @return void
     */
    public function upImplementation(): void
    {
        $this->execute("CREATE TABLE IF NOT EXISTS templates (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            slug TEXT NOT NULL UNIQUE,
            description TEXT,
            content TEXT NOT NULL,
            thumbnail TEXT,
            category_id INTEGER,
            price REAL DEFAULT 0,
            featured INTEGER DEFAULT 0,
            status TEXT NOT NULL DEFAULT 'active',
            downloads INTEGER DEFAULT 0,
            tags TEXT,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL
        )");
        
        // Create template categories table
        $this->execute("CREATE TABLE IF NOT EXISTS template_categories (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            slug TEXT NOT NULL UNIQUE,
            description TEXT,
            parent_id INTEGER,
            sort_order INTEGER DEFAULT 0,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL
        )");
        
        // Create indexes for frequently queried columns
        $this->execute("CREATE INDEX IF NOT EXISTS idx_templates_slug ON templates (slug)");
        $this->execute("CREATE INDEX IF NOT EXISTS idx_templates_category_id ON templates (category_id)");
        $this->execute("CREATE INDEX IF NOT EXISTS idx_templates_featured ON templates (featured)");
        $this->execute("CREATE INDEX IF NOT EXISTS idx_templates_status ON templates (status)");
        
        $this->execute("CREATE INDEX IF NOT EXISTS idx_template_categories_slug ON template_categories (slug)");
        $this->execute("CREATE INDEX IF NOT EXISTS idx_template_categories_parent_id ON template_categories (parent_id)");
    }
    
    /**
     * Reverse the migration
     * 
     * @return void
     */
    public function downImplementation(): void
    {
        // Drop indexes first
        $this->execute("DROP INDEX IF EXISTS idx_templates_slug");
        $this->execute("DROP INDEX IF EXISTS idx_templates_category_id");
        $this->execute("DROP INDEX IF EXISTS idx_templates_featured");
        $this->execute("DROP INDEX IF EXISTS idx_templates_status");
        
        $this->execute("DROP INDEX IF EXISTS idx_template_categories_slug");
        $this->execute("DROP INDEX IF EXISTS idx_template_categories_parent_id");
        
        // Drop tables
        $this->execute("DROP TABLE IF EXISTS templates");
        $this->execute("DROP TABLE IF EXISTS template_categories");
    }
}
