<?php
// Test session and user authentication
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== SESSION TEST ===\n";

// Bootstrap
require_once __DIR__ . '/bootstrap.php';
session_start();

echo "Session ID: " . session_id() . "\n";
echo "Session data: " . print_r($_SESSION, true) . "\n";

// Test login
$userModel = new \App\Models\User();
$user = $userModel->findByEmail('iamvijaysinghrajput@gmail.com');

if ($user) {
    echo "User found: " . $user['full_name'] . " (ID: " . $user['id'] . ")\n";
    
    // Set session
    $_SESSION['user_id'] = $user['id'];
    echo "Session set with user_id: " . $_SESSION['user_id'] . "\n";
    
    // Test if user is logged in
    if (isset($_SESSION['user_id'])) {
        echo "User is logged in\n";
        
        // Test the post-ad page logic
        $userId = $_SESSION['user_id'];
        $user = $userModel->findById($userId);
        
        if ($user) {
            echo "User retrieved: " . $user['full_name'] . "\n";
            echo "User points: " . $user['points'] . "\n";
        } else {
            echo "User not found by ID\n";
        }
    } else {
        echo "User is NOT logged in\n";
    }
} else {
    echo "User not found by email\n";
}

echo "=== SESSION TEST COMPLETE ===\n";
?>
