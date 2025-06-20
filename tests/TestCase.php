<?php

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use PDO;
use PDOStatement;

/**
 * Base TestCase for all tests
 */
class TestCase extends BaseTestCase
{
    /**
     * Create a mock PDO instance
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|PDO
     */
    protected function createMockPDO()
    {
        return $this->getMockBuilder(PDO::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
    
    /**
     * Create a mock PDOStatement instance
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|PDOStatement
     */
    protected function createMockPDOStatement()
    {
        return $this->getMockBuilder(PDOStatement::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
    
    /**
     * Set up a mock PDO with prepared statement
     *
     * @param PDO $mockPdo The mock PDO instance
     * @param PDOStatement $mockStatement The mock PDOStatement instance
     * @param string $sql The expected SQL query
     * @return void
     */
    protected function setUpMockPdoWithStatement($mockPdo, $mockStatement, $sql)
    {
        $mockPdo->expects($this->once())
            ->method('prepare')
            ->with($this->equalTo($sql))
            ->willReturn($mockStatement);
    }
}
