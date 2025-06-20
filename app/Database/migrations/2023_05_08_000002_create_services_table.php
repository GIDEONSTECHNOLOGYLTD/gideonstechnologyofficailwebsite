<?php

namespace App\Database\Migrations;

use App\Database\Migration;

class CreateServicesTable extends Migration
{
    /**
     * Run the migration
     * 
     * @return void
     */
    public function upImplementation(): void
    {
        $this->execute("CREATE TABLE IF NOT EXISTS services (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            slug TEXT NOT NULL UNIQUE,
            description TEXT NOT NULL,
            icon TEXT,
            price REAL,
            featured INTEGER DEFAULT 0,
            category_id INTEGER,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL
        )");
        
        // Create indexes for frequently queried columns
        $this->execute("CREATE INDEX IF NOT EXISTS idx_services_slug ON services (slug)");
        $this->execute("CREATE INDEX IF NOT EXISTS idx_services_featured ON services (featured)");
        $this->execute("CREATE INDEX IF NOT EXISTS idx_services_category_id ON services (category_id)");
    }
    
    /**
     * Reverse the migration
     * 
     * @return void
     */
    public function downImplementation(): void
    {
        // Drop indexes first
        $this->execute("DROP INDEX IF EXISTS idx_services_slug");
        $this->execute("DROP INDEX IF EXISTS idx_services_featured");
        $this->execute("DROP INDEX IF EXISTS idx_services_category_id");
        
        // Drop the table
        $this->execute("DROP TABLE IF EXISTS services");
    }
}
