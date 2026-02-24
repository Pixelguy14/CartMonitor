<?php

namespace App\Core;

use PDO;
use PDOException;

// Database es parte del patrón de diseño Singleton y consiste en cargar la base de datos
// desde el archivo .env
class Database
{
    private static ?PDO $instance = null; // Instancia de la base de datos

    // la metodologia singleton consiste en que solo exista una instancia de la clase
    // y que esta no sea recuperable desde strings
    private function __construct()
    {
    }

    // Este es el metodo estático que controla el acceso a la instancia singleton, al ejecutarse,
    // crea los objetos, y en caso de que ya exista, devuelve la instancia existente
    // ya almacenada. Esta implementacion en particular utiliza la clase Config para obtener
    // los datos de la base de datos desde el archivo .env e instanciar la base de datos
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $host = Config::get('DB_HOST', 'localhost'); // Host de la base de datos
            $db = Config::get('DB_NAME', 'CartMonitor'); // Nombre de la base de datos
            $user = Config::get('DB_USER', 'root'); // Usuario de la base de datos
            $pass = Config::get('DB_PASS', ''); // Contraseña de la base de datos
            $port = Config::get('DB_PORT', '3306'); // Puerto de la base de datos
            $charset = 'utf8mb4'; // Caracteres de la base de datos

            $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
            // pdo son las siglas de PHP Data Objects, es una extension de php que permite
            // la interaccion con bases de datos, y es nativo de php
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Exepciones basicas para manejar errores
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Devuelve arreglos asociativos
                PDO::ATTR_EMULATE_PREPARES => false, // Mejor seguridad para Prepared Statements
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4" // Asegura la codificación en la conexión
            ];

            try {
                self::$instance = new PDO($dsn, $user, $pass, $options); // Creamos la instancia de la base de datos
            }
            catch (PDOException $e) {
                // En un entorno real, lo ideal es registrar el error en un archivo de log
                // para no exponer informacion sensible
                throw new PDOException($e->getMessage(), (int)$e->getCode());
            }
        }

        return self::$instance;
    }
}