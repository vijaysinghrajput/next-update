<?php
// Final debug for post-ad page
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== POST-AD DEBUG ===\n";

// Bootstrap
require_once __DIR__ . '/bootstrap.php';
session_start();
$_SESSION['user_id'] = 1;

echo "Bootstrap loaded, session set\n";

// Test the exact same code as post-ad page
try {
    $userModel = new \App\Models\User();
    $adModel = new \App\Models\Ad();
    $userId = $_SESSION['user_id'];
    $user = $userModel->findById($userId);
    
    echo "User found: " . ($user ? $user['name'] : 'No user') . "\n";
    
    $adPositions = $adModel->getAdPositions();
    echo "Ad positions: " . count($adPositions) . "\n";
    
    // Test the header include
    echo "Testing header include...\n";
    $headerFile = APP_PATH . '/views/layouts/user-header.php';
    echo "Header file: " . $headerFile . "\n";
    echo "Header exists: " . (file_exists($headerFile) ? 'Yes' : 'No') . "\n";
    
    if (file_exists($headerFile)) {
        // Test if we can read the header file
        $headerContent = file_get_contents($headerFile);
        echo "Header file size: " . strlen($headerContent) . " bytes\n";
        
        // Check if config function is available
        if (function_exists('config')) {
            echo "Config function available\n";
            $appName = config('app_name');
            echo "App name: " . $appName . "\n";
        } else {
            echo "Config function NOT available\n";
        }
    }
    
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
} catch (Error $e) {
    echo "Fatal error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}

echo "=== DEBUG COMPLETE ===\n";
?>
