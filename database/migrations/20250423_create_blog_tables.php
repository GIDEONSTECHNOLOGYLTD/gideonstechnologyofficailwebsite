<?php

namespace Database\Migrations;

use Database\Migration;

class CreateBlogTables extends Migration {
    public function up() {
        $this->schema->createRaw([
            // Create posts table
            "CREATE TABLE IF NOT EXISTS posts (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                slug TEXT NOT NULL UNIQUE,
                content TEXT NOT NULL,
                excerpt TEXT NULL,
                author_id INTEGER,
                category_id INTEGER,
                status TEXT CHECK(status IN ('draft', 'published', 'archived')) DEFAULT 'draft',
                featured_image TEXT NULL,
                views INTEGER DEFAULT 0,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL,
                FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
            )",

            // Create categories table
            "CREATE TABLE IF NOT EXISTS categories (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                slug TEXT NOT NULL UNIQUE,
                content TEXT NOT NULL,
                excerpt TEXT NULL,
                author_id INTEGER,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL,
                FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE
            )",

            // Create tags table
            "CREATE TABLE IF NOT EXISTS tags (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL UNIQUE,
                slug TEXT NOT NULL UNIQUE,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL
            )",

            // Create post_tags table
            "CREATE TABLE IF NOT EXISTS post_tags (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                post_id INTEGER,
                tag_id INTEGER,
                created_at TIMESTAMP NULL,
                FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
                FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
            )",

            // Create comments table
            "CREATE TABLE IF NOT EXISTS comments (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                post_id INTEGER,
                author_id INTEGER,
                content TEXT NOT NULL,
                status TEXT CHECK(status IN ('pending', 'approved', 'spam')) DEFAULT 'pending',
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL,
                FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
                FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE
            )"
        ]);
    }

    public function down() {
        $this->schema->drop(['post_tags', 'tags', 'comments', 'posts', 'categories']);
    }
}
