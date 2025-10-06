<?php
require_once __DIR__ . '/../../bootstrap.php';

use App\Models\News;

header('Content-Type: application/json');

$newsModel = new News();

// Get parameters
$page = (int)($_GET['page'] ?? 1);
$limit = (int)($_GET['limit'] ?? 10);
$filter = $_GET['filter'] ?? 'all';
$categoryId = $_GET['category_id'] ?? null;
$cityId = $_GET['city_id'] ?? null;

// Calculate offset
$offset = ($page - 1) * $limit;

try {
    $news = [];
    
    switch ($filter) {
        case 'featured':
            $news = $newsModel->getFeatured($limit);
            break;
        case 'bansgaonsandesh':
            $news = $newsModel->getDb()->fetchAll("
                SELECT n.*, u.full_name as author_name, c.name as category_name, ci.name as city_name
                FROM news_articles n
                LEFT JOIN users u ON n.user_id = u.id
                LEFT JOIN categories c ON n.category_id = c.id
                LEFT JOIN cities ci ON n.city_id = ci.id
                WHERE n.is_bansgaonsandesh = 1 AND n.is_active = 1
                ORDER BY n.created_at DESC
                LIMIT ? OFFSET ?
            ", [$limit, $offset]);
            break;
        case 'category':
            if ($categoryId) {
                $news = $newsModel->getByCategory($categoryId, $limit);
            }
            break;
        case 'city':
            if ($cityId) {
                $news = $newsModel->getByCity($cityId, $limit);
            }
            break;
        default:
            $news = $newsModel->getDb()->fetchAll("
                SELECT n.*, u.full_name as author_name, c.name as category_name, ci.name as city_name
                FROM news_articles n
                LEFT JOIN users u ON n.user_id = u.id
                LEFT JOIN categories c ON n.category_id = c.id
                LEFT JOIN cities ci ON n.city_id = ci.id
                WHERE n.is_published = 1 AND n.is_active = 1
                ORDER BY n.created_at DESC
                LIMIT ? OFFSET ?
            ", [$limit, $offset]);
            break;
    }
    
    // Process news data for frontend
    $processedNews = [];
    foreach ($news as $item) {
        $processedNews[] = [
            'id' => $item['id'],
            'title' => $item['title'],
            'excerpt' => $item['excerpt'] ?: substr(strip_tags($item['content']), 0, 150) . '...',
            'content' => $item['content'],
            'featured_image' => $item['featured_image'] ? base_url('public/' . $item['featured_image']) : null,
            'category_id' => $item['category_id'],
            'category_name' => $item['category_name'],
            'city_id' => $item['city_id'],
            'city_name' => $item['city_name'],
            'author_name' => $item['author_name'],
            'is_bansgaonsandesh' => $item['is_bansgaonsandesh'],
            'is_featured' => $item['is_featured'],
            'views' => $item['views'],
            'created_at' => $item['created_at'],
            'updated_at' => $item['updated_at']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'data' => $processedNews,
        'page' => $page,
        'has_more' => count($processedNews) === $limit
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error loading news: ' . $e->getMessage()
    ]);
}
?>
