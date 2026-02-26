<?php
namespace App\Repositories;

use App\Core\Database;
use PDO;

/**
 * AdminRepository maneja las consultas a la base de datos para la gestión de usuarios
 */
class AdminRepository
{
    protected PDO $db;
    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Obtiene todos los usuarios
     */
    public function getAllUsers(): array
    {
        $stmt = $this->db->query(
            "SELECT id, username, email, phone, type, created_at FROM users ORDER BY created_at DESC"
        );
        return $stmt->fetchAll();
    }

    /**
     * Busca un usuario por su ID
     */
    public function findUserById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT id, username, email, type, phone FROM users WHERE id = :id"
        );
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    /**
     * Actualiza el rol de un usuario
     */
    public function updateRole(int $id, string $type): void
    {
        $stmt = $this->db->prepare(
            "UPDATE users SET type = :type WHERE id = :id"
        );
        $stmt->execute(['type' => $type, 'id' => $id]);
    }

    /**
     * Actualiza los datos de un usuario
     */
    public function updateUser(int $id, array $data): bool
    {
        $fields = [];
        $params = ['id' => $id];
        // Recorremos las columnas básicas para construir el SQL dinámicamente
        foreach (['username', 'email', 'phone'] as $col) {
            if (isset($data[$col])) {
                $fields[] = "$col = :$col";
                $params[$col] = $data[$col];
            }
        }
        // Si viene un hash de contraseña, lo incluimos
        if (!empty($data['password_hash'])) {
            $fields[] = "password_hash = :password_hash";
            $params['password_hash'] = $data['password_hash'];
        }
        if (empty($fields)) {
            return false;
        }
        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = :id";
        return $this->db->prepare($sql)->execute($params);
    }

    /**
     * Elimina fisicamente un usuario
     * De nuevo, la diferencia entre hardDelete y softDelete es que el primero elimina el usuario
     * de la base de datos y el segundo lo elimina dejando una muestra en la base de datos de que el 
     * usuario existio y fue eliminado.
     */
    public function hardDelete(int $id): void
    {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }

    /**
     * Elimina las credenciales del usuario y lo deja como anonimo
     * Se utiliza para eliminar usuarios que tienen datos enlazados a otras tablas
     * y no se deben eliminar.
     */
    public function logicalDelete(int $id): void
    {
        $stmt = $this->db->prepare(
            "UPDATE users SET username = :anon,
                             email = :anon_email,
                             password_hash = 'deleted',
                             type = 'usuario'
             WHERE id = :id"
        );
        $stmt->execute([
            'anon' => "Usuario Eliminado ($id)",
            'anon_email' => "deleted_$id@system.local",
            'id' => $id,
        ]);
    }
}