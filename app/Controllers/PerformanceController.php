<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;
use App\Core\Database;

class PerformanceController extends BaseController
{
    /**
     * Constructor
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
    }
    
    /**
     * Display performance dashboard
     */
    public function index(Request $request, Response $response): Response
    {
        // Enable query profiling for this request
        Database::enableQueryProfiling(50); // Set threshold to 50ms
        
        // Run some test queries to demonstrate profiling
        Database::query("SELECT * FROM users LIMIT 10");
        Database::query("SELECT * FROM products LIMIT 10");
        Database::query("SELECT o.*, u.email FROM orders o JOIN users u ON o.user_id = u.id LIMIT 10");
        
        // Get query stats
        $queryStats = Database::getQueryStats();
        $queries = \App\Core\QueryProfiler::getQueries();
        
        // Get system stats
        $systemStats = [
            'php_version' => PHP_VERSION,
            'memory_usage' => $this->formatBytes(memory_get_usage(true)),
            'peak_memory' => $this->formatBytes(memory_get_peak_usage(true)),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'database_type' => 'MySQL',
        ];
        
        return $this->render($response, 'admin/performance.php', [
            'title' => 'Performance Dashboard',
            'queryStats' => $queryStats,
            'queries' => $queries,
            'systemStats' => $systemStats
        ]);
    }
    
    /**
     * Format bytes to human-readable format
     */
    private function formatBytes($bytes, $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
    
    /**
     * Analyze database indexes
     */
    public function analyzeIndexes(Request $request, Response $response): Response
    {
        // Get table information
        $tables = Database::query("SHOW TABLES");
        $tableData = [];
        
        foreach ($tables as $table) {
            $tableName = reset($table);
            
            // Get indexes for this table
            $indexes = Database::query("SHOW INDEX FROM {$tableName}");
            
            // Get table structure
            $columns = Database::query("DESCRIBE {$tableName}");
            
            $tableData[$tableName] = [
                'indexes' => $indexes,
                'columns' => $columns
            ];
        }
        
        // Analyze missing indexes
        $recommendations = $this->analyzeIndexRecommendations($tableData);
        
        return $this->render($response, 'admin/indexes.php', [
            'title' => 'Database Index Analysis',
            'tableData' => $tableData,
            'recommendations' => $recommendations
        ]);
    }
    
    /**
     * Analyze and recommend indexes
     */
    private function analyzeIndexRecommendations(array $tableData): array
    {
        $recommendations = [];
        
        // Common patterns for columns that might need indexes
        $commonPatterns = [
            'id$' => 'Primary key or foreign key',
            '_id$' => 'Foreign key reference',
            'email$' => 'Frequently used in lookups',
            'username$' => 'Frequently used in lookups',
            'status$' => 'Frequently used in filters',
            'created_at$' => 'Frequently used in sorting',
            'updated_at$' => 'Frequently used in sorting'
        ];
        
        foreach ($tableData as $tableName => $data) {
            $existingIndexedColumns = [];
            
            // Collect existing indexed columns
            foreach ($data['indexes'] as $index) {
                $existingIndexedColumns[] = $index['Column_name'];
            }
            
            $existingIndexedColumns = array_unique($existingIndexedColumns);
            $recommendationsForTable = [];
            
            // Check each column against patterns
            foreach ($data['columns'] as $column) {
                $columnName = $column['Field'];
                
                // Skip if already indexed
                if (in_array($columnName, $existingIndexedColumns)) {
                    continue;
                }
                
                // Check against patterns
                foreach ($commonPatterns as $pattern => $reason) {
                    if (preg_match('/' . $pattern . '/', $columnName)) {
                        $recommendationsForTable[] = [
                            'column' => $columnName,
                            'reason' => $reason,
                            'sql' => "ALTER TABLE {$tableName} ADD INDEX idx_{$tableName}_{$columnName} ({$columnName});"
                        ];
                        break;
                    }
                }
            }
            
            if (!empty($recommendationsForTable)) {
                $recommendations[$tableName] = $recommendationsForTable;
            }
        }
        
        return $recommendations;
    }
}
