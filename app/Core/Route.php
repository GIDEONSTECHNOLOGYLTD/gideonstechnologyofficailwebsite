<?php
namespace App\Core;

class Route
{
    private $method;
    private $path;
    private $handler;
    private $middlewares = [];

    public function __construct($method, $path, $handler)
    {
        $this->method = $method;
        $this->path = $path;
        $this->handler = $handler;
    }

    protected function buildPattern($path)
    {
        return '#^' . preg_replace('#\{([^/]+)\}#', '([^/]+)', $path) . '$#';
    }

    public function middleware($middleware)
    {
        $this->middlewares[] = $middleware;
        return $this;
    }

    public function matches($method, $path)
    {
        return $this->method === $method && preg_match($this->buildPattern($this->path), $path);
    }

    public function getParams($path)
    {
        if (preg_match($this->buildPattern($this->path), $path, $matches)) {
            array_shift($matches);
            return $matches;
        }
        return [];
    }

    public function getHandler()
    {
        return $this->handler;
    }

    public function getMiddlewares()
    {
        return $this->middlewares;
    }

    public static function prefix($prefix, $callback): void
    {
        $oldPrefix = self::$prefix;
        self::$prefix = $prefix;

        $callback();

        self::$prefix = $oldPrefix;
    }

    private static function addRoute($method, $path, $controller, $name = null): void
    {
        $fullPath = self::$prefix . trim($path, '/');
        
        self::$routes[$fullPath] = [
            'method' => $method,
            'controller' => $controller[0],
            'action' => $controller[1]
        ];

        if ($name) {
            self::$namedRoutes[$name] = $fullPath;
        }
    }

    public static function dispatch($uri, $method = 'GET')
    {
        $uri = trim($uri, '/');

        if (isset(self::$routes[$uri]) && self::$routes[$uri]['method'] === $method) {
            self::$currentRoute = self::$routes[$uri];
            return self::callController();
        }

        throw new \Exception("Route not found: $method $uri");
    }

    private static function callController()
    {
        $controller = self::$currentRoute['controller'];
        $action = self::$currentRoute['action'];

        if ($controller === null) {
            return call_user_func($action);
        }

        $controller = new $controller();
        return $controller->$action();
    }

    public static function name($name)
    {
        if (isset(self::$namedRoutes[$name])) {
            return self::$namedRoutes[$name];
        }
        throw new \Exception("Route name not found: $name");
    }

    public static function url($name, $params = [])
    {
        $path = self::name($name);

        // Replace named parameters in path
        if (preg_match_all('/\{([a-zA-Z0-9_]+)\}/', $path, $matches)) {
            foreach ($matches[1] as $key => $param) {
                if (isset($params[$param])) {
                    $path = str_replace("{$matches[0][$key]}", $params[$param], $path);
                }
            }
        }

        return '/' . trim($path, '/');
    }
}
