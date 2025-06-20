<?php

// Function to create a placeholder image
function createPlaceholderImage($width, $height, $text, $filename) {
    $image = imagecreatetruecolor($width, $height);
    
    // Colors
    $bg = imagecolorallocate($image, 240, 240, 240);
    $textColor = imagecolorallocate($image, 100, 100, 100);
    
    // Fill background
    imagefilledrectangle($image, 0, 0, $width, $height, $bg);
    
    // Add text
    $font = 5; // Built-in font
    $textWidth = imagefontwidth($font) * strlen($text);
    $textHeight = imagefontheight($font);
    
    $x = ($width - $textWidth) / 2;
    $y = ($height - $textHeight) / 2;
    
    imagestring($image, $font, $x, $y, $text, $textColor);
    
    // Save image
    imagepng($image, $filename);
    imagedestroy($image);
}

// Create directories if they don't exist
@mkdir('public/assets/img/services', 0777, true);

// Generate placeholder images
$images = [
    ['width' => 200, 'height' => 50, 'text' => 'Gideons Tech Logo', 'file' => 'public/assets/img/logo.png'],
    ['width' => 1200, 'height' => 600, 'text' => 'Hero Image', 'file' => 'public/assets/img/hero-image.png'],
    ['width' => 400, 'height' => 300, 'text' => 'Web Development', 'file' => 'public/assets/img/services/web-dev.jpg'],
    ['width' => 400, 'height' => 300, 'text' => 'Fintech', 'file' => 'public/assets/img/services/fintech.jpg'],
    ['width' => 400, 'height' => 300, 'text' => 'General Tech', 'file' => 'public/assets/img/services/general-tech.jpg']
];

foreach ($images as $img) {
    createPlaceholderImage($img['width'], $img['height'], $img['text'], $img['file']);
}

// Create a simple favicon (1x1 transparent pixel)
$favicon = fopen('public/assets/img/favicon.ico', 'w');
fwrite($favicon, base64_decode('AAABAAEAAQEAAAEAIAAwAAAAFgAAACgAAAABAAAAAgAAAAEAIAAAAAAABAAAAAAAAAAAAAAAAAAAAAAAAAAAAP//AAAAAA=='));
fclose($favicon);

echo "Placeholder images generated successfully!\n";
