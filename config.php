<?php
/**
 * Configuration file for Clipboard Application
 * 
 * This file contains database credentials and site-wide settings.
 * Make sure to update these values for your environment.
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'clipboard_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Site Configuration
define('SITE_URL', 'http://localhost/CLIPBOARD');
define('SITE_NAME', 'Clipboard');

// Session Configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Timezone
date_default_timezone_set('UTC');

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);
