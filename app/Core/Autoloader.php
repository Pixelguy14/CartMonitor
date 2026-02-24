<?php

namespace App\Core;

// Autoloader es parte del patrón de diseño Singleton y consiste en cargar las clases de forma automática
// con base al namespace y el directorio raiz del proyecto
class Autoloader
{
    // spl_autoload_register es una funcion de php que permite registrar una funcion de autoloader
    // que se ejecutara cada vez que se intente instanciar una clase que no ha sido cargada
    public static function register()
    {
        spl_autoload_register(function ($class) {
            // prefixo del namespace App
            // Ejemplo: App\Controllers\TestController
            $prefix = 'App\\';

            // Directorio raiz del proyecto
            $base_dir = __DIR__ . '/../';

            // Nos aseguramos de que la clase pertenece al namespace App
            $len = strlen($prefix);
            if (strncmp($prefix, $class, $len) !== 0) {
                // Si no pertenece al namespace App, se salta el autoloader
                return;
            }

            // Obtenemos el nombre de la clase relativa al namespace
            $relative_class = substr($class, $len);

            // Reemplazamos el prefijo del directorio App con el nombre del directorio con .php
            // Esto es para que el autoloader pueda encontrar el archivo de la clase
            // Ejemplo: App\Controllers\TestController
            // $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
            $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

            // Si el archivo existe, lo requerimos
            if (file_exists($file)) {
                require $file;
            }
        });
    }
}