<?php
class FileUploader {
    private $uploadDir;
    private $allowedTypes;
    private $maxSize;
    private $errors = [];

    public function __construct($uploadDir = null) {
        $this->uploadDir = $uploadDir ?? UPLOAD_DIR;
        $this->allowedTypes = ALLOWED_EXTENSIONS;
        $this->maxSize = MAX_UPLOAD_SIZE;
        $this->ensureUploadDirectory();
    }

    private function ensureUploadDirectory() {
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }

    public function upload($file, $customName = null) {
        if (!$this->validate($file)) {
            return false;
        }

        // Determine file extension from actual mime type, not user input
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        $extension = $this->getExtensionFromMimeType($mimeType);
        
        if (!$extension) {
            $this->errors[] = 'Invalid file type detected';
            return false;
        }
        
        $filename = $customName ? $customName . '.' . $extension : Helper::sanitizeFilename($file['name']);
        $filepath = $this->uploadDir . '/' . $filename;
        
        // Ensure unique filename
        $counter = 1;
        $filenameBase = pathinfo($filename, PATHINFO_FILENAME);
        while (file_exists($filepath)) {
            $filename = $filenameBase . '-' . $counter . '.' . $extension;
            $filepath = $this->uploadDir . '/' . $filename;
            $counter++;
        }

        // Use move_uploaded_file only for actual uploaded files
        if (!is_uploaded_file($file['tmp_name'])) {
            $this->errors[] = 'Invalid upload attempt';
            return false;
        }
        
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            $this->errors[] = 'Failed to move uploaded file';
            return false;
        }

        // Set proper permissions - restrict to owner and group only
        chmod($filepath, 0644);

        return [
            'filename' => $filename,
            'filepath' => $filepath,
            'size' => $file['size'],
            'type' => $mimeType
        ];
    }

    private function validate($file) {
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $this->errors[] = $this->getUploadErrorMessage($file['error']);
            return false;
        }

        // Check file size
        if ($file['size'] > $this->maxSize) {
            $this->errors[] = 'File size exceeds maximum limit (' . $this->formatSize($this->maxSize) . ')';
            return false;
        }

        // Verify file exists and is an uploaded file
        if (!file_exists($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            $this->errors[] = 'Invalid upload attempt';
            return false;
        }

        // Check file type using actual content, not just extension
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        
        $allowedMimeTypes = $this->getMimeTypesFromExtensions($this->allowedTypes);
        if (!in_array($mimeType, $allowedMimeTypes)) {
            $this->errors[] = 'File type not allowed';
            return false;
        }

        return true;
    }

    private function getMimeTypesFromExtensions(array $extensions) {
        $mimeTypes = [];
        $commonMimeTypes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];
        
        foreach ($extensions as $ext) {
            if (isset($commonMimeTypes[$ext])) {
                $mimeTypes[] = $commonMimeTypes[$ext];
            }
        }
        
        return $mimeTypes;
    }

    private function getExtensionFromMimeType($mimeType) {
        $mimeToExt = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'application/pdf' => 'pdf',
            'application/msword' => 'doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx'
        ];
        
        return $mimeToExt[$mimeType] ?? null;
    }

    private function getUploadErrorMessage($errorCode) {
        switch ($errorCode) {
            case UPLOAD_ERR_INI_SIZE:
                return 'File exceeds upload_max_filesize directive in php.ini';
            case UPLOAD_ERR_FORM_SIZE:
                return 'File exceeds MAX_FILE_SIZE directive specified in the HTML form';
            case UPLOAD_ERR_PARTIAL:
                return 'File was only partially uploaded';
            case UPLOAD_ERR_NO_FILE:
                return 'No file was uploaded';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Missing temporary folder';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Failed to write file to disk';
            case UPLOAD_ERR_EXTENSION:
                return 'File upload stopped by extension';
            default:
                return 'Unknown upload error';
        }
    }

    private function formatSize($size) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;
        while ($size >= 1024 && $i < count($units) - 1) {
            $size /= 1024;
            $i++;
        }
        return round($size, 2) . ' ' . $units[$i];
    }

    public function getErrors() {
        return $this->errors;
    }

    public function setAllowedTypes($types) {
        $this->allowedTypes = $types;
    }

    public function setMaxSize($size) {
        $this->maxSize = $size;
    }

    public function deleteFile($filename) {
        $filepath = $this->uploadDir . '/' . $filename;
        if (file_exists($filepath)) {
            return unlink($filepath);
        }
        return false;
    }

    public function getFileInfo($filename) {
        $filepath = $this->uploadDir . '/' . $filename;
        if (!file_exists($filepath)) {
            return false;
        }

        // Use finfo for more accurate mime type detection
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($filepath);

        return [
            'filename' => $filename,
            'filepath' => $filepath,
            'size' => filesize($filepath),
            'type' => $mimeType,
            'modified' => filemtime($filepath)
        ];
    }
}