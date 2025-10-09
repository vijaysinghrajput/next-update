<?php
// Script to upload missing images to production server
// This script will upload all local images to the production server

echo "🔄 Uploading Images to Production Server\n";
echo "=====================================\n\n";

// Production server details
$production_host = 'skyablyitsolution.com';
$production_user = 'u715885454';
$production_path = '/domains/skyablyitsolution.com/public_html/next-update/public/uploads/';

// Local paths
$local_uploads = __DIR__ . '/public/uploads/';

// Function to upload file via FTP
function uploadFile($local_file, $remote_file, $host, $user, $password) {
    $connection = ftp_connect($host);
    if (!$connection) {
        echo "❌ Failed to connect to FTP server\n";
        return false;
    }
    
    $login = ftp_login($connection, $user, $password);
    if (!$login) {
        echo "❌ Failed to login to FTP server\n";
        return false;
    }
    
    // Enable passive mode
    ftp_pasv($connection, true);
    
    // Upload the file
    $result = ftp_put($connection, $remote_file, $local_file, FTP_BINARY);
    ftp_close($connection);
    
    return $result;
}

// Function to create directory structure
function createDirectory($path, $connection) {
    $dirs = explode('/', $path);
    $current_path = '';
    
    foreach ($dirs as $dir) {
        if (empty($dir)) continue;
        
        $current_path .= '/' . $dir;
        
        if (!@ftp_chdir($connection, $current_path)) {
            if (!@ftp_mkdir($connection, $current_path)) {
                echo "❌ Failed to create directory: $current_path\n";
                return false;
            }
            @ftp_chmod($connection, 0755, $current_path);
        }
    }
    
    return true;
}

// Get FTP credentials (you'll need to add these)
$ftp_password = 'YourFTPPassword'; // Add your FTP password here

echo "📁 Scanning local uploads directory...\n";
echo "Local path: $local_uploads\n\n";

// Scan all files in uploads directory
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($local_uploads, RecursiveDirectoryIterator::SKIP_DOTS)
);

$files_to_upload = [];
foreach ($iterator as $file) {
    if ($file->isFile()) {
        $relative_path = str_replace($local_uploads, '', $file->getPathname());
        $files_to_upload[] = [
            'local' => $file->getPathname(),
            'remote' => $production_path . $relative_path,
            'relative' => $relative_path
        ];
    }
}

echo "📊 Found " . count($files_to_upload) . " files to upload\n\n";

// Connect to FTP
echo "🔌 Connecting to production server...\n";
$connection = ftp_connect($production_host);
if (!$connection) {
    echo "❌ Failed to connect to FTP server\n";
    exit(1);
}

$login = ftp_login($connection, $production_user, $ftp_password);
if (!$login) {
    echo "❌ Failed to login to FTP server\n";
    exit(1);
}

ftp_pasv($connection, true);
echo "✅ Connected to production server\n\n";

// Upload files
$uploaded = 0;
$failed = 0;

foreach ($files_to_upload as $file_info) {
    echo "📤 Uploading: {$file_info['relative']}... ";
    
    // Create directory if it doesn't exist
    $remote_dir = dirname($file_info['remote']);
    if (!@ftp_chdir($connection, $remote_dir)) {
        createDirectory($remote_dir, $connection);
    }
    
    // Upload file
    $result = ftp_put($connection, $file_info['remote'], $file_info['local'], FTP_BINARY);
    
    if ($result) {
        echo "✅ Success\n";
        $uploaded++;
    } else {
        echo "❌ Failed\n";
        $failed++;
    }
}

ftp_close($connection);

echo "\n📈 Upload Summary:\n";
echo "✅ Successfully uploaded: $uploaded files\n";
echo "❌ Failed uploads: $failed files\n";
echo "📊 Total files: " . count($files_to_upload) . "\n\n";

if ($failed > 0) {
    echo "⚠️  Some files failed to upload. Please check FTP credentials and permissions.\n";
} else {
    echo "🎉 All images uploaded successfully!\n";
}

echo "\n🔗 Test production URLs:\n";
echo "https://next-update.skyablyitsolution.com/public/uploads/news/\n";
echo "https://next-update.skyablyitsolution.com/public/uploads/ads/\n";
?>
