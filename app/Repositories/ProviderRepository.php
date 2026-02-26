<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

/**
 * ProviderRepository maneja el CRUD de inventario exclusivo para cada Proveedor
 */
class ProviderRepository
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Obtiene todos los productos de un proveedor
     */
    public function getAllByProvider(int $providerId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE provider_id = :pid ORDER BY id DESC");
        $stmt->execute(['pid' => $providerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    /**
     * Obtiene un producto por ID y proveedor
     */
    public function findByIdAndProvider(int $id, int $providerId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE id = :id AND provider_id = :pid");
        $stmt->execute(['id' => $id, 'pid' => $providerId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Actualiza un producto
     */
    public function update(int $id, int $providerId, array $data): bool
    {
        $fields = "";
        foreach ($data as $key => $value) {
            $fields .= "$key = :$key, "; // Construimos la query dinámicamente
        }
        $fields = rtrim($fields, ', '); // Eliminamos la última coma y espacio

        $sql = "UPDATE products SET $fields WHERE id = :id AND provider_id = :pid";
        $data['id'] = $id;
        $data['pid'] = $providerId;

        return $this->db->prepare($sql)->execute($data);
    }

    /**
     * Crea un nuevo producto
     */
    public function create(array $data): bool
    {
        $sql = "INSERT INTO products (name, description, price, stock_quantity, provider_id, image_url) 
                VALUES (:name, :description, :price, :stock_quantity, :provider_id, :image_url)";
        return $this->db->prepare($sql)->execute($data);
    }

    /**
     * Elimina físicamente un producto
     * la diferencia entre hardDelete y softDelete es que hardDelete elimina el producto de la base de datos
     * y softDelete elimina el producto de la base de datos pero lo deja en la base de datos
     * con stock 0 y nombre concatenado con " (Descontinuado)"
     */
    public function hardDelete(int $id, int $providerId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM products WHERE id = :id AND provider_id = :pid");
        $stmt->execute(['id' => $id, 'pid' => $providerId]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Elimina lógicamente un producto
     */
    public function softDelete(int $id, int $providerId): bool
    {
        $stmt = $this->db->prepare("UPDATE products SET stock_quantity = 0, name = CONCAT(name, ' (Descontinuado)') WHERE id = :id AND provider_id = :pid");
        return $stmt->execute(['id' => $id, 'pid' => $providerId]);
    }
}