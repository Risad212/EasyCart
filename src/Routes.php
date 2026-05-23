<?php

namespace App;

use Closure;

class Routes
{
    private static $routes = [];
    private static $instance = null;

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public static function get(string $path, Closure $callback)
    {
        self::$routes['GET'][$path] = $callback;
    }

    public static function post(string $path, Closure $callback)
    {
        self::$routes['POST'][$path] = $callback;
    }

    public function dispatch()
    {
        $method = $_SERVER['REQUEST_METHOD'];

        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // 🔥 FIX: remove project folder dynamically (no hardcode issue later)
        $basePath = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
        $basePath = rtrim($basePath, '/');

        if ($basePath !== '' && strpos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath));
        }

        $path = '/' . trim($uri, '/');

        $request = $this->getRequest();

        if (isset(self::$routes[$method][$path])) {
            $callback = self::$routes[$method][$path];

            $callback($request);
            exit;
        }

        http_response_code(404);
        echo '404 Not Found';
        exit;
    }

    private function getRequest()
    {
        $method = $_SERVER['REQUEST_METHOD'];

        return match ($method) {
            'POST' => $_POST,
            'GET' => $_GET,
            default => []
        };
    }
}
