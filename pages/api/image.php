<?php
// Image serving API
$path = $params['path'] ?? '';

// Debug: Log the path
error_log("Image API called with path: " . $path);

if (empty($path)) {
    http_response_code(404);
    exit('Image not found - no path provided');
}

// Security check - only allow images
$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
$extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

if (!in_array($extension, $allowedExtensions)) {
    http_response_code(403);
    exit('Forbidden');
}

$filePath = __DIR__ . '/../../public/uploads/' . $path;

if (file_exists($filePath) && is_file($filePath)) {
    $mimeType = mime_content_type($filePath);
    header('Content-Type: ' . $mimeType);
    header('Content-Length: ' . filesize($filePath));
    header('Cache-Control: public, max-age=31536000'); // Cache for 1 year
    readfile($filePath);
} else {
    http_response_code(404);
    exit('Image not found');
}
?>
