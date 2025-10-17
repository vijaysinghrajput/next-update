<?php
require_once __DIR__ . '/../../bootstrap-fixed.php';

use App\Services\RSSService;

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

$rssService = new RSSService();

$category = $_GET['category'] ?? 'top-stories';
$limit = (int)($_GET['limit'] ?? 20);
$language = $_GET['language'] ?? 'hindi'; // Default to Hindi

// Validate limit
if ($limit < 1 || $limit > 50) {
    $limit = 20;
}

// Validate language
if (!in_array($language, ['english', 'hindi', 'both'])) {
    $language = 'hindi';
}

try {
    $result = $rssService->fetchRSSFeed($category, $limit, $language);
    
    // Add cache headers for 5 minutes
    header('Cache-Control: public, max-age=300');
    header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 300) . ' GMT');
    
    echo json_encode($result, JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch RSS feed: ' . $e->getMessage(),
        'articles' => []
    ], JSON_PRETTY_PRINT);
}
?>