<?php

namespace App\Controllers;

use App\Core\SessionManager;
use App\Core\Database;

/**
 * ProfileController maneja la cuenta de un usuario normal (editar/borrar su propia cuenta)
 */
class ProfileController extends BaseController
{
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

        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT id, username, email, phone FROM users WHERE id = :id");
        $stmt->execute(['id' => $userId]);
        $userRaw = $stmt->fetch();

        if (!$userRaw) {
            SessionManager::setFlash('error', 'Usuario no encontrado');
            header('Location: /');
            exit;
        }

        $user = [];
        foreach ($userRaw as $key => $value) {
            $user[$key] = $this->escape($value ?? '');
        }

        $csrf_token = SessionManager::generateCsrfToken();
        $success = SessionManager::getFlash('success');
        $error = SessionManager::getFlash('error');

        require_once __DIR__ . '/../../resources/views/profile/index.php';
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
            $params = ['username' => $username, 'email' => $email, 'phone' => $phone, 'id' => $userId];
        }
        else {
            // Actualizar incluyendo nueva contraseña hasheada
            $stmt = $db->prepare("UPDATE users SET username = :username, email = :email, phone = :phone, password_hash = :password_hash WHERE id = :id");
            $params = [
                'username' => $username,
                'email' => $email,
                'phone' => $phone,
                'password_hash' => password_hash($password, PASSWORD_BCRYPT),
                'id' => $userId
            ];
        }

        try {
            $stmt->execute($params);

            // Actualizar el username en la sesión
            $_SESSION['username'] = $username;

            SessionManager::setFlash('success', 'Perfil actualizado correctamente.');
        }
        catch (\PDOException $e) {
            SessionManager::setFlash('error', 'Error al actualizar: El correo o username ya están en uso.');
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

        if (!SessionManager::validateCsrfToken($_POST['csrf_token'] ?? null)) {
            die("Error de seguridad");
        }

        $db = Database::getInstance();

        try {
            $stmt = $db->prepare("DELETE FROM users WHERE id = :id");
            $stmt->execute(['id' => $userId]);

            // Al eliminar, destruir la sesion
            SessionManager::destroy();
            SessionManager::start(); // Start new session to flash message
            SessionManager::setFlash('success', 'Tu cuenta ha sido eliminada permanentemente.');
            header('Location: /');
            exit;
        }
        catch (\PDOException $e) {
            // Manejar error de llave foránea (1451) si tiene productos en order_items o si es un proveedor que ha vendido logic deletes
            if ($e->getCode() == 23000) {
                // Borrado Lógico: Reemplazar datos para mantener el historial intacto
                $stmt = $db->prepare("UPDATE users SET username = :anon, email = :anon_email, password_hash = 'deleted', type = 'usuario' WHERE id = :id");
                $stmt->execute([
                    'anon' => 'Cuenta Eliminada (' . $userId . ')',
                    'anon_email' => 'deleted_' . $userId . '@system.local',
                    'id' => $userId
                ]);

                // Destroy session since they deleted their account
                SessionManager::destroy();
                SessionManager::start();
                SessionManager::setFlash('success', 'Tu cuenta ha sido eliminada y tus datos anonimizados para preservar el historial de órdenes.');
                header('Location: /');
                exit;
            }
            else {
                SessionManager::setFlash('error', 'Error inesperado al eliminar la cuenta.');
                header('Location: /perfil');
                exit;
            }
        }
    }
}