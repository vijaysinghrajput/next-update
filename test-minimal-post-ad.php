<?php
// Minimal post-ad page test
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== MINIMAL POST-AD TEST ===\n";

// Bootstrap
require_once __DIR__ . '/bootstrap.php';
session_start();
$_SESSION['user_id'] = 2;

echo "Bootstrap loaded, session set\n";

// Test the exact same logic as post-ad page
try {
    $userModel = new \App\Models\User();
    $adModel = new \App\Models\Ad();
    $userId = $_SESSION['user_id'];
    $user = $userModel->findById($userId);
    
    echo "User: " . $user['full_name'] . " (Points: " . $user['points'] . ")\n";
    
    $adPositions = $adModel->getAdPositions();
    echo "Ad positions: " . count($adPositions) . "\n";
    
    // Test header include
    echo "Testing header include...\n";
    $headerFile = APP_PATH . '/views/layouts/user-header.php';
    
    if (file_exists($headerFile)) {
        echo "Header file exists, testing include...\n";
        
        // Test if we can include the header without errors
        ob_start();
        include $headerFile;
        $headerContent = ob_get_clean();
        
        echo "Header included successfully, content length: " . strlen($headerContent) . "\n";
    } else {
        echo "Header file does not exist\n";
    }
    
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
} catch (Error $e) {
    echo "Fatal error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}

echo "=== MINIMAL POST-AD TEST COMPLETE ===\n";
?>
