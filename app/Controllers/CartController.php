<?php

namespace App\Controllers;

use App\Services\CartService;
use App\Services\OrderService;

/**
 * CartController maneja las operaciones del carrito
 */
class CartController extends BaseController
{
    private CartService $service;
    private OrderService $orderService;

    public function __construct()
    {
        $cartRepo = new \App\Repositories\CartRepository();
        $productRepo = new \App\Repositories\ProductRepository();
        $orderRepo = new \App\Repositories\OrderRepository();

        $this->service = new CartService($cartRepo, $productRepo);
        $this->orderService = new OrderService($orderRepo, $cartRepo, $productRepo);
    }

    /**
     * Muestra los artículos en el carrito
     */
    public function showCart()
    {
        // En el Sprint 3 usamos el ID de sesión si existe, o pedimos login
        \App\Core\SessionManager::start();
        if (empty($_SESSION['user_id'])) {
            \App\Core\SessionManager::setFlash('error', 'Debes iniciar sesión para ver tu carrito.');
            header("Location: /login");
            exit;
        }

        $userId = $_SESSION['user_id'];

        $cartDataRaw = $this->service->getCartTotals($userId);

        // Zero Raw Data
        $cartData = ['items' => [], 'total' => $this->escape($cartDataRaw['total'])];
        foreach ($cartDataRaw['items'] as $item) {
            $i = [];
            $i['product_id'] = $item['product_id'];
            $i['stock_quantity'] = $item['stock_quantity'];
            $i['name'] = $this->escape($item['name']);
            $i['price'] = $this->escape($item['price']);
            $i['quantity'] = $this->escape($item['quantity']);
            $i['item_total'] = $this->escape($item['item_total']);
            $cartData['items'][] = $i;
        }

        $csrf_token = \App\Core\SessionManager::generateCsrfToken();
        $success = \App\Core\SessionManager::getFlash('success');
        $error = \App\Core\SessionManager::getFlash('error');

        require_once __DIR__ . '/../../resources/views/cart/index.php';
    }

    /**
     * Procesa la adición al carrito
     */
    public function add()
    {
        \App\Core\SessionManager::start();
        if (empty($_SESSION['user_id'])) {
            \App\Core\SessionManager::setFlash('error', 'Debes iniciar sesión para agregar productos.');
            header("Location: /login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("HTTP/1.0 405 Method Not Allowed");
            exit;
        }

        // Validación de CSRF Token obligatoria
        if (!\App\Core\SessionManager::validateCsrfToken($_POST['csrf_token'] ?? null)) {
            die("Error de seguridad: Token CSRF inválido.");
        }

        $userId = $_SESSION['user_id'];
        $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
        $qty = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;
        // isset sirve para verificar si una variable existe y no es null

        $result = $this->service->addToCart($userId, $productId, $qty);

        if ($result['success']) {
            \App\Core\SessionManager::setFlash('success', 'Producto agregado correctamente.');
            header("Location: /carrito");
        }
        else {
            \App\Core\SessionManager::setFlash('error', $result['message']);
            header("Location: /producto/" . $productId);
        }
        exit;
    }

    /**
     * Actualiza la cantidad de un producto
     */
    public function update()
    {
        \App\Core\SessionManager::start();
        if (empty($_SESSION['user_id'])) {
            header("Location: /login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !\App\Core\SessionManager::validateCsrfToken($_POST['csrf_token'] ?? null)) {
            die("Error de seguridad");
        }

        $userId = $_SESSION['user_id'];
        $productId = (int)($_POST['product_id'] ?? 0);
        $qty = (int)($_POST['quantity'] ?? 0);

        $result = $this->service->updateCartItem($userId, $productId, $qty);

        if ($result['success']) {
            \App\Core\SessionManager::setFlash('success', $result['message']);
        }
        else {
            \App\Core\SessionManager::setFlash('error', $result['message']);
        }

        header("Location: /carrito");
        exit;
    }

    /**
     * Elimina un producto del carrito
     */
    public function remove()
    {
        \App\Core\SessionManager::start();
        if (empty($_SESSION['user_id'])) {
            header("Location: /login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !\App\Core\SessionManager::validateCsrfToken($_POST['csrf_token'] ?? null)) {
            die("Error de seguridad");
        }

        $userId = $_SESSION['user_id'];
        $productId = (int)($_POST['product_id'] ?? 0);

        $result = $this->service->removeCartItem($userId, $productId);

        if ($result['success']) {
            \App\Core\SessionManager::setFlash('success', $result['message']);
        }
        else {
            \App\Core\SessionManager::setFlash('error', $result['message']);
        }

        header("Location: /carrito");
        exit;
    }

    /**
     * Procesa la compra del carrito actual
     */
    public function checkout()
    {
        \App\Core\SessionManager::start();
        if (empty($_SESSION['user_id'])) {
            header("Location: /login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !\App\Core\SessionManager::validateCsrfToken($_POST['csrf_token'] ?? null)) {
            die("Error de seguridad");
        }

        $userId = $_SESSION['user_id'];
        $result = $this->orderService->checkout($userId);

        if ($result['success']) {
            \App\Core\SessionManager::setFlash('success', $result['message']);
            header("Location: /mis-ordenes");
        }
        else {
            \App\Core\SessionManager::setFlash('error', $result['message']);
            header("Location: /carrito");
        }
        exit;
    }
}