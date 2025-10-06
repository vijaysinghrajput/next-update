<?php
require_once __DIR__ . '/../../bootstrap.php';

use App\Models\Ad;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$adId = (int)($_POST['ad_id'] ?? 0);

if (!$adId) {
    echo json_encode(['success' => false, 'message' => 'Ad ID required']);
    exit;
}

try {
    $adModel = new Ad();
    
    // Track ad click
    $adModel->getDb()->query("UPDATE ads SET clicks = clicks + 1 WHERE id = ?", [$adId]);
    
    echo json_encode(['success' => true, 'message' => 'Ad click tracked']);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error tracking ad click']);
}
?>
