<?php
namespace App\Controllers;

use App\Services\AdminService;
use App\Core\SessionManager;
use Exception;


/**
 * AdminController maneja la gestión de usuarios por parte de administradores
 */
class AdminController extends BaseController
{
    private AdminService $service;

    public function __construct()
    {
        // Inyectamos la dependencia del repositorio
        $this->service = new AdminService(new \App\Repositories\AdminRepository());
    }

    /**
     * Lista todos los usuarios (Vista de Admin)
     */
    public function index()
    {
        $rawUsers = $this->service->listUsers();

        $users = [];
        foreach ($rawUsers as $user) {
            $u = [];
            foreach ($user as $k => $v) {
                $u[$k] = $this->escape($v ?? '');
            }
            $users[] = $u;
        }

        $csrf_token = SessionManager::generateCsrfToken();
        $success = SessionManager::getFlash('success');
        $error = SessionManager::getFlash('error');

        require_once __DIR__ . '/../../resources/views/admin/users.php';
    }

    /**
     * Cambia el rol de un usuario
     */
    public function updateRole()
    {
        $this->validateCsrf();

        $userId = (int)($_POST['user_id'] ?? 0);
        $newType = $_POST['type'] ?? 'usuario';

        try {
            $this->service->changeRole($userId, $newType, (int)$_SESSION['user_id']);
            SessionManager::setFlash('success', 'Rol de usuario actualizado.');
        }
        catch (Exception $e) {
            SessionManager::setFlash('error', $e->getMessage());
        }

        header('Location: /admin/usuarios');
        exit;
    }

    /**
     * Muestra el formulario para editar un usuario
     */
    public function edit(string $id)
    {
        try {
            $userRaw = $this->service->getUserForEdit((int)$id);
        }
        catch (Exception $e) {
            SessionManager::setFlash('error', $e->getMessage());
            header('Location: /admin/usuarios');
            exit;
        }

        $user = [];
        foreach ($userRaw as $key => $value) {
            $user[$key] = $this->escape($value ?? '');
        }

        $csrf_token = SessionManager::generateCsrfToken();
        $error = SessionManager::getFlash('error');

        require_once __DIR__ . '/../../resources/views/admin/edit_user.php';
    }

    /**
     * Actualiza los datos de un usuario
     */
    public function update(string $id)
    {
        $this->validateCsrf();
        // Limpieza selectiva de datos de entrada
        $input = [
            'username' => trim($_POST['username'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'password' => $_POST['password'] ?? ''
        ];
        try {
            $this->service->updateUser((int)$id, $input);
            SessionManager::setFlash('success', 'Usuario actualizado correctamente.');
            header('Location: /admin/usuarios');
        }
        catch (Exception $e) {
            // En caso de error, el controlador sabe que debe volver al formulario de edición
            SessionManager::setFlash('error', $e->getMessage());
            header('Location: /admin/usuarios/' . $id . '/editar');
        }
        exit;
    }

    /**
     * Elimina un usuario
     */
    public function delete()
    {
        $this->validateCsrf();

        $userId = (int)($_POST['user_id'] ?? 0);

        try {
            $result = $this->service->deleteUser($userId, (int)$_SESSION['user_id']);
            if ($result === 'success_deleted') {
                SessionManager::setFlash('success', 'Usuario eliminado permanentemente.');
            }
            else {
                SessionManager::setFlash('success', 'Usuario tenía ventas; se ha desactivado lógicamente.');
            }
        }
        catch (Exception $e) {
            SessionManager::setFlash('error', $e->getMessage());
        }

        header('Location: /admin/usuarios');
        exit;
    }

    private function validateCsrf()
    {
        if (!SessionManager::validateCsrfToken($_POST['csrf_token'] ?? null)) {
            die('Error de seguridad');
        }
    }
}