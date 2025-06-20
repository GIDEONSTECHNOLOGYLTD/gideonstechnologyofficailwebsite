<?php
// Script to verify and create required directories for assets

// Define base directory 
$baseDir = __DIR__;

// Define required directories structure
$requiredDirs = [
    'assets',
    'assets/css',
    'assets/js',
    'assets/img',
    'assets/img/blog',
    'assets/img/footer',
    'assets/img/seller'
];

// Check and create directories
foreach ($requiredDirs as $dir) {
    $fullPath = $baseDir . '/' . $dir;
    if (!file_exists($fullPath)) {
        echo "Creating directory: $dir\n";
        mkdir($fullPath, 0755, true);
    } else {
        echo "Directory exists: $dir\n";
    }
}

echo "Directory structure check complete.\n";

// Check for essential CSS files
$essentialCssFiles = [
    'assets/css/animate.css',
    'assets/css/bootstrap.min.css',
    'assets/css/flaticon.css',
    'assets/css/slick.css',
    'assets/css/line-awesome.min.css',
    'assets/css/nice-select.css',
    'assets/css/style.css'
];

foreach ($essentialCssFiles as $cssFile) {
    $fullPath = $baseDir . '/' . $cssFile;
    if (!file_exists($fullPath)) {
        echo "Missing CSS file: $cssFile\n";
    } else {
        echo "CSS file exists: $cssFile\n";
    }
}

// Check for essential JS files
$essentialJsFiles = [
    'assets/js/jquery-3.6.0.min.js',
    'assets/js/bootstrap.bundle.min.js',
    'assets/js/wow.min.js',
    'assets/js/slick.js',
    'assets/js/jquery.nice-select.js',
    'assets/js/jquery.nicescroll.min.js',
    'assets/js/main.js'
];

foreach ($essentialJsFiles as $jsFile) {
    $fullPath = $baseDir . '/' . $jsFile;
    if (!file_exists($fullPath)) {
        echo "Missing JS file: $jsFile\n";
    } else {
        echo "JS file exists: $jsFile\n";
    }
}

// Check for essential image files
$essentialImgFiles = [
    'assets/icon.png',
    'assets/img/logo-01.png',
    'assets/img/blog/futurebusiness.jpg',
    'assets/img/blog/tech.jpg',
    'assets/img/blog/blockchain.jpg',
    'assets/img/blog/iot.jpg',
    'assets/img/blog/greent.jpg'
];

foreach ($essentialImgFiles as $imgFile) {
    $fullPath = $baseDir . '/' . $imgFile;
    if (!file_exists($fullPath)) {
        echo "Missing image file: $imgFile\n";
    } else {
        echo "Image file exists: $imgFile\n";
    }
}

echo "File check complete.\n";
?>