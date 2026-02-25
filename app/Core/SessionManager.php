<?php

namespace App\Core;

/**
 * SessionManager maneja la inicialización de sesiones seguras
 * y la generación/validación de Tokens CSRF
 */
class SessionManager
{

    /**
     * Inicia una sesión segura si no existe
     */
    public static function start()
    {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.cookie_httponly', 1);
            ini_set('session.use_only_cookies', 1);
            session_start();
        }
    }

    /**
     * Genera un token CSRF y lo guarda en la sesión
     */
    public static function generateCsrfToken(): string
    {
        self::start();
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Valida el token CSRF enviado en el POST
     */
    public static function validateCsrfToken(?string $token): bool
    {
        self::start();
        if (empty($_SESSION['csrf_token']) || $token === null) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Establece un mensaje Flash (Para notificaciones de éxito/error una sola vez)
     */
    public static function setFlash(string $key, string $message)
    {
        self::start();
        $_SESSION['flash'][$key] = $message;
    }

    /**
     * Obtiene y elimina un mensaje Flash
     */
    public static function getFlash(string $key): ?string
    {
        self::start();
        if (isset($_SESSION['flash'][$key])) {
            $message = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]);
            return $message;
        }
        return null;
    }

    /**
     * Destruye la sesión completa (Logout)
     */
    public static function destroy()
    {
        self::start();
        $_SESSION = array();

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        session_destroy();
    }
}