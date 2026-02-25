<?php

namespace App\Core;

/**
 * Router maneja el ruteo de la aplicación con soporte para grupos y middlewares
 */
class Router
{
    private array $routes = [];
    private array $groupStack = [];

    /**
     * Define un grupo de rutas con prefijo y/o middleware común
     */
    public function group(array $attributes, callable $callback)
    {
        $this->groupStack[] = $attributes;
        $callback($this);
        array_pop($this->groupStack);
    }

    public function get(string $path, string $handler)
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, string $handler)
    {
        $this->addRoute('POST', $path, $handler);
    }

    private function addRoute(string $method, string $path, string $handler)
    {
        $prefix = '';
        $middlewares = [];

        // Combinar atributos del stack de grupos
        foreach ($this->groupStack as $group) {
            $prefix .= $group['prefix'] ?? '';
            if (isset($group['middleware'])) {
                $middlewares = array_merge($middlewares, (array)$group['middleware']);
            }
        }

        $fullPath = $prefix . $path;

        // Convierte {id} a regex
        $pathRegex = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<\1>[a-zA-Z0-9_\-]+)', $fullPath);
        $pathRegex = "#^" . $pathRegex . "$#";

        $this->routes[] = [
            'method' => $method,
            'pattern' => $pathRegex,
            'handler' => $handler,
            'middlewares' => $middlewares
        ];
    }

    public function dispatch(string $method, string $uri)
    {
        // Elimina los slashes finales y los parámetros de la query
        $uri = strtok($uri, '?');
        if ($uri !== '/') {
            $uri = rtrim($uri, '/');
        }

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && preg_match($route['pattern'], $uri, $matches)) {

                // 1. Ejecutar Middlewares
                foreach ($route['middlewares'] as $middlewareClass) {
                    if (class_exists($middlewareClass)) {
                        $middleware = new $middlewareClass();
                        // El middleware puede retornar null para continuar o una respuesta para detener
                        $result = $middleware->handle();
                        if ($result === false)
                            return; // Middleware detuvo el flujo (ej: redirección)
                    }
                }

                // 2. Ejecutar Controlador
                list($controllerClass, $controllerMethod) = explode('@', $route['handler']);
                $controllerClass = "App\\Controllers\\" . $controllerClass;

                if (class_exists($controllerClass) && method_exists($controllerClass, $controllerMethod)) {
                    // Extrae los parámetros nombrados de $matches
                    $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                    // Instancia el controlador y llama al método con los parámetros
                    $controller = new $controllerClass();
                    return call_user_func_array([$controller, $controllerMethod], $params);
                }
                else {
                    $this->sendNotFound("Controlador o método no encontrado");
                    return;
                }
            }
        }

        $this->sendNotFound("La ruta no existe");
    }

    private function sendNotFound(string $message)
    {
        header("HTTP/1.0 404 Not Found");
        echo "<h1>404 Not Found</h1><p>{$message}</p>";
    }
}