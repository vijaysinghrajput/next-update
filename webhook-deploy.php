<?php
// Webhook for automatic deployment from GitHub
// Place this file in your production server root

// Security: Only allow requests from GitHub
$github_secret = 'your-webhook-secret-here'; // Set this in GitHub webhook settings
$signature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';

if ($signature) {
    $payload = file_get_contents('php://input');
    $expected_signature = 'sha256=' . hash_hmac('sha256', $payload, $github_secret);
    
    if (!hash_equals($signature, $expected_signature)) {
        http_response_code(401);
        die('Unauthorized');
    }
}

// Log the deployment
$log_entry = date('Y-m-d H:i:s') . " - Deployment triggered\n";
file_put_contents('deployment.log', $log_entry, FILE_APPEND);

// Execute deployment
$output = [];
$return_code = 0;

// Pull latest changes
exec('cd ' . __DIR__ . ' && git pull origin main 2>&1', $output, $return_code);

// Set permissions
exec('chmod 755 public/uploads/ 2>&1', $output);
exec('chmod 755 storage/sessions/ 2>&1', $output);
exec('chmod 644 config/app.php 2>&1', $output);

// Log results
$log_entry = date('Y-m-d H:i:s') . " - Deployment completed. Return code: $return_code\n";
file_put_contents('deployment.log', $log_entry, FILE_APPEND);

if ($return_code === 0) {
    http_response_code(200);
    echo json_encode(['status' => 'success', 'message' => 'Deployment completed successfully']);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Deployment failed', 'output' => $output]);
}
?>
