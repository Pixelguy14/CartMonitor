<?php

namespace App\Services;

use App\Repositories\AdminRepository;
use Exception;

/**
 * ProfileService maneja la lógica de negocio del perfil de usuario
 */
class ProfileService
{
    private AdminRepository $repo;

    public function __construct(AdminRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Obtiene los datos del perfil
     */
    public function getProfileData(int $userId): array
    {
        $user = $this->repo->findUserById($userId);
        if (!$user) {
            throw new Exception('Usuario no encontrado');
        }
        return $user;
    }

    /**
     * Actualiza el perfil del usuario
     */
    public function updateProfile(int $userId, array $data): bool
    {
        if (empty($data['username']) || empty($data['email'])) {
            throw new Exception('El nombre de usuario y el correo son obligatorios.');
        }

        if (!empty($data['password'])) {
            $data['password_hash'] = password_hash($data['password'], PASSWORD_BCRYPT);
            unset($data['password']);
        }

        try {
            return $this->repo->updateUser($userId, $data);
        }
        catch (Exception $e) {
            throw new Exception('Error al actualizar: El correo o username ya están en uso.');
        }
    }

    /**
     * Elimina la cuenta del usuario
     */
    public function deleteAccount(int $userId): string
    {
        try {
            $this->repo->hardDelete($userId);
            return 'success_deleted';
        }
        catch (Exception $e) {
            if ($e->getCode() == 23000) {
                $this->repo->logicalDelete($userId);
                return 'success_logical';
            }
            throw new Exception('Error inesperado al eliminar la cuenta.');
        }
    }
}