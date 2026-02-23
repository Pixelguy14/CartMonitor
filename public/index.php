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

// La ruta /prueba se mapea al método index de la clase TestController
// Con el fin de probar el router manual
$router->get('/prueba', 'TestController@index');

// La ruta /producto/{id} se mapea al método showProduct de la clase TestController
// Con el fin de probar el router manual
$router->get('/producto/{id}', 'TestController@showProduct');

// Se obtiene el método HTTP y la URI de la petición
// URI = URL sin el protocolo y el host
$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Se muestra un mensaje de bienvenida y el estado de la base de datos
echo "<h1>CartMonitorCart Engine Online</h1>";
echo "<p>Running Front Controller</p>";

try {
    $db = Database::getInstance();
    echo "<p style='color:green;'>Database Connected</p>";
}
catch (PDOException $e) {
    echo "<p style='color:red;'>Database Connection Error: " . $e->getMessage() . "</p>";
}

// Nos aseguramos que la clase TestController exista para que el enrutamiento no genere un error 404
if (!class_exists('App\Controllers\TestController')) {
    echo "<pre>El siguiente paso es crear app\Controllers\TestController para probar el enrutamiento.</pre>";
}
else {
    $router->dispatch($requestMethod, $requestUri);
}