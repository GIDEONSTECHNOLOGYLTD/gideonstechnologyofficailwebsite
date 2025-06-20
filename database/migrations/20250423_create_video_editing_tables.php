<?php

namespace Database\Migrations;

use Database\Migration;

class CreateVideoEditingTables extends Migration {
    public function up() {
        $this->schema->createRaw([
            "CREATE TABLE IF NOT EXISTS video_projects (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                title TEXT NOT NULL,
                description TEXT,
                status TEXT CHECK(status IN ('draft', 'in_progress', 'completed', 'archived')) DEFAULT 'draft',
                duration INTEGER,
                resolution TEXT,
                format TEXT,
                thumbnail_url TEXT,
                output_url TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            )",

            "CREATE TABLE IF NOT EXISTS video_clips (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                project_id INTEGER NOT NULL,
                title TEXT NOT NULL,
                source_url TEXT NOT NULL,
                start_time INTEGER DEFAULT 0,
                end_time INTEGER,
                duration INTEGER,
                sort_order INTEGER DEFAULT 0,
                effects TEXT,
                transitions TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (project_id) REFERENCES video_projects(id) ON DELETE CASCADE
            )",

            "CREATE TABLE IF NOT EXISTS video_effects (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                type TEXT NOT NULL,
                parameters TEXT,
                preview_url TEXT,
                is_active INTEGER DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )",

            "CREATE TABLE IF NOT EXISTS video_transitions (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                type TEXT NOT NULL,
                duration INTEGER DEFAULT 1000,
                parameters TEXT,
                preview_url TEXT,
                is_active INTEGER DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )",

            "CREATE TABLE IF NOT EXISTS video_templates (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                description TEXT,
                category TEXT,
                thumbnail_url TEXT,
                preview_url TEXT,
                parameters TEXT,
                is_featured INTEGER DEFAULT 0,
                is_active INTEGER DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )",

            "CREATE TABLE IF NOT EXISTS video_exports (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                project_id INTEGER NOT NULL,
                format TEXT NOT NULL,
                resolution TEXT NOT NULL,
                status TEXT CHECK(status IN ('pending', 'in_progress', 'completed', 'failed')) DEFAULT 'pending',
                output_url TEXT,
                error_message TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (project_id) REFERENCES video_projects(id) ON DELETE CASCADE
            )"
        ]);

        // Create indexes after table creation
        $this->schema->createRaw([
            "CREATE INDEX IF NOT EXISTS idx_video_projects_user_id ON video_projects(user_id)",
            "CREATE INDEX IF NOT EXISTS idx_video_clips_project_id ON video_clips(project_id)",
            "CREATE INDEX IF NOT EXISTS idx_video_exports_project_id ON video_exports(project_id)"
        ]);
    }

    public function down() {
        // Drop indexes first
        $this->schema->dropRaw([
            "DROP INDEX IF EXISTS idx_video_projects_user_id",
            "DROP INDEX IF EXISTS idx_video_clips_project_id",
            "DROP INDEX IF EXISTS idx_video_exports_project_id"
        ]);

        // Then drop tables
        $this->schema->drop(['video_exports', 'video_templates', 'video_transitions', 'video_effects', 'video_clips', 'video_projects']);
    }
}
