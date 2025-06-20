<?php

namespace App\Core\Database;

abstract class Migration
{
    /**
     * The schema builder instance.
     *
     * @var \App\Core\Database\Schema
     */
    protected $schema;

    /**
     * Create a new migration instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Create a PDO instance first and pass it to Schema
        $dbPath = defined('BASE_PATH') ? BASE_PATH . '/database/gtech.db' : __DIR__ . '/../../../database/gtech.db';
        $pdo = new \PDO('sqlite:' . $dbPath);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        
        $this->schema = new Schema($pdo);
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    abstract public function up();

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    abstract public function down();
}
