<?php
/**
 * Structured Data Generator for Gideons Technology
 * Include this file in your templates to add JSON-LD structured data
 */

function getStructuredDataForBusiness() {
    $jsonData = [
        "@context" => "https://schema.org",
        "@type" => "Organization",
        "name" => "Gideons Technology Ltd",
        "url" => "https://gideonstechnology.com",
        "logo" => "https://gideonstechnology.com/assets/images/logo.png",
        "description" => "Professional technology services including web development, repair services, and fintech solutions.",
        "address" => [
            "@type" => "PostalAddress",
            "streetAddress" => "123 Tech Street", // Replace with actual address
            "addressLocality" => "London", // Replace with actual city
            "addressRegion" => "London", // Replace with actual region
            "postalCode" => "SW1A 1AA", // Replace with actual postal code
            "addressCountry" => "UK" // Replace with actual country
        ],
        "contactPoint" => [
            "@type" => "ContactPoint",
            "telephone" => "+44-123-456-7890", // Replace with actual phone
            "contactType" => "customer service",
            "email" => "info@gideonstechnology.com" // Replace with actual email
        ],
        "sameAs" => [
            "https://www.facebook.com/gideonstechnology", // Replace with actual social media URLs
            "https://www.twitter.com/gideonstech",
            "https://www.linkedin.com/company/gideons-technology"
        ]
    ];
    
    return '<script type="application/ld+json">' . json_encode($jsonData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . '</script>';
}

function getStructuredDataForWebPage($title, $description, $url = null, $image = null) {
    if (!$url) {
        $url = 'https://gideonstechnology.com' . ($_SERVER['REQUEST_URI'] ?? '/');
    }
    
    $jsonData = [
        "@context" => "https://schema.org",
        "@type" => "WebPage",
        "name" => $title,
        "description" => $description,
        "url" => $url
    ];
    
    if ($image) {
        $jsonData["primaryImageOfPage"] = [
            "@type" => "ImageObject",
            "url" => $image
        ];
    }
    
    return '<script type="application/ld+json">' . json_encode($jsonData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . '</script>';
}

function getStructuredDataForProduct($product) {
    $jsonData = [
        "@context" => "https://schema.org",
        "@type" => "Product",
        "name" => $product['name'],
        "description" => $product['description'],
        "image" => $product['image'],
        "sku" => $product['sku'],
        "brand" => [
            "@type" => "Brand",
            "name" => $product['brand']
        ],
        "offers" => [
            "@type" => "Offer",
            "priceCurrency" => "GBP",
            "price" => $product['price'],
            "availability" => $product['in_stock'] ? "https://schema.org/InStock" : "https://schema.org/OutOfStock",
            "url" => $product['url']
        ]
    ];
    
    return '<script type="application/ld+json">' . json_encode($jsonData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . '</script>';
}
