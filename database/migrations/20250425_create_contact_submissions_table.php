<?php

namespace Database\Migrations;

use Database\Migration;
use Database\Schema;
use Database\Blueprint;

class CreateContactSubmissionsTable extends Migration {
    public function up() {
        $this->schema->create('contact_submissions', function($table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('service');
            $table->text('message');
            $table->string('file_path')->nullable();
            $table->enum('status', ['new', 'in_progress', 'completed'])->default('new');
            $table->timestamps();
        });
    }

    public function down() {
        $this->schema->dropIfExists('contact_submissions');
    }
}
