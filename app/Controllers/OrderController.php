<?php

namespace App\Controllers;

use App\Services\OrderService;
use App\Core\SessionManager;

/**
 * OrderController maneja la visualización del historial de compras
 */
class OrderController extends BaseController
{
    private OrderService $service;

    public function __construct()
    {
        $this->service = new OrderService(
            new \App\Repositories\OrderRepository(),
            new \App\Repositories\CartRepository(),
            new \App\Repositories\ProductRepository()
            );
    }

    /**
     * Lista las órdenes del usuario
     */
    public function index()
    {
        SessionManager::start();
        if (empty($_SESSION['user_id'])) {
            SessionManager::setFlash('error', 'Debes iniciar sesión para ver tus compras.');
            header("Location: /login");
            exit;
        }

        $userId = $_SESSION['user_id'];

        $ordersRaw = $this->service->getUserOrders($userId);

        // Zero Raw Data
        $orders = [];
        foreach ($ordersRaw as $o) {
            $order = [];
            foreach ($o as $key => $val) {
                $order[$key] = $this->escape($val ?? '');
            }

            // Traer items de la orden
            $itemsRaw = $this->service->getOrderItems($order['id'], $userId);
            $order['items'] = [];
            foreach ($itemsRaw as $item) {
                $i = [];
                foreach ($item as $k => $v) {
                    $i[$k] = $this->escape($v ?? '');
                }
                $order['items'][] = $i;
            }

            $orders[] = $order;
        }

        $success = SessionManager::getFlash('success');
        $error = SessionManager::getFlash('error');

        require_once __DIR__ . '/../../resources/views/order/index.php';
    }
}