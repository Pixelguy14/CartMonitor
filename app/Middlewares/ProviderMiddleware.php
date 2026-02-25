<?php

namespace App\Middlewares;

use App\Core\SessionManager;

/**
 * ProviderMiddleware restringe el acceso a proveedores (y administradores)
 */
class ProviderMiddleware
{
    public function handle(): bool
    {
        SessionManager::start();

        if (!isset($_SESSION['user_type']) || !in_array($_SESSION['user_type'], ['proveedor', 'admin'])) {
            SessionManager::setFlash('error', 'Acceso denegado: Se requieren permisos de Proveedor.');
            header('Location: /');
            return false;
        }

        return true;
    }
}