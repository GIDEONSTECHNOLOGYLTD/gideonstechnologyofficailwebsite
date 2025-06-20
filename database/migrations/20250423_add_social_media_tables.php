<?php

namespace Database\Migrations;

use Database\Migration;

class AddSocialMediaTables extends Migration {
    public function up() {
        $this->schema->createRaw([
            // Create social_users table
            "CREATE TABLE IF NOT EXISTS social_users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                provider TEXT NULL,
                provider_id TEXT NULL,
                access_token TEXT NULL,
                refresh_token TEXT NULL,
                expires_at INTEGER NULL,
                user_id INTEGER,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            )",

            // Create social_posts table
            "CREATE TABLE IF NOT EXISTS social_posts (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER,
                provider TEXT NULL,
                post_id TEXT NULL,
                content TEXT NULL,
                media_url TEXT NULL,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            )",

            // Create social_analytics table
            "CREATE TABLE IF NOT EXISTS social_analytics (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                post_id INTEGER,
                likes INTEGER DEFAULT 0,
                shares INTEGER DEFAULT 0,
                comments INTEGER DEFAULT 0,
                reach INTEGER DEFAULT 0,
                engagement_rate REAL DEFAULT 0.00,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL,
                FOREIGN KEY (post_id) REFERENCES social_posts(id) ON DELETE CASCADE
            )",

            // Create social_schedules table
            "CREATE TABLE IF NOT EXISTS social_schedules (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER,
                provider TEXT NULL,
                content TEXT NULL,
                media_url TEXT NULL,
                scheduled_at TIMESTAMP NULL,
                status TEXT DEFAULT 'pending',
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            )"
        ]);
    }

    public function down() {
        $this->schema->drop(['social_schedules', 'social_analytics', 'social_posts', 'social_users']);
    }
}
