<?php

namespace Database\Migrations;

use Database\Migration;
use Database\Schema;
use Database\Blueprint;

class CreatePaymentMethodsTable extends Migration {
    public function up() {
        $this->schema->createRaw([
            "CREATE TABLE IF NOT EXISTS payment_methods (
                id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
                user_id BIGINT UNSIGNED NOT NULL,
                type ENUM('card', 'bank_account') NOT NULL,
                provider ENUM('stripe', 'paystack', 'paypal') NOT NULL,
                token VARCHAR(255) NOT NULL,
                last_four VARCHAR(4) NULL,
                expiry_month VARCHAR(2) NULL,
                expiry_year VARCHAR(4) NULL,
                is_default TINYINT(1) DEFAULT 0,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL,
                FOREIGN KEY (user_id) REFERENCES users(id)
            )"
        ]);
    }

    public function down() {
        $this->schema->dropIfExists('payment_methods');
    }
}
