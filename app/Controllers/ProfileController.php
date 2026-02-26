<?php

namespace App\Controllers;

use App\Services\ProfileService;
use App\Repositories\AdminRepository;
use App\Core\SessionManager;
use Exception;

/**
 * ProfileController maneja la cuenta de un usuario normal (editar/borrar su propia cuenta)
 */
class ProfileController extends BaseController
{
    private ProfileService $service;

    public function __construct()
    {
        $this->service = new ProfileService(new AdminRepository());
    }

    /**
     * Muestra el formulario para editar el perfil del usuario activo
     */
    public function index()
    {
        SessionManager::start();
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            header('Location: /login');
            exit;
        }

        try {
            $userRaw = $this->service->getProfileData($userId);

            $user = [];
            foreach ($userRaw as $key => $value) {
                $user[$key] = $this->escape($value ?? '');
            }

            $csrf_token = SessionManager::generateCsrfToken();
            $success = SessionManager::getFlash('success');
            $error = SessionManager::getFlash('error');

            require_once __DIR__ . '/../../resources/views/profile/index.php';
        }
        catch (Exception $e) {
            SessionManager::setFlash('error', $e->getMessage());
            header('Location: /');
            exit;
        }
    }

    /**
     * Actualiza los datos del usuario activo
     */
    public function update()
    {
        SessionManager::start();
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            header('Location: /login');
            exit;
        }

        $this->validateCsrf();

        $data = [
            'username' => trim($_POST['username'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'password' => $_POST['password'] ?? ''
        ];

        try {
            $this->service->updateProfile($userId, $data);

            // Actualizar el username en la sesión
            $_SESSION['username'] = $data['username'];

            SessionManager::setFlash('success', 'Perfil actualizado correctamente.');
        }
        catch (Exception $e) {
            SessionManager::setFlash('error', $e->getMessage());
        }

        header('Location: /perfil');
        exit;
    }

    /**
     * Elimina el usuario activo
     */
    public function delete()
    {
        SessionManager::start();
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            header('Location: /login');
            exit;
        }

        $this->validateCsrf();

        try {
            $result = $this->service->deleteAccount($userId);

            // Al eliminar, destruir la sesion
            SessionManager::destroy();
            SessionManager::start(); // Start new session to flash message

            if ($result === 'success_deleted') {
                SessionManager::setFlash('success', 'Tu cuenta ha sido eliminada permanentemente.');
            }
            else {
                SessionManager::setFlash('success', 'Tu cuenta ha sido eliminada y tus datos anonimizados para preservar el historial de órdenes.');
            }

            header('Location: /');
            exit;
        }
        catch (Exception $e) {
            SessionManager::setFlash('error', $e->getMessage());
            header('Location: /perfil');
            exit;
        }
    }

    private function validateCsrf()
    {
        if (!SessionManager::validateCsrfToken($_POST['csrf_token'] ?? null)) {
            die("Error de seguridad");
        }
    }
}