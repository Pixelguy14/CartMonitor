<?php
namespace App\Services;

use App\Repositories\AdminRepository;
use Exception;

/**
 * AdminService maneja la lógica de negocio para la gestión de usuarios desde la vista de admin
 */
class AdminService
{
    private AdminRepository $repo;

    public function __construct(AdminRepository $repo)
    {
        // Instanciamos el repositorio
        $this->repo = $repo;
    }

    /**
     * Lista todos los usuarios
     */
    public function listUsers(): array
    {
        return $this->repo->getAllUsers();
    }

    /*
     * Correccion rápida del rol de un usuario 
     */
    public function changeRole(int $userId, string $newType, int $currentAdminId): void
    {
        $allowed = ['usuario', 'proveedor', 'admin'];
        if (!in_array($newType, $allowed, true)) {
            throw new Exception('Rol inválido');
        }

        if ($userId === $currentAdminId && $newType !== 'admin') {
            throw new Exception('No puedes quitarte el rol de admin a ti mismo.');
        }
        $this->repo->updateRole($userId, $newType);
    }

    /**
     * Obtiene los datos de un usuario para editar
     */
    public function getUserForEdit(int $id): array
    {
        $user = $this->repo->findUserById($id);
        if (!$user) {
            throw new Exception('Usuario no encontrado');
        }
        return $user;
    }

    /**
     * Actualiza los datos del usuario seleccionado
     */
    public function updateUser(int $id, array $data): bool
    {
        // Reglas de negocio: validación básica
        if (empty($data['username']) || empty($data['email'])) {
            throw new Exception('El nombre de usuario y el correo son obligatorios.');
        }
        // Si hay una contraseña, el servicio decide cómo procesarla (hashing)
        if (!empty($data['password'])) {
            $data['password_hash'] = password_hash($data['password'], PASSWORD_BCRYPT);
            unset($data['password']); // No enviamos la pass plana al repositorio
        }
        return $this->repo->updateUser($id, $data);
    }

    /**
     * Elimina al usuario seleccionado
     */
    public function deleteUser(int $id, int $currentAdminId): string
    {
        if ($id === $currentAdminId) {
            throw new Exception('No puedes eliminarte a ti mismo.');
        }

        try {
            $this->repo->hardDelete($id);
            return 'success_deleted';
        }
        catch (Exception $e) {
            if ($e->getCode() == 23000) {
                $this->repo->logicalDelete($id);
                return 'success_logical';
            }
            throw $e;
        }
    }
}