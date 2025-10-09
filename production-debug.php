<?php
// Production Debug Helper
// Add this to your index.php temporarily to debug routing issues

echo "<h2>Production Debug Information</h2>";
echo "<strong>REQUEST_URI:</strong> " . $_SERVER['REQUEST_URI'] . "<br>";
echo "<strong>SCRIPT_NAME:</strong> " . $_SERVER['SCRIPT_NAME'] . "<br>";
echo "<strong>PHP_SELF:</strong> " . $_SERVER['PHP_SELF'] . "<br>";
echo "<strong>HTTP_HOST:</strong> " . $_SERVER['HTTP_HOST'] . "<br>";
echo "<strong>SERVER_NAME:</strong> " . $_SERVER['SERVER_NAME'] . "<br>";
echo "<strong>DOCUMENT_ROOT:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "<br>";

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
echo "<strong>Parsed URI:</strong> " . $uri . "<br>";

$basePath = dirname($_SERVER['SCRIPT_NAME']);
echo "<strong>Base Path:</strong> " . $basePath . "<br>";

if (!empty($basePath) && strpos($uri, $basePath) === 0) {
    $uri = substr($uri, strlen($basePath));
}
echo "<strong>Final URI:</strong> " . $uri . "<br>";

echo "<strong>File exists check:</strong><br>";
$routes = [
    '/' => 'pages/website/index.php',
    '/signup' => 'pages/website/signup.php',
    '/login' => 'pages/website/login.php',
    '/dashboard' => 'pages/user/dashboard.php',
    '/admin' => 'pages/admin/working-dashboard.php',
];

foreach ($routes as $route => $file) {
    $exists = file_exists(__DIR__ . '/' . $file) ? 'YES' : 'NO';
    echo "Route: $route -> File: $file -> Exists: $exists<br>";
}

echo "<strong>Current working directory:</strong> " . getcwd() . "<br>";
echo "<strong>__DIR__:</strong> " . __DIR__ . "<br>";
?>
