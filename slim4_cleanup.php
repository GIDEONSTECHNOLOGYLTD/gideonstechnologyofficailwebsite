<?php

/**
 * Slim 4 Application Cleanup and Optimization Script
 * 
 * This script performs comprehensive cleanup and optimization of the Slim 4 application structure:
 * 1. Removes duplicate directories
 * 2. Standardizes naming conventions
 * 3. Removes redundant files
 * 4. Organizes files according to Slim 4 best practices
 */

class Slim4Cleanup {
    protected $basePath;
    protected $backupDir;
    protected $duplicateDirs = [];
    protected $redundantFiles = [];
    protected $tempFiles = [];
    protected $backupFiles = [];
    protected $debugFiles = [];
    
    // Directories that should be consolidated
    protected $consolidateDirs = [
        'app/Core' => 'app/core',
        'app/Core_temp' => 'app/core',
        'app/core_backup' => 'app/core'
    ];
    
    // Files that should be removed (relative to base path)
    protected $removeFiles = [
        'public/index.php.bak',
        'public/index.php.bak2',
        'public/router.php.bak',
        'public/router.php.bak2',
        'public/fixed-index.php',
        'public/fixed-index-v2.php',
        'public/debug-index.php',
        'public/debug-test.php',
        'public/debug.php',
        'public/error-debug.php',
        'public/error-test.php',
        'public/simple-debug.php',
        'public/trace-debug.php'
    ];
    
    public function __construct() {
        $this->basePath = __DIR__;
        $this->backupDir = $this->basePath . '/backup_' . date('Ymd_His');
        
        // Create backup directory if it doesn't exist
        if (!is_dir($this->backupDir)) {
            mkdir($this->backupDir, 0755, true);
        }
    }
    
    /**
     * Run the cleanup process
     */
    public function run($dryRun = true) {
        echo "\nSlim 4 Application Cleanup and Optimization\n";
        echo "===========================================\n\n";
        
        if ($dryRun) {
            echo "DRY RUN MODE: No changes will be made\n\n";
        }
        
        // Scan for issues
        $this->scanDuplicateDirectories();
        $this->scanRedundantFiles();
        $this->scanTempAndBackupFiles();
        
        // Report findings
        $this->reportFindings();
        
        // Perform cleanup if not in dry run mode
        if (!$dryRun) {
            $this->performCleanup();
        }
        
        echo "\nCleanup process completed.\n";
        if ($dryRun) {
            echo "Run with --execute parameter to perform actual cleanup.\n";
        }
    }
    
    /**
     * Scan for duplicate directories
     */
    protected function scanDuplicateDirectories() {
        echo "Scanning for duplicate directories...\n";
        
        foreach ($this->consolidateDirs as $source => $target) {
            $sourcePath = $this->basePath . '/' . $source;
            if (is_dir($sourcePath)) {
                $this->duplicateDirs[] = [
                    'source' => $source,
                    'target' => $target
                ];
            }
        }
    }
    
    /**
     * Scan for redundant files
     */
    protected function scanRedundantFiles() {
        echo "Scanning for redundant files...\n";
        
        foreach ($this->removeFiles as $file) {
            $filePath = $this->basePath . '/' . $file;
            if (file_exists($filePath)) {
                $this->redundantFiles[] = $file;
            }
        }
        
        // Find all .bak, .bak2, etc. files
        $this->findFilesByPattern('*.bak*', $this->backupFiles);
        
        // Find all debug-*, test-*, etc. files
        $this->findFilesByPattern('*debug*.php', $this->debugFiles);
        $this->findFilesByPattern('*test*.php', $this->debugFiles);
    }
    
