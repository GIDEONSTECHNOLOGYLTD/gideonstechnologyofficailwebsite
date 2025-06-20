<?php

// Contact form settings
define('MAX_MESSAGE_LENGTH', 5000);
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB

// Allowed file types for attachments
define('ALLOWED_FILE_TYPES', [
    'pdf', 'doc', 'docx', 'txt', 
    'jpg', 'jpeg', 'png', 'gif'
]);

// Services list
define('SERVICES', [
    'web-development' => 'Web Development',
    'fintech' => 'Financial Technology',
    'graphics' => 'Graphics Design',
    'videographics' => 'Video & Graphics',
    'repair' => 'Hardware Repair',
    'general-tech' => 'General Technology'
]);
