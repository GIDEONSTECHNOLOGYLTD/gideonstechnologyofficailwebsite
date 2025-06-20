<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    public function test_example()
    {
        $this->assertTrue(true);
    }

    public function test_database_connection()
    {
        $db = \App\Core\Database::getInstance();
        $this->assertNotNull($db);
    }
}
