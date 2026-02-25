<?php

namespace App\Controllers;

use App\Repositories\UserRepository;
use App\Core\SessionManager;
use App\Core\Database;

/**
 * AdminController maneja la gestión de usuarios por parte de administradores
 */
class AdminController extends BaseController
{
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    /**
     * Lista todos los usuarios (Vista de Admin)
     */
    public function index()
    {
        $db = Database::getInstance();
        $stmt = $db->query("SELECT id, username, email, type, created_at FROM users ORDER BY created_at DESC");
        $usersRaw = $stmt->fetchAll();

        // Escapar datos para Zero Raw Data
        $users = [];
        foreach ($usersRaw as $user) {
            $u = [];
            foreach ($user as $key => $value) {
                $u[$key] = $this->escape($value);
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
        if (!SessionManager::validateCsrfToken($_POST['csrf_token'] ?? null)) {
            die("Error de seguridad");
        }

        $userId = (int)($_POST['user_id'] ?? 0);
        $newType = $_POST['type'] ?? 'usuario';

        if (!in_array($newType, ['usuario', 'proveedor', 'admin'])) {
            SessionManager::setFlash('error', 'Rol inválido');
            header('Location: /admin/usuarios');
            exit;
        }

        // Evitar que un admin se quite el permiso a sí mismo (opcional pero seguro)
        if ($userId === (int)$_SESSION['user_id'] && $newType !== 'admin') {
            SessionManager::setFlash('error', 'No puedes quitarte el rol de admin a ti mismo.');
            header('Location: /admin/usuarios');
            exit;
        }

        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE users SET type = :type WHERE id = :id");
        $stmt->execute(['type' => $newType, 'id' => $userId]);

        SessionManager::setFlash('success', 'Rol de usuario actualizado.');
        header('Location: /admin/usuarios');
        exit;
    }

    /**
     * Muestra el formulario para editar un usuario
     */
    public function edit(string $id)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT id, username, email, type, phone FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $userRaw = $stmt->fetch();

        if (!$userRaw) {
            SessionManager::setFlash('error', 'Usuario no encontrado');
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
        if (!SessionManager::validateCsrfToken($_POST['csrf_token'] ?? null)) {
            die("Error de seguridad");
        }

        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';

        $db = Database::getInstance();

        if (empty($password)) {
            // Actualizar sin tocar la contraseña
            $stmt = $db->prepare("UPDATE users SET username = :username, email = :email, phone = :phone WHERE id = :id");
            $params = ['username' => $username, 'email' => $email, 'phone' => $phone, 'id' => $id];
        }
        else {
            // Actualizar incluyendo nueva contraseña hasheada
            $stmt = $db->prepare("UPDATE users SET username = :username, email = :email, phone = :phone, password_hash = :password_hash WHERE id = :id");
            $params = [
                'username' => $username,
                'email' => $email,
                'phone' => $phone,
                'password_hash' => password_hash($password, PASSWORD_BCRYPT),
                'id' => $id
            ];
        }

        try {
            $stmt->execute($params);
            SessionManager::setFlash('success', 'Usuario actualizado correctamente.');
            header('Location: /admin/usuarios');
        }
        catch (\PDOException $e) {
            SessionManager::setFlash('error', 'Error al actualizar: ' . $e->getMessage());
            header('Location: /admin/usuarios/' . $id . '/editar');
        }
        exit;
    }

    /**
     * Elimina un usuario
     */
    public function delete()
    {
        if (!SessionManager::validateCsrfToken($_POST['csrf_token'] ?? null)) {
            die("Error de seguridad");
        }

        $userId = (int)($_POST['user_id'] ?? 0);

        if ($userId === (int)$_SESSION['user_id']) {
            SessionManager::setFlash('error', 'No puedes eliminarte a ti mismo.');
            header('Location: /admin/usuarios');
            exit;
        }

        $db = Database::getInstance();

        try {
            $stmt = $db->prepare("DELETE FROM users WHERE id = :id");
            $stmt->execute(['id' => $userId]);
            SessionManager::setFlash('success', 'Usuario eliminado permanentemente.');
        }
        catch (\PDOException $e) {
            // Manejar error de llave foránea (1451) si tiene productos en order_items
            if ($e->getCode() == 23000) {

                // Borrado Lógico: Reemplazar datos del proveedor para mantener el historial intacto
                $stmt = $db->prepare("UPDATE users SET username = :anon, email = :anon_email, password_hash = 'deleted', type = 'usuario' WHERE id = :id");
                $stmt->execute([
                    'anon' => 'Usuario Eliminado (' . $userId . ')',
                    'anon_email' => 'deleted_' . $userId . '@system.local',
                    'id' => $userId
                ]);

                SessionManager::setFlash('success', 'El usuario tenía ventas asociadas. Su cuenta ha sido ofuscada y desactivada (Borrado Lógico) para preservar el historial de órdenes.');
            }
            else {
                SessionManager::setFlash('error', 'Error inesperado al eliminar: ' . $e->getMessage());
            }
        }

        header('Location: /admin/usuarios');
        exit;
    }
}