-- Database Schema for Clipboard Application
-- Version: 1.0
-- Database: clipboard_db

-- Create database if it doesn't exist
CREATE DATABASE IF NOT EXISTS clipboard_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE clipboard_db;

-- ============================================================================
-- Users Table
-- ============================================================================
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- Boards Table
-- ============================================================================
CREATE TABLE IF NOT EXISTS `boards` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `suburl` varchar(100) NOT NULL,
  `default_access` enum('private','public_view','public_add') NOT NULL DEFAULT 'private',
  `list_publically` tinyint(1) NOT NULL DEFAULT 0,
  `is_editable` tinyint(1) NOT NULL DEFAULT 1,
  `password_hash` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `suburl` (`suburl`),
  KEY `owner_id` (`owner_id`),
  KEY `idx_created_at` (`created_at`),
  CONSTRAINT `boards_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- Clips Table
-- ============================================================================
CREATE TABLE IF NOT EXISTS `clips` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `board_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `board_id` (`board_id`),
  KEY `user_id` (`user_id`),
  KEY `idx_created_at` (`created_at`),
  CONSTRAINT `clips_ibfk_1` FOREIGN KEY (`board_id`) REFERENCES `boards` (`id`) ON DELETE CASCADE,
  CONSTRAINT `clips_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- Board Permissions Table
-- ============================================================================
CREATE TABLE IF NOT EXISTS `board_permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `board_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `permission_level` enum('view','edit','admin') NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `board_user_unique` (`board_id`,`user_id`),
  KEY `user_id` (`user_id`),
  KEY `idx_permission` (`permission_level`),
  CONSTRAINT `board_permissions_ibfk_1` FOREIGN KEY (`board_id`) REFERENCES `boards` (`id`) ON DELETE CASCADE,
  CONSTRAINT `board_permissions_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;