<?php

namespace App\Middlewares;

use App\Repositories\UserRepository;

/**
 * AuthMiddleware intercepta peticiones para validar la sesión del usuario
 * Según el diagrama de secuencia Validacion_sesion.png
 */
class AuthMiddleware
{
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    /**
     * Verifica si hay una sesión activa válida
     */
    public function handle(): ?array
    {
        if (!isset($_SESSION['user_token'])) {
            return null;
        }

        $tokenHash = $_SESSION['user_token'];

        // En una implementación real buscaríamos en la tabla user_sessions
        // Por ahora retornamos un mock si existe el token en la sesión
        // El Sprint 3 completará la lógica de login real
        return [
            'id' => 1,
            'username' => 'usuario_prueba',
            'type' => 'usuario'
        ];
    }
}