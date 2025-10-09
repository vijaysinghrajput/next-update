<?php
// Service Worker API endpoint
$swFile = __DIR__ . '/../../sw.js';

if (file_exists($swFile) && is_file($swFile)) {
    header('Content-Type: application/javascript');
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');
    header('Content-Length: ' . filesize($swFile));
    readfile($swFile);
} else {
    http_response_code(404);
    exit('Service Worker not found');
}
?>
