<?php

namespace App\Middlewares;

use App\Core\Database;
use App\Core\SessionManager;

/**
 * AuthMiddleware valida que el usuario esté logueado
 */
class AuthMiddleware
{
    /**
     * Verifica si hay una sesión activa válida
     */
    public function handle(): bool
    {
        SessionManager::start();

        if (!isset($_SESSION['user_token']) || !isset($_SESSION['user_id'])) {
            SessionManager::setFlash('error', 'Debes iniciar sesión para acceder.');
            header('Location: /login');
            return false;
        }

        // Validación extra contra DB para mayor seguridad
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT id FROM user_sessions WHERE token_hash = :hash AND user_id = :uid AND expires_at > NOW()");
        $stmt->execute([
            'hash' => $_SESSION['user_token'],
            'uid' => $_SESSION['user_id']
        ]);

        if (!$stmt->fetch()) {
            SessionManager::destroy();
            header('Location: /login');
            return false;
        }

        return true;
    }
}