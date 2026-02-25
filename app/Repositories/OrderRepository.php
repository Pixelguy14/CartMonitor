<?php

namespace App\Repositories;

use PDO;

/**
 * OrderRepository maneja la persistencia de las órdenes (compras)
 */
class OrderRepository extends BaseRepository
{
    /**
     * Crea un registro maestro de orden y devuelve su ID
     */
    public function createOrder(int $userId, float $totalAmount): int
    {
        $stmt = $this->db->prepare("INSERT INTO orders (user_id, total_amount, status) VALUES (:u, :t, 'completada')");
        $stmt->execute(['u' => $userId, 't' => $totalAmount]);
        return (int)$this->db->lastInsertId();
    }

    /**
     * Agrega el detalle de la orden con el precio congelado
     */
    public function addOrderItem(int $orderId, int $productId, int $quantity, float $priceAtPurchase): bool
    {
        $stmt = $this->db->prepare("INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase) VALUES (:o, :p, :q, :price)");
        return $stmt->execute([
            'o' => $orderId,
            'p' => $productId,
            'q' => $quantity,
            'price' => $priceAtPurchase
        ]);
    }

    /**
     * Ontiene todas las ordenes de un usuario
     */
    public function getOrdersByUser(int $userId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM orders WHERE user_id = :u ORDER BY created_at DESC");
        $stmt->execute(['u' => $userId]);
        return $stmt->fetchAll();
    }

    /**
     * Obtiene los productos de una orden específica
     */
    public function getOrderItems(int $orderId, int $userId): array
    {
        // Validamos que la orden pertenezca al usuario por seguridad
        $stmt = $this->db->prepare("
            SELECT oi.*, p.name 
            FROM order_items oi 
            JOIN orders o ON oi.order_id = o.id
            JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = :oid AND o.user_id = :uid
        ");
        $stmt->execute(['oid' => $orderId, 'uid' => $userId]);
        return $stmt->fetchAll();
    }
}