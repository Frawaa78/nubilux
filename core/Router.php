<?php

class Router {
    private $routes = [];
    private $middleware = [];
    
    public function __construct() {
        $this->loadRoutes();
    }
    
    public function get($path, $callback) {
        $this->addRoute('GET', $path, $callback);
    }
    
    public function post($path, $callback) {
        $this->addRoute('POST', $path, $callback);
    }
    
    private function addRoute($method, $path, $callback) {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'callback' => $callback
        ];
    }
    
    public function resolve() {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        foreach ($this->routes as $route) {
            if ($route['method'] === $requestMethod && $this->matchPath($route['path'], $requestUri)) {
                return $this->executeCallback($route['callback'], $requestUri);
            }
        }
        
        // Default fallback to existing system
        $this->handleLegacyRouting($requestUri);
    }
    
    private function matchPath($routePath, $requestUri) {
        // Simple exact match for now, can be extended with parameters
        return $routePath === $requestUri || ($routePath === '/' && $requestUri === '/index.php');
    }
    
    private function executeCallback($callback, $uri) {
        if (is_string($callback) && strpos($callback, '@') !== false) {
            [$controller, $method] = explode('@', $callback);
            $controllerFile = __DIR__ . "/../app/controllers/{$controller}.php";
            
            if (file_exists($controllerFile)) {
                require_once $controllerFile;
                $controllerInstance = new $controller();
                return $controllerInstance->$method();
            }
        }
        
        if (is_callable($callback)) {
            return $callback();
        }
        
        throw new Exception("Invalid route callback");
    }
    
    private function handleLegacyRouting($uri) {
        // Handle existing file-based routing
        $file = ltrim($uri, '/');
        if (empty($file) || $file === 'index.php') {
            $file = 'index.php';
        }
        
        $fullPath = __DIR__ . "/../{$file}";
        if (file_exists($fullPath)) {
            include $fullPath;
        } else {
            http_response_code(404);
            echo "Page not found";
        }
    }
    
    private function loadRoutes() {
        $routesFile = __DIR__ . '/../config/routes.php';
        if (file_exists($routesFile)) {
            require_once $routesFile;
        }
    }
}