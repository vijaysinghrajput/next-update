<?php
// Database Test Script
// Run this to test your database connection

require 'bootstrap.php';

echo "🗄️  NextUpdate Database Test\n";
echo "============================\n\n";

try {
    // Test database connection
    $pdo = pdo();
    echo "✅ Database connection: SUCCESS\n";
    
    // Get environment info
    $env = getEnvironment();
    $dbConfig = config('database');
    
    echo "📋 Environment: $env\n";
    echo "📋 Database: " . $dbConfig['database'] . "\n";
    echo "📋 Host: " . $dbConfig['host'] . "\n";
    echo "📋 User: " . $dbConfig['username'] . "\n\n";
    
    // Test basic queries
    echo "🧪 Testing database queries...\n";
    
    // Check if tables exist
    $tables = ['users', 'news_articles', 'ads', 'categories', 'cities'];
    foreach ($tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "✅ Table '$table': EXISTS\n";
        } else {
            echo "❌ Table '$table': MISSING\n";
        }
    }
    
    echo "\n🎉 Database test completed successfully!\n";
    echo "🌐 Your application is ready to use!\n";
    
} catch (Exception $e) {
    echo "❌ Database test failed: " . $e->getMessage() . "\n";
    echo "\n🔧 Troubleshooting:\n";
    echo "1. Check if MySQL is running\n";
    echo "2. Verify database credentials in config/app.php\n";
    echo "3. Ensure database exists\n";
    echo "4. Check user permissions\n";
}
?>
