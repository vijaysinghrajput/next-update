<?php
// Main entry point for Next Update Application

// Bootstrap the application
require_once __DIR__ . '/bootstrap.php';

// Load routes
require_once __DIR__ . '/routes.php';

// Dispatch the request
use App\Services\Router;
Router::dispatch();
?>