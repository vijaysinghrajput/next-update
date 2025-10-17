<?php
namespace App\Models;

use App\Services\Database;

class News {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getFeatured($limit = 5) {
        return $this->db->fetchAll("
            SELECT n.*, u.full_name as author_name, c.name as category_name, ci.name as city_name
            FROM news_articles n
            LEFT JOIN users u ON n.user_id = u.id
            LEFT JOIN categories c ON n.category_id = c.id
            LEFT JOIN cities ci ON n.city_id = ci.id
            WHERE n.is_featured = 1 AND n.is_published = 1 AND n.is_active = 1
            ORDER BY n.created_at DESC
            LIMIT ?
        ", [$limit]);
    }
    
    public function getLatest($limit = 10) {
        return $this->db->fetchAll("
            SELECT n.*, u.full_name as author_name, c.name as category_name, ci.name as city_name
            FROM news_articles n
            LEFT JOIN users u ON n.user_id = u.id
            LEFT JOIN categories c ON n.category_id = c.id
            LEFT JOIN cities ci ON n.city_id = ci.id
            WHERE n.is_published = 1 AND n.is_active = 1
            ORDER BY n.created_at DESC
            LIMIT ?
        ", [$limit]);
    }
    
    public function getById($id) {
        return $this->db->fetch("
            SELECT n.*, u.full_name as author_name, c.name as category_name, ci.name as city_name
            FROM news_articles n
            LEFT JOIN users u ON n.user_id = u.id
            LEFT JOIN categories c ON n.category_id = c.id
            LEFT JOIN cities ci ON n.city_id = ci.id
            WHERE n.id = ? AND n.is_active = 1
        ", [$id]);
    }
    
    public function getByCategory($categoryId, $limit = 10) {
        return $this->db->fetchAll("
            SELECT n.*, u.full_name as author_name, c.name as category_name, ci.name as city_name
            FROM news_articles n
            LEFT JOIN users u ON n.user_id = u.id
            LEFT JOIN categories c ON n.category_id = c.id
            LEFT JOIN cities ci ON n.city_id = ci.id
            WHERE n.category_id = ? AND n.is_active = 1
            ORDER BY n.created_at DESC
            LIMIT ?
        ", [$categoryId, $limit]);
    }
    
    public function getByCity($cityId, $limit = 10) {
        return $this->db->fetchAll("
            SELECT n.*, u.full_name as author_name, c.name as category_name, ci.name as city_name
            FROM news_articles n
            LEFT JOIN users u ON n.user_id = u.id
            LEFT JOIN categories c ON n.category_id = c.id
            LEFT JOIN cities ci ON n.city_id = ci.id
            WHERE n.city_id = ? AND n.is_active = 1
            ORDER BY n.created_at DESC
            LIMIT ?
        ", [$cityId, $limit]);
    }
    
    public function getRelated($newsId, $categoryId, $limit = 5) {
        return $this->db->fetchAll("
            SELECT n.*, u.full_name as author_name, c.name as category_name, ci.name as city_name
            FROM news_articles n
            LEFT JOIN users u ON n.user_id = u.id
            LEFT JOIN categories c ON n.category_id = c.id
            LEFT JOIN cities ci ON n.city_id = ci.id
            WHERE n.category_id = ? AND n.id != ? AND n.is_active = 1
            ORDER BY n.created_at DESC
            LIMIT ?
        ", [$categoryId, $newsId, $limit]);
    }
    
    public function incrementViews($id) {
        return $this->db->query("UPDATE news_articles SET views = views + 1 WHERE id = ?", [$id]);
    }
    
    public function create($data) {
        $data['slug'] = $this->generateSlug($data['title']);
        $data['excerpt'] = $this->generateExcerpt($data['content']);
        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->db->insert('news_articles', $data);
    }
    
    public function update($id, $data) {
        if (isset($data['title'])) {
            $data['slug'] = $this->generateSlug($data['title']);
        }
        if (isset($data['content'])) {
            $data['excerpt'] = $this->generateExcerpt($data['content']);
        }
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->update('news_articles', $data, 'id = ?', [$id]);
    }
    
    public function delete($id) {
        return $this->db->query("UPDATE news_articles SET is_active = 0 WHERE id = ?", [$id])->rowCount();
    }
    
    public function getCategories() {
        return $this->db->fetchAll("SELECT * FROM categories WHERE is_active = 1 ORDER BY name");
    }
    
    public function getCities() {
        return $this->db->fetchAll("SELECT * FROM cities WHERE is_active = 1 ORDER BY name");
    }
    
    public function getCategoryBySlug($slug) {
        return $this->db->fetch("SELECT * FROM categories WHERE slug = ? AND is_active = 1", [$slug]);
    }
    
    public function getCityBySlug($slug) {
        return $this->db->fetch("SELECT * FROM cities WHERE slug = ? AND is_active = 1", [$slug]);
    }
    
    public function search($query, $limit = 20, $offset = 0) {
        return $this->db->fetchAll("
            SELECT n.*, u.full_name as author_name, c.name as category_name, ci.name as city_name
            FROM news_articles n
            LEFT JOIN users u ON n.user_id = u.id
            LEFT JOIN categories c ON n.category_id = c.id
            LEFT JOIN cities ci ON n.city_id = ci.id
            WHERE (n.title LIKE ? OR n.content LIKE ?) AND n.is_active = 1
            ORDER BY n.created_at DESC
            LIMIT ? OFFSET ?
        ", ["%{$query}%", "%{$query}%", $limit, $offset]);
    }
    
    public function getSearchCount($query) {
        $result = $this->db->fetch("
            SELECT COUNT(*) as count
            FROM news_articles n
            WHERE (n.title LIKE ? OR n.content LIKE ?) AND n.is_active = 1
        ", ["%{$query}%", "%{$query}%"]);
        return $result['count'] ?? 0;
    }
    
    public function getByUser($userId, $limit = 20) {
        return $this->db->fetchAll("
            SELECT n.*, c.name as category_name, ci.name as city_name
            FROM news_articles n
            LEFT JOIN categories c ON n.category_id = c.id
            LEFT JOIN cities ci ON n.city_id = ci.id
            WHERE n.user_id = ? AND n.is_active = 1
            ORDER BY n.created_at DESC
            LIMIT ?
        ", [$userId, $limit]);
    }
    
    public function getPopular($limit = 10) {
        return $this->db->fetchAll("
            SELECT n.*, u.full_name as author_name, c.name as category_name, ci.name as city_name
            FROM news_articles n
            LEFT JOIN users u ON n.user_id = u.id
            LEFT JOIN categories c ON n.category_id = c.id
            LEFT JOIN cities ci ON n.city_id = ci.id
            WHERE n.is_active = 1
            ORDER BY n.views DESC, n.created_at DESC
            LIMIT ?
        ", [$limit]);
    }
    
    private function generateSlug($title) {
        $slug = strtolower(trim($title));
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/[\s-]+/', '-', $slug);
        $slug = trim($slug, '-');
        
        // Check if slug exists and add number if needed
        $originalSlug = $slug;
        $counter = 1;
        
        while ($this->slugExists($slug)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
    
    private function slugExists($slug) {
        $result = $this->db->fetch("SELECT id FROM news_articles WHERE slug = ?", [$slug]);
        return $result !== false;
    }
    
    private function generateExcerpt($content, $length = 150) {
        $excerpt = strip_tags($content);
        $excerpt = preg_replace('/\s+/', ' ', $excerpt);
        $excerpt = trim($excerpt);
        
        if (strlen($excerpt) > $length) {
            $excerpt = substr($excerpt, 0, $length);
            $excerpt = substr($excerpt, 0, strrpos($excerpt, ' ')) . '...';
        }
        
        return $excerpt;
    }
    
    public function getBySlug($slug) {
        return $this->db->fetch("
            SELECT n.*, u.full_name as author_name, c.name as category_name, ci.name as city_name
            FROM news_articles n
            LEFT JOIN users u ON n.user_id = u.id
            LEFT JOIN categories c ON n.category_id = c.id
            LEFT JOIN cities ci ON n.city_id = ci.id
            WHERE n.slug = ? AND n.is_active = 1
        ", [$slug]);
    }
    
    public function getDb() {
        return $this->db;
    }
}
?>
