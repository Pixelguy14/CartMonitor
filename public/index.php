<?php
// public/index.php
// Este archivo es el punto de entrada de la aplicación
// Aquí se recibirá la URI y se despachará al Controller correspondiente
// Ningún otro archivo PHP debe poder ser invocado por otro medio

require_once __DIR__ . '/../app/Core/Config.php';

// Implementación de Seguridad Obligatoria: Manejo de sesiones seguro
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
session_start();

// Carga del ruteador manual
// Aquí se recibirá la URI y se despachará al Controller correspondiente
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

echo "<h1>NexusCart Engine Online</h1>";
echo "URI actual: " . $uri;