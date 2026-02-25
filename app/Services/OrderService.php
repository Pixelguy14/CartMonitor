<?php

namespace App\Services;

use App\Repositories\OrderRepository;
use App\Repositories\CartRepository;
use App\Core\Database;
use Exception;

/**
 * OrderService provee lógica transaccional para la creación y gestión de Órdenes
 */
class OrderService extends BaseService
{
    private OrderRepository $orderRepository;
    private CartRepository $cartRepository;

    public function __construct()
    {
        $this->orderRepository = new OrderRepository();
        $this->cartRepository = new CartRepository();
    }

    /**
     * Procesa el Checkout Transaccional (Bloqueo conservador de stock FOR UPDATE)
     */
    public function checkout(int $userId): array
    {
        $db = Database::getInstance();

        try {
            $db->beginTransaction();

            // 1. Obtener items del carrito y BLOQUEAR las filas de producto correspondientes.
            // Esto evita que otro usuario compre concurrentemente el último artículo.
            $stmt = $db->prepare("
                SELECT c.product_id, c.quantity, p.price, p.stock_quantity, p.name 
                FROM cart_items c 
                JOIN products p ON c.product_id = p.id 
                WHERE c.user_id = :u 
                FOR UPDATE
            ");
            $stmt->execute(['u' => $userId]);
            $items = $stmt->fetchAll();

            if (empty($items)) {
                $db->rollBack();
                return ['success' => false, 'message' => 'Tu carrito está vacío.'];
            }

            $totalAmount = 0.0;

            // 2. Validar Stock y Recalcular (Zero Trust del cliente)
            foreach ($items as $item) {
                if ($item['stock_quantity'] < $item['quantity']) {
                    $db->rollBack();
                    return ['success' => false, 'message' => 'Stock insuficiente para el producto: ' . $item['name']];
                }
                $totalAmount += $item['price'] * $item['quantity'];
            }

            // 3. Crear Orden Maestra
            $orderId = $this->orderRepository->createOrder($userId, $totalAmount);

            // 4. Crear Detalle (Order items) e impactar Inventario
            $updateStockStmt = $db->prepare("UPDATE products SET stock_quantity = stock_quantity - :q WHERE id = :id");

            foreach ($items as $item) {
                $this->orderRepository->addOrderItem($orderId, $item['product_id'], $item['quantity'], $item['price']);
                $updateStockStmt->execute(['q' => $item['quantity'], 'id' => $item['product_id']]);
            }

            // 5. Vaciar el carrito
            $this->cartRepository->clearCart($userId);

            // 6. Confirmar Transacción
            $db->commit();
            return ['success' => true, 'message' => '¡Compra realizada con éxito!'];

        }
        catch (Exception $e) {
            $db->rollBack();
            return ['success' => false, 'message' => 'Error al procesar la compra: Intenta de nuevo.'];
        }
    }

    public function getUserOrders(int $userId): array
    {
        return $this->orderRepository->getOrdersByUser($userId);
    }

    public function getOrderItems(int $orderId, int $userId): array
    {
        return $this->orderRepository->getOrderItems($orderId, $userId);
    }
}