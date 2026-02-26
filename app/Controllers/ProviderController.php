<?php

namespace App\Controllers;

use App\Services\ProviderService;
use App\Repositories\ProviderRepository;
use App\Core\SessionManager;
use Exception;

/**
 * ProviderController maneja el CRUD de inventario exclusivo para cada Proveedor
 */
class ProviderController extends BaseController
{
    private $service;

    public function __construct()
    {
        $this->service = new ProviderService(new ProviderRepository());
    }

    /**
     * index muestra todos los productos del proveedor
     */
    public function index()
    {
        $rawProducts = $this->service->getProviderProducts($_SESSION['user_id']);

        // Zero Raw Data
        $products = [];
        foreach ($rawProducts as $product) {
            $p = [];
            foreach ($product as $key => $val) {
                $p[$key] = $this->escape($val ?? '');
            }
            $products[] = $p;
        }

        $csrf_token = SessionManager::generateCsrfToken();
        $success = SessionManager::getFlash('success');
        $error = SessionManager::getFlash('error');

        require_once __DIR__ . '/../../resources/views/provider/products.php';
    }

    /**
     * store agrega un nuevo producto
     */
    public function store()
    {
        $this->validateCsrf(); // Validamos si el token es correcto segun la sesion

        try {
            $data = [
                'name' => trim($_POST['name'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'price' => (float)($_POST['price'] ?? 0),
                'stock_quantity' => (int)($_POST['stock_quantity'] ?? 0),
                'provider_id' => $_SESSION['user_id'],
                'image_url' => null
            ];

            $this->service->createProduct($data, $_FILES['image'] ?? null);
            SessionManager::setFlash('success', 'Producto agregado.');
        }
        catch (Exception $e) {
            SessionManager::setFlash('error', $e->getMessage());
        }

        header('Location: /proveedor/productos');
        exit;
    }

    /**
     * delete elimina un producto
     */
    public function delete()
    {
        $this->validateCsrf();

        $productId = (int)($_POST['product_id'] ?? 0); // ID del producto a eliminar
        $providerId = $_SESSION['user_id'];

        try {
            $result = $this->service->deleteProduct($productId, $providerId);

            switch ($result) {
                case 'success_deleted':
                    SessionManager::setFlash('success', 'Producto eliminado.');
                    break;
                case 'success_discontinued':
                    SessionManager::setFlash('success', 'El producto tiene historial de ventas. Se ha descontinuado lógicamente.');
                    break;
                case 'not_found':
                    SessionManager::setFlash('error', 'Producto no encontrado o no autorizado.');
                    break;
            }
        }
        catch (Exception $e) {
            SessionManager::setFlash('error', $e->getMessage());
        }

        header('Location: /proveedor/productos');
        exit;

    }

    /**
     * edit edita un producto
     */
    public function edit(string $id)
    {
        try {
            $productId = (int)$id;
            $providerId = $_SESSION['user_id'];

            $product = $this->service->getProductForEditing($productId, $providerId);

            $csrf_token = SessionManager::generateCsrfToken();
            $success = SessionManager::getFlash('success');
            $error = SessionManager::getFlash('error');
        }
        catch (Exception $e) {
            SessionManager::setFlash('error', $e->getMessage());
        }
        require_once __DIR__ . '/../../resources/views/provider/edit_product.php';
        header('Location: /proveedor/productos');
        exit;
    }

    /**
     * update actualiza un producto
     */
    public function update(string $id)
    {
        $this->validateCsrf();

        try {
            $input = [
                'name' => trim($_POST['name'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'price' => (float)($_POST['price'] ?? 0),
                'stock_quantity' => (int)($_POST['stock_quantity'] ?? 0),
            ];

            $this->service->updateProduct(
                (int)$id,
                $_SESSION['user_id'],
                $input,
                $_FILES['image'] ?? null
            );

            SessionManager::setFlash('success', 'Producto editado exitosamente.');
        }
        catch (Exception $e) {
            SessionManager::setFlash('error', $e->getMessage());
        }

        header('Location: /proveedor/productos');
        exit;
    }

    /**
     * validateCsrf valida el token CSRF para el proovedor
     */
    private function validateCsrf()
    {
        if (!SessionManager::validateCsrfToken($_POST['csrf_token'] ?? null)) {
            die("Error de seguridad");
        }
    }
}