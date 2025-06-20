<?php

namespace Database\Migrations;

use Database\Migration;
use Database\Schema;
use Database\Blueprint;

class TemplateClassName extends Migration {
    public function up() {
        // Example 1: Using create method with Blueprint callbacks
        /*
        $this->schema->create('table_name', function($table) {
            $table->id();
            $table->string('column_name');
            $table->integer('another_column')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
        */

        // Example 2: Using raw SQL
        /*
        $this->schema->createRaw([
            "CREATE TABLE IF NOT EXISTS table_name (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                column_name TEXT NOT NULL,
                another_column INTEGER NULL,
                status TEXT CHECK(status IN ('active', 'inactive')) DEFAULT 'active',
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL
            )"
        ]);
        */
        
        // Example 3: Creating indexes after table creation
        /*
        $this->schema->createRaw([
            "CREATE INDEX IF NOT EXISTS idx_table_column ON table_name(column_name)"
        ]);
        */
    }

    public function down() {
        // Example 1: Drop table(s)
        /*
        $this->schema->dropIfExists('table_name');
        */

        // Example 2: Drop multiple tables
        /*
        $this->schema->drop(['table_name1', 'table_name2']);
        */
        
        // Example 3: Drop indexes first
        /*
        $this->schema->dropRaw([
            "DROP INDEX IF EXISTS idx_table_column"
        ]);
        */
    }
}