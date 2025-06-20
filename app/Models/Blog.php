<?php

namespace App\Models;

class Blog {
    private $posts = [];
    
    public function __construct() {
        // Initialize with some sample blog posts
        $this->initializeBlogData();
    }
    
    public function getAllPosts(int $page = 1, int $perPage = 10): array {
        $offset = ($page - 1) * $perPage;
        return array_slice($this->posts, $offset, $perPage);
    }
    
    public function getPostBySlug(string $slug): ?array {
        foreach ($this->posts as $post) {
            if ($post['slug'] === $slug) {
                return $post;
            }
        }
        return null;
    }
    
    public function getPostsByCategory(string $category, int $page = 1, int $perPage = 10): array {
        $categoryPosts = array_filter($this->posts, function($post) use ($category) {
            return in_array($category, $post['categories']);
        });
        
        $offset = ($page - 1) * $perPage;
        return array_slice($categoryPosts, $offset, $perPage);
    }
    
    private function initializeBlogData(): void {
        $this->posts = [
            [
                'id' => 1,
                'title' => 'Getting Started with Web Development',
                'slug' => 'getting-started-with-web-development',
                'excerpt' => 'Learn the basics of web development and where to begin your journey.',
                'content' => '<p>Web development is an exciting field that combines creativity with technical skills...</p><p>This article covers the fundamentals you need to start your journey.</p>',
                'author' => 'Gideon Aina',
                'date' => '2023-05-10',
                'categories' => ['web-development', 'beginners'],
                'image' => '/images/blog/web-dev.jpg'
            ],
            [
                'id' => 2,
                'title' => 'The Future of Mobile Applications',
                'slug' => 'future-of-mobile-applications',
                'excerpt' => 'Explore the upcoming trends in mobile app development and technologies.',
                'content' => '<p>Mobile applications continue to evolve rapidly with new technologies...</p><p>This post discusses future trends and what developers should focus on.</p>',
                'author' => 'Gideon Aina',
                'date' => '2023-05-15',
                'categories' => ['mobile-development', 'tech-trends'],
                'image' => '/images/blog/mobile-future.jpg'
            ],
            [
                'id' => 3,
                'title' => 'Hardware Troubleshooting Guide',
                'slug' => 'hardware-troubleshooting-guide',
                'excerpt' => 'A comprehensive guide to troubleshooting common computer hardware issues.',
                'content' => '<p>Having problems with your computer? This troubleshooting guide will help you...</p><p>Learn how to diagnose and fix common hardware issues yourself.</p>',
                'author' => 'Gideon Aina',
                'date' => '2023-05-20',
                'categories' => ['hardware', 'troubleshooting'],
                'image' => '/images/blog/hardware-guide.jpg'
            ],
            [
                'id' => 4,
                'title' => 'Securing Your Website',
                'slug' => 'securing-your-website',
                'excerpt' => 'Essential security measures every website owner should implement.',
                'content' => '<p>Website security is crucial in today\'s digital landscape...</p><p>This guide covers the essential security measures you need to implement.</p>',
                'author' => 'Gideon Aina',
                'date' => '2023-05-25',
                'categories' => ['web-development', 'security'],
                'image' => '/images/blog/web-security.jpg'
            ],
            [
                'id' => 5,
                'title' => 'Introduction to PHP Framework',
                'slug' => 'introduction-to-php-framework',
                'excerpt' => 'Learn about PHP frameworks and how they can streamline your development.',
                'content' => '<p>PHP frameworks provide a structured way to build web applications...</p><p>This introduction will help you understand the benefits and get started quickly.</p>',
                'author' => 'Gideon Aina',
                'date' => '2023-05-30',
                'categories' => ['web-development', 'php'],
                'image' => '/images/blog/php-framework.jpg'
            ]
        ];
    }
}