<?php

namespace App\Controllers;

use App\Core\SessionManager;
use App\Core\Database;

/**
 * ProviderController maneja el CRUD de inventario exclusivo para cada Proveedor
 */
class ProviderController extends BaseController
{
    /**
     * Lista los productos del proveedor actual
     */
    public function index()
    {
        $providerId = $_SESSION['user_id'];
        $db = Database::getInstance();

        // Los admins pueden ver todos los productos en este panel para asistencia, o solo el suyo. 
        // Implementaremos que vean solo los suyos si deciden vender, o podemos condicionar.
        // Para mantenerlo simple, carga solo los del provider_id en la sesión.
        $stmt = $db->prepare("SELECT * FROM products WHERE provider_id = :pid ORDER BY id DESC");
        $stmt->execute(['pid' => $providerId]);
        $productsRaw = $stmt->fetchAll();

        // Zero Raw Data
        $products = [];
        foreach ($productsRaw as $product) {
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
     * Crea un nuevo producto
     */
    public function store()
    {
        if (!SessionManager::validateCsrfToken($_POST['csrf_token'] ?? null)) {
            die("Error de seguridad");
        }

        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $price = (float)($_POST['price'] ?? 0);
        $stock = (int)($_POST['stock_quantity'] ?? 0);
        $providerId = $_SESSION['user_id'];

        if ($price <= 0 || $stock < 0 || empty($name)) {
            SessionManager::setFlash('error', 'Datos inválidos. Verifica el precio y stock.');
            header('Location: /proveedor/productos');
            exit;
        }

        $db = Database::getInstance();
        $stmt = $db->prepare("INSERT INTO products (name, description, price, stock_quantity, provider_id) VALUES (:name, :desc, :price, :stock, :pid)");

        try {
            $stmt->execute([
                'name' => $name,
                'desc' => $description,
                'price' => $price,
                'stock' => $stock,
                'pid' => $providerId
            ]);
            SessionManager::setFlash('success', 'Producto agregado exitosamente.');
        }
        catch (\PDOException $e) {
            SessionManager::setFlash('error', 'Error al guardar el producto.');
        }

        header('Location: /proveedor/productos');
        exit;
    }

    /**
     * Elimina un producto
     */
    public function delete()
    {
        if (!SessionManager::validateCsrfToken($_POST['csrf_token'] ?? null)) {
            die("Error de seguridad");
        }

        $productId = (int)($_POST['product_id'] ?? 0);
        $providerId = $_SESSION['user_id'];

        $db = Database::getInstance();

        try {
            // Se asegura que solo pueda borrar su propio producto
            $stmt = $db->prepare("DELETE FROM products WHERE id = :id AND provider_id = :pid");
            $stmt->execute(['id' => $productId, 'pid' => $providerId]);

            if ($stmt->rowCount() > 0) {
                SessionManager::setFlash('success', 'Producto eliminado.');
            }
            else {
                SessionManager::setFlash('error', 'Producto no encontrado o no autorizado.');
            }
        }
        catch (\PDOException $e) {
            // Error 1451: ON DELETE RESTRICT (está en order_items)
            if ($e->getCode() == 23000) {
                // Borrado Lógico: Lo ocultamos del catálogo y lo "desactivamos" vaciando su stock
                $stmt = $db->prepare("UPDATE products SET stock_quantity = 0, name = CONCAT(name, ' (Descontinuado)') WHERE id = :id AND provider_id = :pid");
                $stmt->execute(['id' => $productId, 'pid' => $providerId]);
                SessionManager::setFlash('success', 'El producto tiene historial de ventas. Se ha descontinuado lógicamente (Stock a 0).');
            }
            else {
                SessionManager::setFlash('error', 'Error al eliminar el producto.');
            }
        }

        header('Location: /proveedor/productos');
        exit;
    }
}