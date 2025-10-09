<?php
namespace App\Models;

use App\Services\Database;

class Ad {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Create a new ad
     */
    public function createAd($userId, $position, $heading, $description, $image, $whatsappNumber, $callNumber, $websiteUrl, $totalDays) {
        // Get cost per day for this position
        $positionCost = $this->getPositionCost($position);
        $totalCost = $positionCost * $totalDays;
        
        $query = "INSERT INTO ads (user_id, position, heading, description, image, whatsapp_number, call_number, website_url, total_days, cost_per_day, total_cost, status) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')";
        
        return $this->db->query($query, [
            $userId, $position, $heading, $description, $image, 
            $whatsappNumber, $callNumber, $websiteUrl, $totalDays, $positionCost, $totalCost
        ]);
    }
    
    /**
     * Get all ads with filters
     */
    public function getAllAds($filters = []) {
        $whereClause = "WHERE 1=1";
        $params = [];
        
        if (!empty($filters['status'])) {
            $whereClause .= " AND status = ?";
            $params[] = $filters['status'];
        }
        
        if (!empty($filters['position'])) {
            $whereClause .= " AND position = ?";
            $params[] = $filters['position'];
        }
        
        if (!empty($filters['user_id'])) {
            $whereClause .= " AND user_id = ?";
            $params[] = $filters['user_id'];
        }
        
        $query = "SELECT a.*, u.full_name as user_name, u.email as user_email, u.phone as user_phone,
                         ap.name as position_name, ap.description as position_description
                  FROM ads a
                  LEFT JOIN users u ON a.user_id = u.id
                  LEFT JOIN ad_positions ap ON a.position = ap.position
                  {$whereClause}
                  ORDER BY a.created_at DESC";
        
        return $this->db->fetchAll($query, $params);
    }
    
    /**
     * Get ads by user ID
     */
    public function getUserAds($userId) {
        $query = "SELECT a.*, ap.name as position_name, ap.description as position_description
                  FROM ads a
                  LEFT JOIN ad_positions ap ON a.position = ap.position
                  WHERE a.user_id = ?
                  ORDER BY a.created_at DESC";
        
        return $this->db->fetchAll($query, [$userId]);
    }
    
    /**
     * Get pending ads for admin approval
     */
    public function getPendingAds() {
        $query = "SELECT a.*, u.full_name as user_name, u.email as user_email, u.phone as user_phone,
                         ap.name as position_name, ap.description as position_description
                  FROM ads a
                  LEFT JOIN users u ON a.user_id = u.id
                  LEFT JOIN ad_positions ap ON a.position = ap.position
                  WHERE a.status = 'pending'
                  ORDER BY a.created_at ASC";
        
        return $this->db->fetchAll($query);
    }
    
    /**
     * Get active ads for display
     */
    public function getActiveAds($position = null) {
        $whereClause = "WHERE a.status = 'active' AND a.start_date <= CURDATE() AND a.end_date >= CURDATE()";
        $params = [];
        
        if ($position) {
            $whereClause .= " AND a.position = ?";
            $params[] = $position;
        }
        
        $query = "SELECT a.*, u.full_name as user_name
                  FROM ads a
                  LEFT JOIN users u ON a.user_id = u.id
                  {$whereClause}
                  ORDER BY a.created_at DESC";
        
        return $this->db->fetchAll($query, $params);
    }
    
    /**
     * Get ad by ID
     */
    public function getAdById($id) {
        $query = "SELECT a.*, u.full_name as user_name, u.email as user_email, u.phone as user_phone,
                         ap.name as position_name, ap.description as position_description
                  FROM ads a
                  LEFT JOIN users u ON a.user_id = u.id
                  LEFT JOIN ad_positions ap ON a.position = ap.position
                  WHERE a.id = ?";
        
        return $this->db->fetch($query, [$id]);
    }
    
    /**
     * Approve ad
     */
    public function approveAd($id, $adminId, $notes = null) {
        $query = "UPDATE ads 
                  SET status = 'approved', 
                      approved_at = NOW(), 
                      approved_by = ?, 
                      admin_notes = ?,
                      start_date = CURDATE(),
                      end_date = DATE_ADD(CURDATE(), INTERVAL total_days DAY)
                  WHERE id = ? AND status = 'pending'";
        
        return $this->db->query($query, [$adminId, $notes, $id]);
    }
    
