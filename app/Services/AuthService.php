<?php
// app/Services/AuthService.php
namespace App\Services;

use App\Repositories\UserRepository;
use Exception;

class AuthService
{
    private UserRepository $repo;
    public function __construct(UserRepository $repo)
    {
        $this->repo = $repo;
    }

    public function attemptLogin(string $email, string $password, string $userAgent, string $ipAddress): array
    {
        $user = $this->repo->findByEmail($email);
        if (!$user || !password_verify($password, $user['password_hash'])) {
            throw new Exception('Credenciales incorrectas');
        }
        // Creamos token robusto
        $token_hash = hash('sha256', random_bytes(32) . time());

        // Guardamos en la base de datos
        $this->repo->createSession(
            $user['id'],
            $token_hash,
            $userAgent,
            $ipAddress,
            date('Y-m-d H:i:s', strtotime('+30 days'))
        );
        // Devolvemos la info que el controlador necesita para la variable $_SESSION
        return [
            'user' => $user,
            'token_hash' => $token_hash
        ];
    }

    public function registerUser(string $username, string $email, string $password, string $confirmPassword): void
    {
        // Validar contraseñas
        if ($password !== $confirmPassword) {
            throw new Exception('Las contraseñas no coinciden.');
        }
        // Revisar si existe
        if ($this->repo->findByEmail($email)) {
            throw new Exception('El correo ya está registrado.');
        }
        // Crear usuario
        $userId = $this->repo->create([
            'username' => $username,
            'email' => $email,
            'password_hash' => password_hash($password, PASSWORD_BCRYPT),
            'type' => 'usuario'
        ]);
        if (!$userId) {
            throw new Exception('Error al crear la cuenta en la base de datos.');
        }
    }
}