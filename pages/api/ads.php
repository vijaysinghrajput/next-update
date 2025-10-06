<?php
require_once __DIR__ . '/../../bootstrap.php';

use App\Models\Ad;

header('Content-Type: application/json');

$adModel = new Ad();

// Get parameters
$position = $_GET['position'] ?? null;
$status = $_GET['status'] ?? 'active';

try {
    $ads = [];
    
    if ($position) {
        $ads = $adModel->getDb()->fetchAll("
            SELECT * FROM ads 
            WHERE position = ? AND status = ? AND start_date <= CURDATE() AND end_date >= CURDATE()
            ORDER BY created_at DESC
        ", [$position, $status]);
    } else {
        $ads = $adModel->getDb()->fetchAll("
            SELECT * FROM ads 
            WHERE status = ? AND start_date <= CURDATE() AND end_date >= CURDATE()
            ORDER BY position, created_at DESC
        ", [$status]);
    }
    
    echo json_encode([
        'success' => true,
        'data' => $ads,
        'count' => count($ads)
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error loading ads: ' . $e->getMessage()
    ]);
}
?>
