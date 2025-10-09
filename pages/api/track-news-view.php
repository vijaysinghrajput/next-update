<?php
require_once __DIR__ . '/../../bootstrap-fixed.php';

use App\Models\News;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$newsId = (int)($_POST['news_id'] ?? 0);

if (!$newsId) {
    echo json_encode(['success' => false, 'message' => 'News ID required']);
    exit;
}

try {
    $newsModel = new News();
    
    // Track news view
    $newsModel->incrementViews($newsId);
    
    echo json_encode(['success' => true, 'message' => 'News view tracked']);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error tracking news view']);
}
?>
