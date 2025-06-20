<?php
namespace Database\Migrations;

use Database\Migration;

class CreateWebDevTables extends Migration {
    public function up() {
        $this->schema->createRaw([
            "CREATE TABLE IF NOT EXISTS services (
                id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
                category VARCHAR(100) NOT NULL,
                title VARCHAR(100) NOT NULL,
                slug VARCHAR(100) NOT NULL,
                description TEXT NULL,
                price DECIMAL(10,2) NOT NULL,
                is_featured TINYINT(1) DEFAULT 0,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL,
                UNIQUE KEY (slug)
            )",

            "CREATE TABLE IF NOT EXISTS projects (
                id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
                category VARCHAR(100) NOT NULL,
                title VARCHAR(100) NOT NULL,
                slug VARCHAR(100) NOT NULL,
                description TEXT NULL,
                technologies TEXT NULL,
                features TEXT NULL,
                images TEXT NULL,
                is_featured TINYINT(1) DEFAULT 0,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL,
                UNIQUE KEY (slug)
            )",

            "CREATE TABLE IF NOT EXISTS quotes (
                id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
                name VARCHAR(100) NOT NULL,
                email VARCHAR(100) NOT NULL,
                phone VARCHAR(20) NULL,
                company VARCHAR(100) NULL,
                requirements TEXT NOT NULL,
                budget DECIMAL(10,2) NULL,
                timeline VARCHAR(50) NULL,
                status VARCHAR(20) DEFAULT 'pending',
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL
            )"
        ]);
    }

    public function down() {
        $this->schema->dropIfExists('quotes');
        $this->schema->dropIfExists('projects');
        $this->schema->dropIfExists('services');
    }
}
