<?php

namespace App\Middlewares;

use App\Core\SessionManager;

/**
 * AdminMiddleware restringe el acceso a solo administradores
 */
class AdminMiddleware
{
    public function handle(): bool
    {
        SessionManager::start();

        // Primero verificamos si está logueado (AuthMiddleware debería correr antes, pero por seguridad doble check)
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
            SessionManager::setFlash('error', 'Acceso denegado: Se requieren permisos de administrador.');
            header('Location: /');
            return false;
        }

        return true;
    }
}