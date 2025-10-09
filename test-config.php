<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Testing Config class...\n";

// Include bootstrap
require_once __DIR__ . '/bootstrap.php';

echo "Bootstrap included\n";

// Test if Config class exists
if (class_exists('App\Services\Config')) {
    echo "Config class exists\n";
} else {
    echo "Config class does not exist\n";
}

// Test config function
try {
    $appName = config('app_name');
    echo "App name: " . $appName . "\n";
} catch (Exception $e) {
    echo "Config error: " . $e->getMessage() . "\n";
} catch (Error $e) {
    echo "Config fatal error: " . $e->getMessage() . "\n";
}

echo "Config test completed.\n";
?>
