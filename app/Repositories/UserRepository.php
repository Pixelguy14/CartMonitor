<?php

namespace App\Repositories;

use PDO;

/**
 * UserRepository maneja la persistencia de usuarios y sesiones
 */
class UserRepository extends BaseRepository
{

    /**
     * Busca un usuario por email (Para login)
     */
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    /**
     * Crea un nuevo usuario
     */
    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO users (username, email, password_hash, type) 
            VALUES (:username, :email, :password_hash, :type)
        ");
        $stmt->execute([
            'username' => $data['username'],
            'email' => $data['email'],
            'password_hash' => $data['password_hash'],
            'type' => $data['type'] ?? 'usuario'
        ]);
        return (int)$this->db->lastInsertId();
    }

    /**
     * Almacena una nueva sesión en la base de datos
     */
    public function createSession(int $userId, string $tokenHash, string $deviceInfo, string $ipAddress, string $expiresAt): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO user_sessions (user_id, token_hash, device_info, ip_address, expires_at)
            VALUES (:user_id, :token_hash, :device_info, :ip_address, :expires_at)
        ");
        return $stmt->execute([
            'user_id' => $userId,
            'token_hash' => $tokenHash,
            'device_info' => $deviceInfo,
            'ip_address' => $ipAddress,
            'expires_at' => $expiresAt
        ]);
    }
}