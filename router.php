<?php
// Router script for PHP built-in server
// This file routes all requests through index.php

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Serve static files directly (CSS, JS, images, etc.)
if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    // Check if it's a static file (not a directory and not a PHP file)
    if (is_file(__DIR__ . $uri) && !preg_match('/\.php$/', $uri)) {
        return false; // Serve the file directly
    }
}

// Route all other requests through index.php
require_once __DIR__ . '/index.php';
?>
