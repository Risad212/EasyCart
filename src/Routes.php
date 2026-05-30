<?php

namespace App;

use Closure;

class Routes
{
    /** @var array<string, array<string, Closure>> */
    private static array $routes = [];

    /** @var static|null */
    private static ?Routes $instance = null;

    /**
     * Get singleton instance of Routes.
     *
     * @return static
     */
    public static function getInstance(): static
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Register a GET route.
     *
     * @param string  $path     Route path
     * @param Closure $callback Route handler
     * @return void
     */
    public static function get(string $path, Closure $callback): void
    {
        self::$routes['GET'][$path] = $callback;
    }

    /**
     * Register a POST route.
     *
     * @param string  $path     Route path
     * @param Closure $callback Route handler
     * @return void
     */
    public static function post(string $path, Closure $callback): void
    {
        self::$routes['POST'][$path] = $callback;
    }

    /**
     * Dispatch the current request to the matching route.
     *
     * @return void
     */
    public function dispatch(): void
    {
        $method   = $_SERVER['REQUEST_METHOD'];
        $uri      = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Remove project folder from URI dynamically
        $basePath = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
        $basePath = rtrim($basePath, '/');

        if ($basePath !== '' && strpos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath));
        }

        $path    = '/' . trim($uri, '/');
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

    /**
     * Get request data based on HTTP method.
     *
     * @return array
     */
    private function getRequest(): array
    {
        return match ($_SERVER['REQUEST_METHOD']) {
            'POST'  => $_POST,
            'GET'   => $_GET,
            default => []
        };
    }
}