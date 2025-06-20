<?php

namespace Database\Migrations;

use Database\Migration;

class CreateMigrationTracking extends Migration {
    public function up() {
        $this->schema->createRaw([
            "CREATE TABLE IF NOT EXISTS migration_history (
                id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
                migration_name VARCHAR(255) NOT NULL,
                batch INT NOT NULL,
                executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                status ENUM('success', 'failed') NOT NULL DEFAULT 'success',
                error_message TEXT
            )",

            "CREATE INDEX idx_migration_name ON migration_history(migration_name)",
            "CREATE INDEX idx_batch ON migration_history(batch)"
        ]);
    }

    public function down() {
        $this->schema->createRaw([
            "DROP INDEX IF EXISTS idx_migration_name ON migration_history",
            "DROP INDEX IF EXISTS idx_batch ON migration_history"
        ]);
        $this->schema->dropIfExists('migration_history');
    }
}
