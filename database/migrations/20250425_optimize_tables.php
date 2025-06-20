<?php
namespace Database\Migrations;

use Database\Migration;

class OptimizeTables extends Migration {
    public function up() {
        try {
            // Add indexes for products table
            $this->schema->createRaw([
                "CREATE INDEX idx_products_name ON products(name)",
                "CREATE INDEX idx_products_price ON products(price)"
            ]);

            // Add indexes for transactions table
            $this->schema->createRaw([
                "CREATE INDEX idx_transactions_type ON transactions(type)",
                "CREATE INDEX idx_transactions_status ON transactions(status)"
            ]);

            // Add indexes for blog posts table
            $this->schema->createRaw([
                "CREATE INDEX idx_blog_posts_title ON blog_posts(title)"
            ]);

            // Add indexes for web projects table
            $this->schema->createRaw([
                "CREATE INDEX idx_web_projects_status ON web_projects(status)"
            ]);

            // Add indexes for video projects table
            $this->schema->createRaw([
                "CREATE INDEX idx_video_projects_status ON video_projects(status)"
            ]);
        } catch (\Exception $e) {
            throw new \Exception("Error optimizing tables: " . $e->getMessage());
        }
    }

    public function down() {
        try {
            // Drop all created indexes
            $this->schema->createRaw([
                // Products table indexes
                "DROP INDEX IF EXISTS idx_products_name ON products",
                "DROP INDEX IF EXISTS idx_products_price ON products",

                // Transactions table indexes
                "DROP INDEX IF EXISTS idx_transactions_type ON transactions",
                "DROP INDEX IF EXISTS idx_transactions_status ON transactions",

                // Blog posts table indexes
                "DROP INDEX IF EXISTS idx_blog_posts_title ON blog_posts",

                // Web projects table indexes
                "DROP INDEX IF EXISTS idx_web_projects_status ON web_projects",

                // Video projects table indexes
                "DROP INDEX IF EXISTS idx_video_projects_status ON video_projects"
            ]);
        } catch (\Exception $e) {
            throw new \Exception("Error dropping indexes: " . $e->getMessage());
        }
    }
}
