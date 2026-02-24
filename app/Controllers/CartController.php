<?php

namespace App\Controllers;

use App\Services\CartService;

/**
 * CartController maneja las operaciones del carrito
 */
class CartController extends BaseController
{
    private CartService $cartService;

    public function __construct()
    {
        $this->cartService = new CartService();
    }

    /**
     * Muestra los artículos en el carrito
     */
    public function showCart()
    {
        // En el Sprint 2 usamos un ID de usuario mock (Ej: 1)
        // Ya que el sistema de login es del Sprint 3
        $userId = 1;

        $cartData = $this->cartService->getCartTotals($userId);

        echo "<h1>Tu Carrito de Compras</h1>";
        if (empty($cartData['items'])) {
            echo "<p>El carrito está vacío.</p>";
        }
        else {
            foreach ($cartData['items'] as $item) {
                $name = $this->escape($item['name']);
                echo "<div>{$name} x {$item['quantity']} - \${$item['item_total']}</div>";
            }
            echo "<h3>Total: \${$cartData['total']}</h3>";
        }
        echo "<a href='/'>Continuar comprando</a>";
    }

    /**
     * Procesa la adición al carrito
     */
    public function add()
    {
        // Mock de usuario
        $userId = 1;

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
        }

        // Validación de datos crudos
        $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
        $qty = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;

        $result = $this->cartService->addToCart($userId, $productId, $qty);

        if ($result['success']) {
            $this->jsonResponse($result);
        }
        else {
            $this->jsonResponse($result, 400);
        }
    }
}