    /**
     * Find files by pattern
     */
    protected function findFilesByPattern($pattern, &$fileList) {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->basePath, RecursiveDirectoryIterator::SKIP_DOTS)
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile() && fnmatch($pattern, $file->getFilename())) {
                $relativePath = str_replace($this->basePath . '/', '', $file->getPathname());
                $fileList[] = $relativePath;
            }
        }
    }
    
    /**
     * Scan for temporary and backup files
     */
    protected function scanTempAndBackupFiles() {
        echo "Scanning for temporary and backup files...\n";
        
        // Find all temp_* directories
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->basePath, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        
        foreach ($iterator as $path) {
            if ($path->isDir() && (strpos($path->getFilename(), 'temp_') === 0 || strpos($path->getFilename(), 'backup_') === 0)) {
                $relativePath = str_replace($this->basePath . '/', '', $path->getPathname());
                $this->tempFiles[] = $relativePath;
            }
        }
    }
    
    /**
     * Report findings
     */
    protected function reportFindings() {
        echo "\nFindings Report\n";
        echo "==============\n\n";
        
        // Report duplicate directories
        echo "Duplicate Directories to Consolidate: " . count($this->duplicateDirs) . "\n";
        foreach ($this->duplicateDirs as $dir) {
            echo "  - {$dir['source']} → {$dir['target']}\n";
        }
        
        // Report redundant files
        echo "\nRedundant Files to Remove: " . count($this->redundantFiles) . "\n";
        foreach ($this->redundantFiles as $file) {
            echo "  - {$file}\n";
        }
        
        // Report backup files
        echo "\nBackup Files Found: " . count($this->backupFiles) . "\n";
        if (count($this->backupFiles) > 10) {
            echo "  - Showing first 10 of " . count($this->backupFiles) . " files\n";
            for ($i = 0; $i < 10; $i++) {
                echo "  - {$this->backupFiles[$i]}\n";
            }
        } else {
            foreach ($this->backupFiles as $file) {
                echo "  - {$file}\n";
            }
        }
        
        // Report debug files
        echo "\nDebug/Test Files Found: " . count($this->debugFiles) . "\n";
        if (count($this->debugFiles) > 10) {
            echo "  - Showing first 10 of " . count($this->debugFiles) . " files\n";
            for ($i = 0; $i < 10; $i++) {
                echo "  - {$this->debugFiles[$i]}\n";
            }
        } else {
            foreach ($this->debugFiles as $file) {
                echo "  - {$file}\n";
            }
        }
        
        // Report temp directories
        echo "\nTemporary/Backup Directories Found: " . count($this->tempFiles) . "\n";
        foreach ($this->tempFiles as $dir) {
            echo "  - {$dir}\n";
        }
    }
    
    /**
     * Perform the actual cleanup
     */
    protected function performCleanup() {
        echo "\nPerforming Cleanup\n";
        echo "=================\n\n";
        
        // Consolidate duplicate directories
        foreach ($this->duplicateDirs as $dir) {
            $this->consolidateDirectory($dir['source'], $dir['target']);
        }
        
        // Remove redundant files
        foreach ($this->redundantFiles as $file) {
            $this->backupAndRemoveFile($file);
        }
        
        // Remove backup files (optional, based on confirmation)
        echo "\nWould you like to remove all " . count($this->backupFiles) . " backup files? (y/n): ";
        $handle = fopen("php://stdin", "r");
        $line = trim(fgets($handle));
        if (strtolower($line) === 'y') {
            foreach ($this->backupFiles as $file) {
                $this->backupAndRemoveFile($file);
            }
            echo "All backup files have been removed.\n";
        } else {
            echo "Backup files will be kept.\n";
        }
        
        // Remove debug files (optional, based on confirmation)
        echo "\nWould you like to remove all " . count($this->debugFiles) . " debug/test files? (y/n): ";
        $line = trim(fgets($handle));
        if (strtolower($line) === 'y') {
            foreach ($this->debugFiles as $file) {
                $this->backupAndRemoveFile($file);
            }
            echo "All debug/test files have been removed.\n";
        } else {
            echo "Debug/test files will be kept.\n";
        }
        
        fclose($handle);
    }
    
    /**
     * Consolidate a directory by moving its contents to the target directory
     */
    protected function consolidateDirectory($source, $target) {
        $sourcePath = $this->basePath . '/' . $source;
        $targetPath = $this->basePath . '/' . $target;
        $backupPath = $this->backupDir . '/' . $source;
        
        // Create target directory if it doesn't exist
        if (!is_dir($targetPath)) {
            mkdir($targetPath, 0755, true);
            echo "Created target directory: {$target}\n";
        }
        
        // Create backup directory
        if (!is_dir(dirname($backupPath))) {
            mkdir(dirname($backupPath), 0755, true);
        }
        
        // Copy source directory to backup
        $this->recursiveCopy($sourcePath, $backupPath);
        echo "Backed up {$source} to {$this->backupDir}/{$source}\n";
        
        // Move files from source to target
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($sourcePath, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        
        foreach ($iterator as $item) {
            $relPath = substr($item->getPathname(), strlen($sourcePath));
            $targetFile = $targetPath . $relPath;
            
            if ($item->isDir()) {
                if (!is_dir($targetFile)) {
                    mkdir($targetFile, 0755, true);
                }
            } else {
                // Check if file already exists in target
                if (file_exists($targetFile)) {
                    // Compare files to see if they're different
                    if (md5_file($item->getPathname()) !== md5_file($targetFile)) {
                        // Files are different, rename the target file as backup
                        rename($targetFile, $targetFile . '.bak');
                        echo "  - Renamed existing file {$target}{$relPath} to {$target}{$relPath}.bak\n";
                        copy($item->getPathname(), $targetFile);
                        echo "  - Copied {$source}{$relPath} to {$target}{$relPath}\n";
                    }
                } else {
                    copy($item->getPathname(), $targetFile);
                    echo "  - Copied {$source}{$relPath} to {$target}{$relPath}\n";
                }
            }
        }
        
        // Remove source directory
        $this->recursiveRemove($sourcePath);
        echo "Removed directory {$source}\n";
    }
    
    /**
     * Backup and remove a file
     */
    protected function backupAndRemoveFile($file) {
        $filePath = $this->basePath . '/' . $file;
        $backupPath = $this->backupDir . '/' . $file;
        
        // Create backup directory if it doesn't exist
        if (!is_dir(dirname($backupPath))) {
            mkdir(dirname($backupPath), 0755, true);
        }
        
        // Copy file to backup
        copy($filePath, $backupPath);
        echo "Backed up {$file} to {$this->backupDir}/{$file}\n";
        
        // Remove file
        unlink($filePath);
        echo "Removed file {$file}\n";
    }
    
    /**
     * Recursively copy a directory
     */
    protected function recursiveCopy($source, $dest) {
        if (!is_dir($dest)) {
            mkdir($dest, 0755, true);
        }
        
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        
        foreach ($iterator as $item) {
            if ($item->isDir()) {
                $targetDir = $dest . '/' . $iterator->getSubPathName();
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0755, true);
                }
            } else {
                copy($item->getPathname(), $dest . '/' . $iterator->getSubPathName());
            }
        }
    }
    
    /**
     * Recursively remove a directory
     */
    protected function recursiveRemove($dir) {
        if (!is_dir($dir)) {
            return;
        }
        
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        
        foreach ($iterator as $item) {
            if ($item->isDir()) {
                rmdir($item->getPathname());
            } else {
                unlink($item->getPathname());
            }
        }
        
        rmdir($dir);
    }
}

// Run the script
if (basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
    $cleanup = new Slim4Cleanup();
    
    // Check if --execute parameter is provided
    $dryRun = true;
    if (isset($argv[1]) && $argv[1] === '--execute') {
        $dryRun = false;
    }
    
    $cleanup->run($dryRun);
}
