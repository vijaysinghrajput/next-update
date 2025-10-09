<?php
/**
 * Cron job to auto-activate approved ads and complete expired ads
 * Run this every hour: 0 * * * * php /path/to/activate-ads.php
 */

// Set up environment
require_once __DIR__ . '/../src/services/Database.php';
require_once __DIR__ . '/../src/models/Ad.php';
require_once __DIR__ . '/../src/helpers/AdHelper.php';

use App\Helpers\AdHelper;

// Set timezone
date_default_timezone_set('UTC');

// Log function
function logMessage($message) {
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[{$timestamp}] {$message}" . PHP_EOL;
    file_put_contents(__DIR__ . '/../logs/ads-activation.log', $logMessage, FILE_APPEND | LOCK_EX);
}

try {
    logMessage("Starting ad activation process...");
    
    // Auto-activate ads
    AdHelper::autoActivateAds();
    
    // Get statistics
    $adModel = new Ad();
    $stats = $adModel->getAdStats();
    
    logMessage("Ad activation completed. Stats: " . json_encode($stats));
    
} catch (Exception $e) {
    logMessage("Error in ad activation: " . $e->getMessage());
}

logMessage("Ad activation process finished.");
?>
