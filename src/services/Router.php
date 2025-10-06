<?php
namespace App\Services;

class Router {
    private static $routes = [];
    private static $currentRoute = null;
    
    public static function add($method, $path, $handler) {
        self::$routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'handler' => $handler
        ];
    }
    
    public static function get($path, $handler) {
        self::add('GET', $path, $handler);
    }
    
    public static function post($path, $handler) {
        self::add('POST', $path, $handler);
    }
    
    public static function put($path, $handler) {
        self::add('PUT', $path, $handler);
    }
    
    public static function delete($path, $handler) {
        self::add('DELETE', $path, $handler);
    }
    
    public static function dispatch() {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = rtrim($uri, '/');
        
        // Remove base path if exists
        $basePath = str_replace($_SERVER['DOCUMENT_ROOT'], '', __DIR__ . '/../..');
        $basePath = str_replace('\\', '/', $basePath);
        if (strpos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath));
        }
        
        // Handle direct file access - redirect to clean URLs
        if (strpos($uri, '/pages/') === 0) {
            $cleanUri = str_replace('/pages/', '/', $uri);
            $cleanUri = str_replace('.php', '', $cleanUri);
            self::redirect($cleanUri, 301);
            return;
        }
        
        $uri = $uri ?: '/';
        
        foreach (self::$routes as $route) {
            if ($route['method'] === $method && self::matchRoute($route['path'], $uri)) {
                self::$currentRoute = $route;
                return self::callHandler($route['handler'], self::extractParams($route['path'], $uri));
            }
        }
        
        // 404 Not Found
        http_response_code(404);
        self::show404();
    }
    
    private static function matchRoute($routePath, $uri) {
        // Convert route path to regex
        $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $routePath);
        
        // Special handling for path parameter
        if (strpos($routePath, '{path}') !== false) {
            $pattern = preg_replace('/\{path\}/', '(.+)', $routePath);
        }
        
        $pattern = '#^' . $pattern . '$#';
        
        return preg_match($pattern, $uri);
    }
    
    private static function extractParams($routePath, $uri) {
        $params = [];
        $routeParts = explode('/', trim($routePath, '/'));
        $uriParts = explode('/', trim($uri, '/'));
        
        for ($i = 0; $i < count($routeParts); $i++) {
            if (isset($routeParts[$i]) && isset($uriParts[$i])) {
                if (preg_match('/\{([^}]+)\}/', $routeParts[$i], $matches)) {
                    $paramName = $matches[1];
                    
                    // Special handling for path parameter (capture everything after the parameter)
                    if ($paramName === 'path') {
                        $params[$paramName] = implode('/', array_slice($uriParts, $i));
                        break;
                    } else {
                        $params[$paramName] = $uriParts[$i];
                    }
                }
            }
        }
        
        return $params;
    }
    
    private static function callHandler($handler, $params = []) {
        if (is_string($handler)) {
            // Controller@method format
            if (strpos($handler, '@') !== false) {
                list($controller, $method) = explode('@', $handler);
                $controllerClass = "App\\Controllers\\{$controller}";
                
                if (class_exists($controllerClass)) {
                    $instance = new $controllerClass();
                    if (method_exists($instance, $method)) {
                        return call_user_func_array([$instance, $method], [$params]);
                    }
                }
            } else {
                // Direct file include
                $file = __DIR__ . '/../../pages/' . $handler . '.php';
                if (file_exists($file)) {
                    extract($params);
                    return include $file;
                }
            }
        } elseif (is_callable($handler)) {
            return call_user_func_array($handler, [$params]);
        }
        
        self::show404();
    }
    
    public static function url($path = '', $params = []) {
        $baseUrl = config('app_url');
        $url = rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
        
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }
        
        return $url;
    }
    
    public static function redirect($path, $statusCode = 302) {
        $url = self::url($path);
        header("Location: $url", true, $statusCode);
        exit;
    }
    
    public static function currentRoute() {
        return self::$currentRoute;
    }
    
    private static function show404() {
        http_response_code(404);
        include __DIR__ . '/../../pages/errors/404.php';
    }
    
    public static function generateSlug($text) {
        // Convert to lowercase
        $text = strtolower($text);
        
        // Remove special characters
        $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
        
        // Replace spaces and multiple hyphens with single hyphen
        $text = preg_replace('/[\s-]+/', '-', $text);
        
        // Trim hyphens from start and end
        $text = trim($text, '-');
        
        return $text;
    }
}
?>
