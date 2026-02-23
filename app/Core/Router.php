<?php

namespace App\Core;

// Router es parte del patrón de diseño Singleton y consiste en cargar la ruta
// desde el archivo .env
class Router
{
    private array $routes = []; // Array de rutas

    public function get(string $path, string $handler)
    {
        $this->addRoute('GET', $path, $handler); // Agrega una ruta GET
    }

    public function post(string $path, string $handler)
    {
        $this->addRoute('POST', $path, $handler); // Agrega una ruta POST
    }

    private function addRoute(string $method, string $path, string $handler)
    {
        // Convierte los parámetros dinámicos como {id} a Regex (\d+)
        $pathRegex = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<\1>[a-zA-Z0-9_\-]+)', $path);
        // Nos aseguramos de que la ruta coincida exactamente con la cadena
        $pathRegex = "#^" . $pathRegex . "$#";

        $this->routes[] = [
            'method' => $method,
            'pattern' => $pathRegex,
            'handler' => $handler
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
                // Determina el controlador y el método (e.g., 'ProductController@show')
                list($controllerClass, $controllerMethod) = explode('@', $route['handler']);

                $controllerClass = "App\\Controllers\\" . $controllerClass;

                if (class_exists($controllerClass) && method_exists($controllerClass, $controllerMethod)) {
                    // Extrae los parámetros nombrados de $matches
                    $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                    // Instancia el controlador y llama al método con los parámetros
                    $controller = new $controllerClass();
                    return call_user_func_array([$controller, $controllerMethod], $params); // Llama al método con los parámetros
                }
                else {
                    $this->sendNotFound("El controlador o el método no se encontraron");
                    return;
                }
            }
        }

        $this->sendNotFound("La ruta no se encontró");
    }

    private function sendNotFound(string $message)
    {
        header("HTTP/1.0 404 Not Found");
        echo "<h1>404 Not Found</h1><p>{$message}</p>";
    }
}