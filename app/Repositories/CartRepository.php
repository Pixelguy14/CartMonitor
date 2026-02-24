<?php

namespace App\Repositories;

use PDO;

/**
 * CartRepository maneja los ítems del carrito en la base de datos
 */
class CartRepository extends BaseRepository
{

    /**
     * Obtiene los ítems del carrito para un usuario específico con detalles de producto
     */
    public function getItemsByUser(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT c.*, p.name, p.price, p.image_url, p.stock_quantity 
            FROM cart_items c
            JOIN products p ON c.product_id = p.id
            WHERE c.user_id = :user_id
        ");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }

    /**
     * Agrega un ítem al carrito o actualiza la cantidad si ya existe
     */
    public function addItem(int $userId, int $productId, int $quantity): bool
    {
        // Primero verificamos si ya existe el producto en el carrito del usuario
        $stmt = $this->db->prepare("SELECT id, quantity FROM cart_items WHERE user_id = :u AND product_id = :p");
        $stmt->execute(['u' => $userId, 'p' => $productId]);
        $existing = $stmt->fetch();

        if ($existing) {
            $newQty = $existing['quantity'] + $quantity;
            $updateStmt = $this->db->prepare("UPDATE cart_items SET quantity = :q WHERE id = :id");
            return $updateStmt->execute(['q' => $newQty, 'id' => $existing['id']]);
        }

        $insertStmt = $this->db->prepare("INSERT INTO cart_items (user_id, product_id, quantity) VALUES (:u, :p, :q)");
        return $insertStmt->execute(['u' => $userId, 'p' => $productId, 'q' => $quantity]);
    }

    /**
     * Elimina un ítem del carrito
     */
    public function removeItem(int $userId, int $productId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM cart_items WHERE user_id = :u AND product_id = :p");
        return $stmt->execute(['u' => $userId, 'p' => $productId]);
    }

    /**
     * Limpia el carrito de un usuario (Después del checkout)
     */
    public function clearCart(int $userId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM cart_items WHERE user_id = :u");
        return $stmt->execute(['u' => $userId]);
    }
}