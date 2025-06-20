<?php

namespace Database\Migrations;

use Database\Migration;
use Database\Schema;
use Database\Blueprint;

class CreateUsersTable extends Migration {
    public function up() {
        $this->schema->createRaw([
            "CREATE TABLE IF NOT EXISTS users (
                id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
                first_name VARCHAR(255) NOT NULL,
                last_name VARCHAR(255) NOT NULL,
                email VARCHAR(255) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                phone VARCHAR(255) NULL,
                avatar VARCHAR(255) NULL,
                role ENUM('user', 'admin', 'staff') DEFAULT 'user',
                is_active TINYINT(1) DEFAULT 1,
                remember_token VARCHAR(255) NULL,
                verification_token VARCHAR(255) NULL,
                email_verified_at TIMESTAMP NULL,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL
            )"
        ]);
    }

    public function down() {
        $this->schema->dropIfExists('users');
    }
}
