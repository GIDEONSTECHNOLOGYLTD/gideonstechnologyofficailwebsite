<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Repositories\BaseRepository;
use App\Core\DatabaseErrorHandler;
use PDO;
use PDOException;

/**
 * Test for BaseRepository
 */
class BaseRepositoryTest extends TestCase
{
    /**
     * @var BaseRepository|
     */
    protected $repository;
    
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|PDO
     */
    protected $mockPdo;
    
    /**
     * Set up the test
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->mockPdo = $this->createMockPDO();
        
        // Create a concrete implementation of the abstract BaseRepository
        $this->repository = new class($this->mockPdo) extends BaseRepository {
            protected $table = 'test_table';
            protected $primaryKey = 'id';
            
            public function __construct(PDO $pdo) {
                parent::__construct($pdo);
            }
        };
    }
    
    /**
     * Test finding a record by ID
     */
    public function testFind()
    {
        $mockStatement = $this->createMockPDOStatement();
        $expectedData = ['id' => 1, 'name' => 'Test Item'];
        
        $this->setUpMockPdoWithStatement(
            $this->mockPdo, 
            $mockStatement, 
            "SELECT * FROM test_table WHERE id = :id LIMIT 1"
        );
        
        $mockStatement->expects($this->once())
            ->method('bindValue')
            ->with(':id', 1);
            
        $mockStatement->expects($this->once())
            ->method('execute');
            
        $mockStatement->expects($this->once())
            ->method('fetch')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn($expectedData);
        
        $result = $this->repository->find(1);
        
        $this->assertEquals($expectedData, $result);
    }
    
    /**
     * Test getting all records
     */
    public function testAll()
    {
        $mockStatement = $this->createMockPDOStatement();
        $expectedData = [
            ['id' => 1, 'name' => 'Test Item 1'],
            ['id' => 2, 'name' => 'Test Item 2']
        ];
        
        $this->setUpMockPdoWithStatement(
            $this->mockPdo, 
            $mockStatement, 
            "SELECT * FROM test_table"
        );
        
        $mockStatement->expects($this->once())
            ->method('execute');
            
        $mockStatement->expects($this->once())
            ->method('fetchAll')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn($expectedData);
        
        $result = $this->repository->all();
        
        $this->assertEquals($expectedData, $result);
    }
    
    /**
     * Test creating a record
     */
    public function testCreate()
    {
        $mockStatement = $this->createMockPDOStatement();
        $data = ['name' => 'New Test Item'];
        
        $this->setUpMockPdoWithStatement(
            $this->mockPdo, 
            $mockStatement, 
            "INSERT INTO test_table (name, created_at, updated_at) VALUES (:name, :created_at, :updated_at)"
        );
        
        $mockStatement->expects($this->exactly(3))
            ->method('bindValue');
            
        $mockStatement->expects($this->once())
            ->method('execute');
            
        $this->mockPdo->expects($this->once())
            ->method('lastInsertId')
            ->willReturn('1');
        
        $result = $this->repository->create($data);
        
        $this->assertEquals('1', $result);
    }
    
    /**
     * Test updating a record
     */
    public function testUpdate()
    {
        $mockStatement = $this->createMockPDOStatement();
        $data = ['name' => 'Updated Test Item'];
        
        $this->setUpMockPdoWithStatement(
            $this->mockPdo, 
            $mockStatement, 
            "UPDATE test_table SET name = :name, updated_at = :updated_at WHERE id = :id"
        );
        
        $mockStatement->expects($this->exactly(3))
            ->method('bindValue');
            
        $mockStatement->expects($this->once())
            ->method('execute')
            ->willReturn(true);
        
        $result = $this->repository->update(1, $data);
        
        $this->assertTrue($result);
    }
    
    /**
     * Test deleting a record
     */
    public function testDelete()
    {
        $mockStatement = $this->createMockPDOStatement();
        
        $this->setUpMockPdoWithStatement(
            $this->mockPdo, 
            $mockStatement, 
            "DELETE FROM test_table WHERE id = :id"
        );
        
        $mockStatement->expects($this->once())
            ->method('bindValue')
            ->with(':id', 1);
            
        $mockStatement->expects($this->once())
            ->method('execute')
            ->willReturn(true);
        
        $result = $this->repository->delete(1);
        
        $this->assertTrue($result);
    }
    
    /**
     * Test error handling when an exception occurs
     */
    public function testErrorHandling()
    {
        $mockStatement = $this->createMockPDOStatement();
        $exception = new PDOException('Database error');
        
        $this->mockPdo->expects($this->once())
            ->method('prepare')
            ->willThrowException($exception);
        
        $result = $this->repository->find(1);
        
        $this->assertNull($result);
    }
}
