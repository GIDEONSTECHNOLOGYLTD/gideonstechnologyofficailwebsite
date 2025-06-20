<?php
namespace Database\Migrations;

use Database\Migration;
use Database\Schema;
use Database\Blueprint;

class CreateWalletsAndPaymentMethods extends Migration {
    public function up() {
        $this->schema->createRaw([
            "CREATE TABLE IF NOT EXISTS wallets (
                id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
                user_id BIGINT UNSIGNED NOT NULL,
                balance DECIMAL(10,2) DEFAULT 0,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            )",

            "CREATE TABLE IF NOT EXISTS payment_methods (
                id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
                user_id BIGINT UNSIGNED NOT NULL,
                type VARCHAR(255) NOT NULL,
                data JSON NULL,
                is_default TINYINT(1) DEFAULT 0,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            )"
        ]);
    }

    public function down() {
        $this->schema->dropIfExists('payment_methods');
        $this->schema->dropIfExists('wallets');
    }
}