    /**
     * Reject ad
     */
    public function rejectAd($id, $adminId, $notes = null) {
        $query = "UPDATE ads 
                  SET status = 'rejected', 
                      approved_at = NOW(), 
                      approved_by = ?, 
                      admin_notes = ?
                  WHERE id = ? AND status = 'pending'";
        
        return $this->db->query($query, [$adminId, $notes, $id]);
    }
    
    /**
     * Activate approved ad
     */
    public function activateAd($id) {
        $query = "UPDATE ads 
                  SET status = 'active'
                  WHERE id = ? AND status = 'approved' 
                  AND start_date <= CURDATE() AND end_date >= CURDATE()";
        
        return $this->db->query($query, [$id]);
    }
    
    /**
     * Complete expired ad
     */
    public function completeExpiredAds() {
        $query = "UPDATE ads 
                  SET status = 'completed'
                  WHERE status = 'active' AND end_date < CURDATE()";
        
        return $this->db->query($query);
    }
    
    /**
     * Get ad positions configuration
     */
    public function getAdPositions() {
        $query = "SELECT * FROM ad_positions WHERE is_active = 1 ORDER BY cost_per_day ASC";
        return $this->db->fetchAll($query);
    }
    
    /**
     * Get position cost per day
     */
    public function getPositionCost($position) {
        $query = "SELECT cost_per_day FROM ad_positions WHERE position = ? AND is_active = 1";
        $result = $this->db->fetch($query, [$position]);
        return $result ? $result['cost_per_day'] : 100;
    }
    
    /**
     * Update position cost
     */
    public function updatePositionCost($position, $costPerDay) {
        $query = "UPDATE ad_positions SET cost_per_day = ? WHERE position = ?";
        return $this->db->query($query, [$costPerDay, $position]);
    }
    
    /**
     * Get ad statistics
     */
    public function getAdStats() {
        $stats = [];
        
        // Total ads by status
        $query = "SELECT status, COUNT(*) as count FROM ads GROUP BY status";
        $statusStats = $this->db->fetchAll($query);
        
        foreach ($statusStats as $stat) {
            $stats[$stat['status']] = $stat['count'];
        }
        
        // Total revenue
        $query = "SELECT SUM(total_cost) as total_revenue FROM ads WHERE status IN ('approved', 'active', 'completed')";
        $revenue = $this->db->fetch($query);
        $stats['total_revenue'] = $revenue['total_revenue'] ?? 0;
        
        // Active ads by position
        $query = "SELECT position, COUNT(*) as count FROM ads WHERE status = 'active' GROUP BY position";
        $positionStats = $this->db->fetchAll($query);
        
        foreach ($positionStats as $stat) {
            $stats['active_' . $stat['position']] = $stat['count'];
        }
        
        return $stats;
    }
    
    /**
     * Check if user has enough points for ad
     */
    public function checkUserPoints($userId, $totalCost) {
        $userModel = new User();
        $user = $userModel->getUserById($userId);
        return $user && $user['points'] >= $totalCost;
    }
    
    /**
     * Deduct points for approved ad
     */
    public function deductAdPoints($userId, $totalCost, $adId) {
        $userModel = new User();
        $userModel->spendPoints($userId, $totalCost, true);
        
        // Record transaction
        $query = "INSERT INTO user_transactions (user_id, transaction_type, points, description, reference_type, reference_id) 
                  VALUES (?, 'spent', ?, ?, 'ad', ?)";
        
        $description = "Ad purchase - " . $totalCost . " points";
        $this->db->query($query, [$userId, $totalCost, $description, $adId]);
        
        return true;
    }
    
    /**
     * Refund points for rejected ad
     */
    public function refundAdPoints($userId, $totalCost, $adId) {
        $userModel = new User();
        $userModel->addPoints($userId, $totalCost, true);
        
        // Record transaction
        $query = "INSERT INTO user_transactions (user_id, transaction_type, points, description, reference_type, reference_id) 
                  VALUES (?, 'refund', ?, ?, 'ad', ?)";
        
        $description = "Ad refund - " . $totalCost . " points";
        $this->db->query($query, [$userId, $totalCost, $description, $adId]);
        
        return true;
    }
    
    public function getDb() {
        return $this->db;
    }
}
