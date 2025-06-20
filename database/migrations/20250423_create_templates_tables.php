<?php
namespace Database\Migrations;

use Database\Migration;

class CreateTemplatesTables extends Migration {
    public function up() {
        $this->schema->createRaw([
            "CREATE TABLE IF NOT EXISTS templates (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                category TEXT NOT NULL,
                subcategory TEXT,
                description TEXT,
                features TEXT,
                preview_image TEXT,
                price DECIMAL(10,2) NOT NULL,
                demo_url TEXT,
                customization_options TEXT,
                is_active TINYINT(1) DEFAULT 1,
                is_featured TINYINT(1) DEFAULT 0,
                sort_order INT DEFAULT 0,
                purchases INT DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )",

            "CREATE TABLE IF NOT EXISTS template_purchases (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                template_id INTEGER NOT NULL,
                purchase_date DATETIME DEFAULT CURRENT_TIMESTAMP,
                status TEXT DEFAULT 'completed',
                customization_data TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (template_id) REFERENCES templates(id) ON DELETE CASCADE
            )"
        ]);

        // Create indexes after table creation
        $this->schema->createRaw([
            "CREATE INDEX IF NOT EXISTS idx_templates_name ON templates(name)",
            "CREATE INDEX IF NOT EXISTS idx_templates_category ON templates(category)",
            "CREATE INDEX IF NOT EXISTS idx_template_purchases_user_id ON template_purchases(user_id)",
            "CREATE INDEX IF NOT EXISTS idx_template_purchases_template_id ON template_purchases(template_id)"
        ]);
    }

    public function down() {
        // Drop indexes first
        $this->schema->dropRaw([
            "DROP INDEX IF EXISTS idx_templates_name",
            "DROP INDEX IF EXISTS idx_templates_category",
            "DROP INDEX IF EXISTS idx_template_purchases_user_id",
            "DROP INDEX IF EXISTS idx_template_purchases_template_id"
        ]);

        // Then drop tables
        $this->schema->drop(['template_purchases', 'templates']);
    }
}
