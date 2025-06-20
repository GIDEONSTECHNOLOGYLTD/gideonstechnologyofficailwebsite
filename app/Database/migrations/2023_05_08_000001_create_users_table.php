<?php

namespace App\Database\Migrations;

use App\Database\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migration
     * 
     * @return void
     */
    public function upImplementation(): void
    {
        $this->execute("CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            email TEXT NOT NULL UNIQUE,
            username TEXT UNIQUE,
            phone TEXT,
            password TEXT NOT NULL,
            role TEXT NOT NULL DEFAULT 'client',
            status TEXT NOT NULL DEFAULT 'active',
            avatar TEXT,
            bio TEXT,
            remember_token TEXT,
            email_verified_at DATETIME,
            last_login DATETIME,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL
        )");
        
        // Create indexes for frequently queried columns
        $this->execute("CREATE INDEX IF NOT EXISTS idx_users_email ON users (email)");
        $this->execute("CREATE INDEX IF NOT EXISTS idx_users_username ON users (username)");
        $this->execute("CREATE INDEX IF NOT EXISTS idx_users_role ON users (role)");
        $this->execute("CREATE INDEX IF NOT EXISTS idx_users_status ON users (status)");
    }
    
    /**
     * Reverse the migration
     * 
     * @return void
     */
    public function downImplementation(): void
    {
        // Drop indexes first
        $this->execute("DROP INDEX IF EXISTS idx_users_email");
        $this->execute("DROP INDEX IF EXISTS idx_users_username");
        $this->execute("DROP INDEX IF EXISTS idx_users_role");
        $this->execute("DROP INDEX IF EXISTS idx_users_status");
        
        // Drop the table
        $this->execute("DROP TABLE IF EXISTS users");
    }
}
