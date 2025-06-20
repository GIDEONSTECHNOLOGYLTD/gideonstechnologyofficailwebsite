<?php

namespace Database\Migrations;

use Database\Migration;
use Database\Schema;
use Database\Blueprint;

class CreateProjectsTable extends Migration {
    public function up() {
        $this->schema->create('projects', function($table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('service_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'in-progress', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('service_id')->references('id')->on('services');
        });
    }

    public function down() {
        $this->schema->dropIfExists('projects');
    }
}
