<?php

namespace App\Controllers;

use App\Services\AuthService;
use App\Repositories\UserRepository;
use App\Core\SessionManager;
use Exception;

/**
 * AuthController maneja el Login y Registro
 */
class AuthController extends BaseController
{
    private AuthService $service;


    public function __construct()
    {
        $this->service = new AuthService(new UserRepository());
    }

    /**
     * Muestra el formulario de Login
     */
    public function showLogin()
    {
        $csrf_token = SessionManager::generateCsrfToken();
        $error = SessionManager::getFlash('error');
        $success = SessionManager::getFlash('success');

        require_once __DIR__ . '/../../resources/views/auth/login.php';
    }

    /**
     * Muestra el formulario de registro
     */
    public function showRegister()
    {
        $csrf_token = SessionManager::generateCsrfToken();
        $error = SessionManager::getFlash('error');

        require_once __DIR__ . '/../../resources/views/auth/register.php';
    }

    /**
     * Procesa la solicitud POST de Login
     */
    public function login()
    {
        $this->validateCsrf();
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
        try {
            // Toda la lógica pesada aquí:
            $result = $this->service->attemptLogin($email, $password, $userAgent, $ipAddress);

            // Regeneramos ID de sesión para prevenir ataques de fijación
            session_regenerate_id(true);
            // Guardamos datos básicos en sesión (el controlador maneja el estado del request/sesion)
            $_SESSION['user_id'] = $result['user']['id'];
            $_SESSION['username'] = $result['user']['username'];
            $_SESSION['user_type'] = $result['user']['type'];
            $_SESSION['user_token'] = $result['token_hash'];
            header('Location: /');
        }
        catch (Exception $e) {
            SessionManager::setFlash('error', $e->getMessage());
            header('Location: /login');
        }
        exit;
    }

    /**
     * Procesa la solicitud POST de Registro
     */
    public function register()
    {
        $this->validateCsrf();
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        try {
            $this->service->registerUser($username, $email, $password, $confirm_password);

            SessionManager::setFlash('success', 'Cuenta creada con éxito. Ahora puedes iniciar sesión.');
            header('Location: /login');
        }
        catch (Exception $e) {
            SessionManager::setFlash('error', $e->getMessage());
            header('Location: /register');
        }
        exit;
    }

    /**
     * Procesa la solicitud POST de Logout
     */
    public function logout()
    {
        SessionManager::destroy();
        header('Location: /login');
        exit;
    }
    private function validateCsrf()
    {
        if (!SessionManager::validateCsrfToken($_POST['csrf_token'] ?? null)) {
            die("Error de seguridad: Token CSRF inválido.");
        }
    }
}