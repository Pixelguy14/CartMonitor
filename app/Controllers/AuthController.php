<?php

namespace App\Controllers;

use App\Repositories\UserRepository;
use App\Core\SessionManager;

/**
 * AuthController maneja el Login y Registro
 */
class AuthController extends BaseController
{
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
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
     * Procesa la solicitud POST de Login
     */
    public function login()
    {
        if (!SessionManager::validateCsrfToken($_POST['csrf_token'] ?? null)) {
            die("Error de seguridad: Token CSRF inválido.");
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $user = $this->userRepository->findByEmail($email);

        if ($user && password_verify($password, $user['password_hash'])) {
            // Regenerate ID on login to prevent session fixation
            session_regenerate_id(true);

            // Guardamos datos básicos en sesión
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_type'] = $user['type'];

            // Creamos token robusto en bd para Middlewares avanzados
            $token_hash = hash('sha256', random_bytes(32) . time());
            $_SESSION['user_token'] = $token_hash;

            $this->userRepository->createSession(
                $user['id'],
                $token_hash,
                $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
                $_SERVER['REMOTE_ADDR'] ?? 'Unknown',
                date('Y-m-d H:i:s', strtotime('+30 days'))
            );

            header('Location: /');
            exit;
        }
        else {
            SessionManager::setFlash('error', 'Credenciales incorrectas');
            header('Location: /login');
            exit;
        }
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
     * Procesa la solicitud POST de registro
     */
    public function register()
    {
        if (!SessionManager::validateCsrfToken($_POST['csrf_token'] ?? null)) {
            die("Error de seguridad: Token CSRF inválido.");
        }

        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        // Validaciones básicas
        if ($password !== $confirm_password) {
            SessionManager::setFlash('error', 'Las contraseñas no coinciden.');
            header('Location: /register');
            exit;
        }

        if ($this->userRepository->findByEmail($email)) {
            SessionManager::setFlash('error', 'El correo ya está registrado.');
            header('Location: /register');
            exit;
        }

        $userId = $this->userRepository->create([
            'username' => $username,
            'email' => $email,
            'password_hash' => password_hash($password, PASSWORD_BCRYPT),
            'type' => 'usuario' // Tipo por defecto
        ]);

        if ($userId) {
            SessionManager::setFlash('success', 'Cuenta creada con éxito. Ahora puedes iniciar sesión.');
            header('Location: /login');
            exit;
        }
        else {
            SessionManager::setFlash('error', 'Error al crear la cuenta.');
            header('Location: /register');
            exit;
        }
    }

    /**
     * Destruye la sesión de forma segura
     */
    public function logout()
    {
        SessionManager::destroy();
        header('Location: /login');
        exit;
    }
}