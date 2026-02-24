<?php
// public/index.php
// Front Controller
require_once __DIR__ . '/../app/Core/Autoloader.php';

use App\Core\Autoloader;
use App\Core\Config;
use App\Core\Router;
use App\Core\Database;

// Carga la clase Autoloader
Autoloader::register();

// Carga la configuración del archivo .env
try {
    Config::load(__DIR__ . '/../.env');
}
catch (Exception $e) {
    die("Configuration Error: " . $e->getMessage());
}

// Evita que la sesión sea accesible vía JavaScript
// Como medida de seguridad de la sesión
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
session_start();

// Initialize the Router
$router = new Router();

// Catálogo y Producto
$router->get('/', 'ProductController@index');
$router->get('/producto/{id}', 'ProductController@show');

// Carrito
$router->get('/carrito', 'CartController@showCart');
$router->post('/carrito/agregar', 'CartController@add');

// URI dispatching
$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// El ruteador se encarga de llamar al controlador correspondiente
$router->dispatch($requestMethod, $requestUri);