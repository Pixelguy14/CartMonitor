<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

/**
 * BaseRepository proporciona la base para la abstracción de datos
 * Maneja la conexión PDO y provee métodos comunes
 */
abstract class BaseRepository
{
    protected PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }
}