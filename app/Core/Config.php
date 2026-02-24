<?php

namespace App\Core;

// Config es parte del patrón de diseño Singleton y consiste en cargar las variables de entorno
// desde el archivo .env
class Config
{
    private static array $settings = [];

    public static function load(string $envPath)
    {
        // Si el archivo .env no existe, se lanza una excepción
        if (!file_exists($envPath)) {
            throw new \Exception(".env file not found.");
        }
        // Cargamos el archivo .env y lo parseamos para obtener las variables de entorno
        // que se almacenan en el array $settings
        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0)
                continue;
            list($name, $value) = explode('=', $line, 2); // Se separa la linea en dos partes, la clave y el valor
            self::$settings[trim($name)] = trim($value); // Se almacena la clave y el valor en el array $settings
        }
    }

    public static function get(string $key, $default = null)
    {
        // Obtenemos el valor de la variable de entorno
        // Si no existe, se devuelve el valor por defecto
        return self::$settings[$key] ?? $default;
    }
}