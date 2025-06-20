<?php
namespace Database\Migrations;

use Database\Migration;

class CreateAuthTables extends Migration {
    public function up() {
        $this->schema->createRaw([
            "CREATE TABLE IF NOT EXISTS login_attempts (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                ip_address TEXT NOT NULL,
                email TEXT NOT NULL,
                success INTEGER DEFAULT 0,
                attempts INTEGER DEFAULT 1,
                last_attempt TIMESTAMP NULL,
                blocked_until TIMESTAMP NULL,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL
            )",

            "CREATE TABLE IF NOT EXISTS password_resets (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                email TEXT NOT NULL,
                token TEXT NOT NULL,
                used INTEGER DEFAULT 0,
                expires_at TIMESTAMP NULL,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL
            )"
        ]);
    }

    public function down() {
        $this->schema->drop(['password_resets', 'login_attempts']);
    }
}
