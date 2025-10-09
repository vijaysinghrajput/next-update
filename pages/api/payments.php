<?php
require_once __DIR__ . '/../../bootstrap-fixed.php';

use App\Models\User;

// Set JSON header
header('Content-Type: application/json');

// Check if user is logged in and is admin
if (!session('user_id') || !session('is_admin')) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$userModel = new User();

try {
    // Get request method and parameters
    $method = $_SERVER['REQUEST_METHOD'];
    $action = $_GET['action'] ?? '';
    
    if ($method === 'GET' && $action === 'list') {
        // Pagination settings
        $page = (int)($_GET['page'] ?? 1);
        $limit = (int)($_GET['limit'] ?? 10);
        $offset = ($page - 1) * $limit;
        
        // Search and filter parameters
        $search = trim($_GET['search'] ?? '');
        $status = $_GET['status'] ?? '';
        $dateFrom = $_GET['date_from'] ?? '';
        $dateTo = $_GET['date_to'] ?? '';
        
        // Build where conditions
        $whereConditions = [];
        $params = [];
        
        if (!empty($search)) {
            $whereConditions[] = "(u.full_name LIKE ? OR u.email LIKE ? OR up.id LIKE ?)";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }
        
        if (!empty($status)) {
            $whereConditions[] = "up.status = ?";
            $params[] = $status;
        }
        
        if (!empty($dateFrom)) {
            $whereConditions[] = "DATE(up.created_at) >= ?";
            $params[] = $dateFrom;
        }
        
        if (!empty($dateTo)) {
            $whereConditions[] = "DATE(up.created_at) <= ?";
            $params[] = $dateTo;
        }
        
        $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
        
        // Get total count for pagination
        $countQuery = "SELECT COUNT(*) as total FROM user_payments up 
                       JOIN users u ON up.user_id = u.id {$whereClause}";
        $totalResult = $userModel->db->fetch($countQuery, $params);
        $totalRecords = $totalResult['total'];
        $totalPages = ceil($totalRecords / $limit);
        
        // Get paginated payments
        $paymentsQuery = "SELECT up.*, u.full_name as user_name, u.email as user_email 
                          FROM user_payments up 
                          JOIN users u ON up.user_id = u.id 
                          {$whereClause}
                          ORDER BY up.created_at DESC 
                          LIMIT {$limit} OFFSET {$offset}";
        $allPayments = $userModel->db->fetchAll($paymentsQuery, $params);
        
        // Format the response
        $response = [
            'success' => true,
            'data' => [
                'payments' => $allPayments,
                'pagination' => [
                    'current_page' => $page,
                    'total_pages' => $totalPages,
                    'total_records' => $totalRecords,
                    'limit' => $limit,
                    'offset' => $offset
                ]
            ]
        ];
        
        echo json_encode($response);
        
    } elseif ($method === 'POST' && $action === 'approve') {
        // Approve payment
        $paymentId = (int)($_POST['payment_id'] ?? 0);
        
        if ($paymentId <= 0) {
            throw new Exception('Invalid payment ID');
        }
        
        $userModel->approvePayment($paymentId);
        
        echo json_encode([
            'success' => true,
            'message' => 'Payment approved successfully! Points have been added to user\'s account.'
        ]);
        
    } elseif ($method === 'POST' && $action === 'reject') {
        // Reject payment
        $paymentId = (int)($_POST['payment_id'] ?? 0);
        $reason = trim($_POST['reason'] ?? '');
        
        if ($paymentId <= 0) {
            throw new Exception('Invalid payment ID');
        }
        
        if (empty($reason)) {
            throw new Exception('Please provide a reason for rejection');
        }
        
        $userModel->rejectPayment($paymentId, $reason);
        
        echo json_encode([
            'success' => true,
            'message' => 'Payment rejected successfully.'
        ]);
        
    } else {
        throw new Exception('Invalid action');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
