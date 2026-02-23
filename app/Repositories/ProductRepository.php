<?php

namespace App\Repositories;

use PDO;

/**
 * ProductRepository maneja todas las consultas SQL relacionadas con productos
 * Implementa Prepared Statements obligatorios para seguridad
 */
class ProductRepository extends BaseRepository
{

    /**
     * Obtiene todos los productos disponibles
     */
    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM products ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }

    /**
     * Busca un producto por su ID
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $product = $stmt->fetch();
        return $product ?: null;
    }

    /**
     * Busca productos por nombre (Búsqueda dinámica)
     */
    public function findByName(string $name): array
    {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE name LIKE :name");
        $stmt->execute(['name' => "%$name%"]);
        return $stmt->fetchAll();
    }

    /**
     * Actualiza el stock de un producto (Usado en el checkout)
     * Utiliza SELECT FOR UPDATE implícitamente si se llama dentro de una transacción
     */
    public function updateStock(int $productId, int $newQuantity): bool
    {
        $stmt = $this->db->prepare("UPDATE products SET stock_quantity = :qty WHERE id = :id");
        return $stmt->execute([
            'qty' => $newQuantity,
            'id' => $productId
        ]);
    }
}