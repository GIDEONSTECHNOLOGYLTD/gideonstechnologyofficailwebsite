<?php

namespace Database\Migrations;

use Database\Migration;
use App\Core\Blueprint;
use Database\Schema;

class CreateInvoicesTable extends Migration {
    public function up() {
        $this->schema->create('invoices', function($table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('service_id');
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->enum('status', ['pending', 'paid', 'cancelled'])->default('pending');
            $table->date('due_date');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('service_id')->references('id')->on('services');
        });
    }

    public function down() {
        $this->schema->dropIfExists('invoices');
    }
}
