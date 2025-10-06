<?php
namespace App\Models;

use App\Services\Database;

class User {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function create($data) {
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $data['referral_code'] = $this->generateReferralCode();
        $data['points'] = config('welcome_points', 10);
        $data['total_earned_points'] = config('welcome_points', 10);
        $data['total_spent_points'] = 0;
        $data['created_at'] = date('Y-m-d H:i:s');
        
        return $this->db->insert('users', $data);
    }
    
    public function findByEmail($email) {
        return $this->db->fetch("SELECT * FROM users WHERE email = ?", [$email]);
    }
    
    public function findByUsername($username) {
        return $this->db->fetch("SELECT * FROM users WHERE username = ?", [$username]);
    }
    
    public function findById($id) {
        return $this->db->fetch("SELECT * FROM users WHERE id = ?", [$id]);
    }
    
    public function findByReferralCode($code) {
        return $this->db->fetch("SELECT * FROM users WHERE referral_code = ?", [$code]);
    }
    
    public function update($id, $data) {
        return $this->db->update('users', $data, 'id = ?', [$id]);
    }
    
    public function updateLastLogin($id) {
        return $this->update($id, ['last_login' => date('Y-m-d H:i:s')]);
    }
    
    public function addPoints($id, $points, $skipTransaction = false) {
        if (!$skipTransaction) {
            $this->db->beginTransaction();
        }
        try {
            $this->db->query("UPDATE users SET points = points + ?, total_earned_points = total_earned_points + ? WHERE id = ?", 
                [$points, $points, $id]);
            
            if (!$skipTransaction) {
                $this->db->commit();
            }
            
            // Update session points if this is the current user
            if (session('user_id') == $id) {
                $newPoints = session('points', 0) + $points;
                session('points', $newPoints);
            }
            
            return true;
        } catch (Exception $e) {
            if (!$skipTransaction) {
                $this->db->rollback();
            }
            throw $e;
        }
    }
    
    public function spendPoints($id, $points, $skipTransaction = false) {
        $user = $this->findById($id);
        if (!$user || $user['points'] < $points) {
            throw new \Exception('Insufficient points');
        }
        
        if (!$skipTransaction) {
            $this->db->beginTransaction();
        }
        try {
            $this->db->query("UPDATE users SET points = points - ?, total_spent_points = total_spent_points + ? WHERE id = ?", 
                [$points, $points, $id]);
            
            if (!$skipTransaction) {
                $this->db->commit();
            }
            
            // Update session points if this is the current user
            if (session('user_id') == $id) {
                $newPoints = session('points', 0) - $points;
                session('points', $newPoints);
            }
            
            return true;
        } catch (Exception $e) {
            if (!$skipTransaction) {
                $this->db->rollback();
            }
            throw $e;
        }
    }
    
    public function getStats($id) {
        $user = $this->findById($id);
        if (!$user) return null;
        
        // Get referral count
        $referralCount = $this->db->fetch("SELECT COUNT(*) as count FROM referrals WHERE referrer_id = ?", [$id])['count'];
        
        // Get ad count
        $adCount = $this->db->fetch("SELECT COUNT(*) as count FROM user_ads WHERE user_id = ?", [$id])['count'];
        
        return array_merge($user, [
            'referral_count' => $referralCount,
            'ad_count' => $adCount
        ]);
    }
    
