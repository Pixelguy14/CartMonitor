<?php

namespace App\Services;

use App\Repositories\OrderRepository;
use App\Repositories\CartRepository;
use App\Repositories\ProductRepository;
use App\Core\Database;
use Exception;

/**
 * OrderService provee lógica transaccional para la creación y gestión de Órdenes
 */
class OrderService extends BaseService
{
    private OrderRepository $orderRepository;
    private CartRepository $cartRepository;
    private ProductRepository $productRepository;

    public function __construct(
        OrderRepository $orderRepository,
        CartRepository $cartRepository,
        ProductRepository $productRepository
        )
    {
        $this->orderRepository = $orderRepository;
        $this->cartRepository = $cartRepository;
        $this->productRepository = $productRepository;
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
            $items = $this->cartRepository->getItemsForUpdate($userId);

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
            foreach ($items as $item) {
                $this->orderRepository->addOrderItem($orderId, $item['product_id'], $item['quantity'], $item['price']);

                // Calculamos nueva cantidad
                $newStock = $item['stock_quantity'] - $item['quantity'];
                $this->productRepository->updateStock($item['product_id'], $newStock);
            }

            // 5. Vaciar el carrito
            $this->cartRepository->clearCart($userId);

            // 6. Confirmar Transacción
            $db->commit();
            return ['success' => true, 'message' => '¡Compra realizada con éxito!'];

        }
        catch (Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
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