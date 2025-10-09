<?php
// Generate upload commands for missing images
echo "📋 Manual Upload Commands for Production\n";
echo "========================================\n\n";

$local_uploads = __DIR__ . '/public/uploads/';
$production_url = 'https://next-update.skyablyitsolution.com';

echo "🔧 To fix missing images on production, you need to:\n\n";
echo "1. Access your hosting control panel (cPanel/FTP)\n";
echo "2. Navigate to: /public_html/next-update/public/uploads/\n";
echo "3. Upload the following files from your local machine:\n\n";

// Scan all files
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($local_uploads, RecursiveDirectoryIterator::SKIP_DOTS)
);

$files = [];
foreach ($iterator as $file) {
    if ($file->isFile()) {
        $relative_path = str_replace($local_uploads, '', $file->getPathname());
        $files[] = $relative_path;
    }
}

sort($files);

echo "📁 Files to upload:\n";
foreach ($files as $file) {
    echo "   - public/uploads/$file\n";
}

echo "\n📊 Total files: " . count($files) . "\n\n";

echo "🔗 After uploading, test these URLs:\n";
foreach (array_slice($files, 0, 5) as $file) {
    echo "   - $production_url/public/uploads/$file\n";
}

echo "\n💡 Alternative: Use FileZilla or similar FTP client to sync the entire 'public/uploads' folder\n";
echo "   Local path: " . $local_uploads . "\n";
echo "   Remote path: /public_html/next-update/public/uploads/\n\n";

echo "🚀 Quick Test Commands:\n";
echo "curl -I $production_url/public/uploads/news/68dff0ef151ad_1759506671.png\n";
echo "curl -I $production_url/public/uploads/ads/68e0f9bb434a5_1759574459.png\n";
?>
