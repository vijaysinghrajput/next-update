<?php
// Main entry point for Next Update Application

// Bootstrap the application
require_once __DIR__ . '/bootstrap-fixed.php';

// Get current request info
$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = rtrim($uri, '/');

// Fix base path calculation for production
$basePath = '';
if (isset($_SERVER['SCRIPT_NAME'])) {
    $basePath = dirname($_SERVER['SCRIPT_NAME']);
    if ($basePath === '/' || $basePath === '\\') {
        $basePath = '';
    }
}

// Remove base path from URI
if (!empty($basePath) && strpos($uri, $basePath) === 0) {
    $uri = substr($uri, strlen($basePath));
}

// Additional production fixes
$uri = trim($uri, '/');
$uri = '/' . $uri;

$uri = $uri ?: '/';

// Your original routing logic
$routes = [
    '/' => 'pages/website/index.php',
    '/about' => 'pages/website/about.php',
    '/contact' => 'pages/website/contact.php',
    '/login' => 'pages/website/login.php',
    '/signup' => 'pages/website/signup.php',
    '/admin' => 'pages/admin/working-dashboard.php',
    '/admin/login' => 'pages/admin/login.php',
    '/admin/news' => 'pages/admin/news.php',
    '/admin/users' => 'pages/admin/users.php',
    '/admin/categories' => 'pages/admin/categories.php',
    '/admin/cities' => 'pages/admin/cities.php',
    '/admin/kyc' => 'pages/admin/kyc.php',
    '/admin/payments' => 'pages/admin/payments.php',
    '/admin/ads' => 'pages/admin/ads.php',
    '/admin/settings' => 'pages/admin/settings.php',
    '/dashboard' => 'pages/user/dashboard.php',
    '/profile' => 'pages/user/profile.php',
    '/my-news' => 'pages/user/my-news.php',
    '/post-news' => 'pages/user/post-news.php',
    '/kyc-verification' => 'pages/user/kyc-verification.php',
    '/buy-points' => 'pages/user/buy-points.php',
    '/post-ad' => 'pages/user/post-ad.php',
    '/referrals' => 'pages/user/referrals.php',
    '/transactions' => 'pages/user/transactions.php',
    '/notifications' => 'pages/user/notifications.php',
    '/news' => 'pages/website/news.php',
    '/search' => 'pages/website/search.php',
    '/forgot-password' => 'pages/website/forgot-password.php',
    '/logout' => 'pages/user/logout.php',
];

// Dynamic routes with parameters
$dynamicRoutes = [
    '/news/' => 'pages/website/news-detail.php',
    '/category/' => 'pages/website/category.php',
    '/city/' => 'pages/website/city.php',
    '/admin/news/' => 'pages/admin/news-edit.php',
    '/admin/users/' => 'pages/admin/user-detail.php',
    '/admin/kyc/' => 'pages/admin/kyc-approve.php',
    '/post-news/' => 'pages/user/post-news.php',
    '/reset-password/' => 'pages/website/reset-password.php',
];

// Special delete route
if (preg_match('/^\/post-news\/(\d+)\/delete$/', $uri, $matches)) {
    $newsId = $matches[1];
    $_GET['id'] = $newsId;
    include __DIR__ . '/pages/user/delete-news.php';
    return;
}

// API routes
$apiRoutes = [
    '/api/news' => 'pages/api/news.php',
    '/api/ads' => 'pages/api/ads.php',
    '/api/track-ad' => 'pages/api/track-ad.php',
    '/api/track-news-view' => 'pages/api/track-news-view.php',
    '/api/notifications' => 'pages/api/notifications.php',
    '/sw.js' => 'pages/api/service-worker.php',
];

// Check if it's an API route
if (strpos($uri, '/api/') === 0 || $uri === '/sw.js') {
    if (isset($apiRoutes[$uri])) {
        $file = $apiRoutes[$uri];
        if (file_exists(__DIR__ . '/' . $file)) {
            include __DIR__ . '/' . $file;
            return;
        }
    }
}

// Check if it's a regular route
if (isset($routes[$uri])) {
    $file = $routes[$uri];
    if (file_exists(__DIR__ . '/' . $file)) {
        include __DIR__ . '/' . $file;
        return;
    }
}

// Debug: Show what's happening
if (config('app.debug', false)) {
    echo "<!-- Debug: URI='$uri', Looking for route -->";
    error_log("Router Debug: URI='$uri', Method='$method', Script='{$_SERVER['SCRIPT_NAME']}', Request='{$_SERVER['REQUEST_URI']}'");
}

// Check dynamic routes
foreach ($dynamicRoutes as $pattern => $file) {
    if (strpos($uri, $pattern) === 0) {
        if (file_exists(__DIR__ . '/' . $file)) {
            // Extract the parameter from the URI
            $param = substr($uri, strlen($pattern));
            // Set the parameter for the page to use
            $_GET['slug'] = $param;
            include __DIR__ . '/' . $file;
            return;
        }
    }
}

// Handle image uploads
if (strpos($uri, '/uploads/') === 0) {
    $file = 'pages/api/image.php';
    if (file_exists(__DIR__ . '/' . $file)) {
        include __DIR__ . '/' . $file;
        return;
    }
}

// 404 Not Found
http_response_code(404);
include __DIR__ . '/pages/errors/404.php';
?>