    public function getReferrals($id) {
        return $this->db->fetchAll("
            SELECT r.*, u.full_name as referred_name, u.email as referred_email, u.created_at as joined_date 
            FROM referrals r 
            JOIN users u ON r.referred_id = u.id 
            WHERE r.referrer_id = ? 
            ORDER BY r.created_at DESC
        ", [$id]);
    }
    
    public function getTransactions($id, $limit = 20) {
        return $this->db->fetchAll("
            SELECT * FROM user_transactions 
            WHERE user_id = ? 
            ORDER BY created_at DESC 
            LIMIT ?
        ", [$id, $limit]);
    }
    
    public function getNotifications($id, $limit = 10) {
        return $this->db->fetchAll("
            SELECT * FROM user_notifications 
            WHERE user_id = ? 
            ORDER BY created_at DESC 
            LIMIT ?
        ", [$id, $limit]);
    }
    
    public function getKYCStatus($id) {
        return $this->db->fetch("
            SELECT * FROM kyc_verifications 
            WHERE user_id = ? 
            ORDER BY created_at DESC 
            LIMIT 1
        ", [$id]);
    }
    
    public function submitKYC($id, $documentType, $documentNumber, $documentImage) {
        $verificationCost = config('kyc_verification_cost', 50);
        
        // Check if user has enough points
        $user = $this->findById($id);
        if ($user['points'] < $verificationCost) {
            throw new \Exception("You need {$verificationCost} points to submit KYC verification");
        }
        
        $this->db->beginTransaction();
        try {
            // Deduct points (skip transaction as we're already in one)
            $this->spendPoints($id, $verificationCost, true);
            
            // Update user status using direct query to avoid parameter binding issues
            $this->db->query("UPDATE users SET kyc_status = ?, verification_points_spent = ? WHERE id = ?", 
                ['pending', $user['verification_points_spent'] + $verificationCost, $id]);
            
            // Insert KYC record
            $kycId = $this->db->insert('kyc_verifications', [
                'user_id' => $id,
                'document_type' => $documentType,
                'document_number' => $documentNumber,
                'document_image' => $documentImage,
                'status' => 'pending'
            ]);
            
            // Record transaction for KYC fee
            $this->recordTransaction($id, 'spent', $verificationCost, 'KYC verification fee', 'kyc', $kycId);
            
            // Create notification
            $this->createNotification($id, 'KYC Verification Submitted', 
                'Your KYC verification has been submitted and is under review. You spent ' . $verificationCost . ' points.', 'info');
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
    
    public function createNotification($userId, $title, $message, $type = 'info') {
        return $this->db->insert('user_notifications', [
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type
        ]);
    }
    
    public function recordTransaction($userId, $type, $points, $description, $referenceType = null, $referenceId = null) {
        return $this->db->insert('user_transactions', [
            'user_id' => $userId,
            'transaction_type' => $type,
            'points' => $points,
            'description' => $description,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId
        ]);
    }
    
    public function processReferral($referrerId, $referredId) {
        $referralBonus = config('referral_points', 10);
        
        $this->db->beginTransaction();
        try {
            // Add points to referrer
            $this->addPoints($referrerId, $referralBonus);
            
            // Record transaction
            $this->recordTransaction($referrerId, 'earned', $referralBonus, 'Referral bonus', 'referral', $referredId);
            
            // Insert referral record
            $this->db->insert('referrals', [
                'referrer_id' => $referrerId,
                'referred_id' => $referredId,
                'points_earned' => $referralBonus
            ]);
            
            // Get names for notification
            $referrer = $this->findById($referrerId);
            $referred = $this->findById($referredId);
            
            // Create notification
            $this->createNotification($referrerId, 'Referral Bonus Earned!', 
                "You earned {$referralBonus} points for referring {$referred['full_name']}!", 'success');
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
    
    private function generateReferralCode() {
        do {
            $code = strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
            $exists = $this->findByReferralCode($code);
        } while ($exists);
        
        return $code;
    }
    
    public function getCities() {
        return $this->db->fetchAll("SELECT * FROM cities WHERE is_active = 1 ORDER BY name");
    }
    
    public function getCategories() {
        return $this->db->fetchAll("SELECT * FROM categories WHERE is_active = 1 ORDER BY name");
    }
    
    public function createPaymentRequest($userId, $points, $amount, $paymentScreenshot) {
        return $this->db->insert('user_payments', [
            'user_id' => $userId,
            'points' => $points,
            'amount' => $amount,
            'payment_screenshot' => $paymentScreenshot,
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
    
    public function getPaymentHistory($userId, $limit = 20) {
        return $this->db->fetchAll("
            SELECT * FROM user_payments 
            WHERE user_id = ? 
            ORDER BY created_at DESC 
            LIMIT ?
        ", [$userId, $limit]);
    }
    
    public function getAllPendingPayments() {
        return $this->db->fetchAll("
            SELECT p.*, u.full_name as user_name, u.email as user_email 
            FROM user_payments p 
            JOIN users u ON p.user_id = u.id 
            WHERE p.status = 'pending' 
            ORDER BY p.created_at ASC
        ");
    }
    
    public function getAllPayments($limit = 50) {
        return $this->db->fetchAll("
            SELECT p.*, u.full_name as user_name, u.email as user_email 
            FROM user_payments p 
            JOIN users u ON p.user_id = u.id 
            ORDER BY p.created_at DESC 
            LIMIT ?
        ", [$limit]);
    }
    
    public function approvePayment($paymentId) {
        $payment = $this->db->fetch("SELECT * FROM user_payments WHERE id = ?", [$paymentId]);
        if (!$payment) {
            throw new \Exception('Payment not found');
        }
        
        $this->db->beginTransaction();
        try {
            // Add points to user
            $this->addPoints($payment['user_id'], $payment['points'], true);
            
            // Record transaction
            $this->recordTransaction($payment['user_id'], 'earned', $payment['points'], 'Points purchased via UPI payment', 'payment', $paymentId);
            
            // Update payment status
            $this->db->query("UPDATE user_payments SET status = ?, approved_at = ? WHERE id = ?", 
                ['approved', date('Y-m-d H:i:s'), $paymentId]);
            
            // Create notification
            $this->createNotification($payment['user_id'], 'Payment Approved!', 
                "Your payment of ₹{$payment['amount']} has been approved and {$payment['points']} points have been added to your account.", 'success');
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
    
    public function rejectPayment($paymentId, $reason = '') {
        $this->db->beginTransaction();
        try {
            // Update payment status
            $this->db->query("UPDATE user_payments SET status = ?, rejected_at = ?, rejection_reason = ? WHERE id = ?", 
                ['rejected', date('Y-m-d H:i:s'), $reason, $paymentId]);
            
            // Get payment details for notification
            $payment = $this->db->fetch("SELECT * FROM user_payments WHERE id = ?", [$paymentId]);
            if ($payment) {
                // Create notification
                $this->createNotification($payment['user_id'], 'Payment Rejected', 
                    "Your payment of ₹{$payment['amount']} has been rejected. Reason: {$reason}", 'error');
            }
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
    
    public function generateUsername($fullName) {
        // Clean the full name and create username
        $username = strtolower(trim($fullName));
        $username = preg_replace('/[^a-z0-9\s]/', '', $username);
        $username = preg_replace('/\s+/', '', $username);
        
        // Limit to 15 characters
        $username = substr($username, 0, 15);
        
        // Check if username exists and add number if needed
        $originalUsername = $username;
        $counter = 1;
        
        while ($this->findByUsername($username)) {
            $username = $originalUsername . $counter;
            $counter++;
        }
        
        return $username;
    }
    
    public function sendWelcomeEmail($email, $fullName, $referralCode) {
        // For now, just log the email (implement actual email sending later)
        error_log("Welcome email would be sent to: {$email} for user: {$fullName} with referral code: {$referralCode}");
        
        // TODO: Implement actual email sending using PHPMailer or similar
        // This would send a welcome email with:
        // - Welcome message
        // - User's referral code
        // - Instructions on how to earn points
        // - Link to login
        
        return true;
    }
    
    /**
     * Get filtered payments with search and filter options
     */
    public function getFilteredPayments($search = '', $status = '', $dateFrom = '', $dateTo = '') {
        $whereConditions = [];
        $params = [];
        
        // Search filter
        if (!empty($search)) {
            $whereConditions[] = "(u.full_name LIKE ? OR u.email LIKE ? OR up.id LIKE ?)";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }
        
        // Status filter
        if (!empty($status)) {
            $whereConditions[] = "up.status = ?";
            $params[] = $status;
        }
        
        // Date filters
        if (!empty($dateFrom)) {
            $whereConditions[] = "DATE(up.created_at) >= ?";
            $params[] = $dateFrom;
        }
        
        if (!empty($dateTo)) {
            $whereConditions[] = "DATE(up.created_at) <= ?";
            $params[] = $dateTo;
        }
        
        $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
        
        $query = "SELECT up.*, u.full_name as user_name, u.email as user_email 
                  FROM user_payments up 
                  JOIN users u ON up.user_id = u.id 
                  {$whereClause}
                  ORDER BY up.created_at DESC";
        
        return $this->db->fetchAll($query, $params);
    }
    
    /**
     * Get all KYC verifications for admin
     */
    public function getAllKYCVerifications() {
        $query = "SELECT kv.*, u.full_name as user_name, u.email as user_email 
                  FROM kyc_verifications kv 
                  JOIN users u ON kv.user_id = u.id 
                  ORDER BY kv.created_at DESC";
        return $this->db->fetchAll($query);
    }
    
    /**
     * Get pending KYC verifications
     */
    public function getPendingKYCVerifications() {
        $query = "SELECT kv.*, u.full_name as user_name, u.email as user_email 
                  FROM kyc_verifications kv 
                  JOIN users u ON kv.user_id = u.id 
                  WHERE kv.status = 'pending'
                  ORDER BY kv.created_at ASC";
        return $this->db->fetchAll($query);
    }
    
    /**
     * Approve KYC verification
     */
    public function approveKYC($kycId, $adminId) {
        $kyc = $this->db->fetch("SELECT * FROM kyc_verifications WHERE id = ?", [$kycId]);
        if (!$kyc) {
            throw new \Exception('KYC verification not found');
        }
        
        $this->db->beginTransaction();
        try {
            // Update KYC status
            $this->db->query("UPDATE kyc_verifications SET status = ?, verified_by = ?, verified_at = ? WHERE id = ?", 
                ['approved', $adminId, date('Y-m-d H:i:s'), $kycId]);
            
            // Update user status using direct query
            $this->db->query("UPDATE users SET is_verified = 1, kyc_status = 'approved' WHERE id = ?", 
                [$kyc['user_id']]);
            
            // Create notification
            $this->createNotification($kyc['user_id'], 'KYC Verification Approved!', 
                'Your KYC verification has been approved. Your account is now verified.', 'success');
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
    
    /**
     * Reject KYC verification
     */
    public function rejectKYC($kycId, $adminId, $reason = '') {
        $kyc = $this->db->fetch("SELECT * FROM kyc_verifications WHERE id = ?", [$kycId]);
        if (!$kyc) {
            throw new \Exception('KYC verification not found');
        }
        
        $verificationCost = config('kyc_verification_cost', 50);
        
        $this->db->beginTransaction();
        try {
            // Update KYC status
            $this->db->query("UPDATE kyc_verifications SET status = ?, verified_by = ?, verified_at = ?, admin_notes = ? WHERE id = ?", 
                ['rejected', $adminId, date('Y-m-d H:i:s'), $reason, $kycId]);
            
            // Return points to user (skip transaction as we're already in one)
            $this->addPoints($kyc['user_id'], $verificationCost, true);
            
            // Update user status using direct query
            $this->db->query("UPDATE users SET kyc_status = 'rejected' WHERE id = ?", 
                [$kyc['user_id']]);
            
            // Record transaction for point return
            $this->recordTransaction($kyc['user_id'], 'earned', $verificationCost, 'KYC verification rejected - points returned', 'kyc_refund', $kycId);
            
            // Create notification
            $this->createNotification($kyc['user_id'], 'KYC Verification Rejected', 
                'Your KYC verification has been rejected. Reason: ' . $reason . ' Your ' . $verificationCost . ' points have been returned.', 'error');
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
    
    /**
     * Get user transactions by type
     */
    public function getUserTransactions($userId, $type = null) {
        $whereClause = "WHERE user_id = ?";
        $params = [$userId];
        
        if ($type) {
            $whereClause .= " AND transaction_type = ?";
            $params[] = $type;
        }
        
        $query = "SELECT * FROM user_transactions 
                  {$whereClause}
                  ORDER BY created_at DESC 
                  LIMIT 10";
        
        return $this->db->fetchAll($query, $params);
    }
    
    public function getDb() {
        return $this->db;
    }
}
?>
