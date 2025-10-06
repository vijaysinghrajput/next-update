<?php
require_once __DIR__ . '/../../bootstrap.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!session('user_id')) {
    echo json_encode([
        'success' => false,
        'message' => 'Not authenticated',
        'data' => []
    ]);
    exit;
}

try {
    // For now, return empty notifications
    // This can be expanded later to show actual notifications
    echo json_encode([
        'success' => true,
        'data' => [],
        'count' => 0
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error loading notifications: ' . $e->getMessage(),
        'data' => []
    ]);
}
?>
