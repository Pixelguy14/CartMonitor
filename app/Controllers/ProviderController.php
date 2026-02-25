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

        $imageUrl = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '/var/www/html/storage/images/';
            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0777, true)) {
                    SessionManager::setFlash('error', 'Error: No se pudo crear el directorio de imágenes en storage/. Revisa los permisos del host.');
                    header('Location: /proveedor/productos');
                    exit;
                }
            }
            $filename = uniqid('prod_') . '_' . basename($_FILES['image']['name']);
            $targetPath = $uploadDir . $filename;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                $imageUrl = '/storage/images/' . $filename;
            }
        }

        if ($price <= 0 || $stock < 0 || empty($name)) {
            SessionManager::setFlash('error', 'Datos inválidos. Verifica el precio y stock.');
            header('Location: /proveedor/productos');
            exit;
        }

        $db = Database::getInstance();
        $stmt = $db->prepare("INSERT INTO products (name, description, price, stock_quantity, provider_id, image_url) VALUES (:name, :desc, :price, :stock, :pid, :img)");

        try {
            $stmt->execute([
                'name' => $name,
                'desc' => $description,
                'price' => $price,
                'stock' => $stock,
                'pid' => $providerId,
                'img' => $imageUrl
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

        $productId = (int)($_POST['product_id'] ?? 0); // ID del producto a eliminar
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
            if ($e->getCode() == 23000) { // Error 23000 es el codigo de error de integridad referencial en MySQL
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
    /*
     * Muestra el formulario para editar un producto
     */
    public function edit(string $id)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT id, name, description, price, stock_quantity, image_url FROM products WHERE id = :id AND provider_id = :pid");
        $stmt->execute(['id' => $id, 'pid' => $_SESSION['user_id']]);
        $product = $stmt->fetch();

        if (!$product) {
            SessionManager::setFlash('error', 'Producto no encontrado o no autorizado.');
            header('Location: /proveedor/productos');
            exit;
        }

        $prod = [];
        foreach ($product as $key => $val) {
            $prod[$key] = $this->escape($val ?? '');
        }

        $csrf_token = SessionManager::generateCsrfToken();
        $error = SessionManager::getFlash('error');

        require_once __DIR__ . '/../../resources/views/provider/edit_product.php';
    }


    /*
     * Actualiza los datos de un producto
     */
    public function update(string $id)
    {
        if (!SessionManager::validateCsrfToken($_POST['csrf_token'] ?? null)) {
            die("Error de seguridad");
        }

        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $price = (float)($_POST['price'] ?? 0);
        $stock = (int)($_POST['stock_quantity'] ?? 0);
        $providerId = $_SESSION['user_id'];

        $db = Database::getInstance();

        // Mantener imagen original si no se sube una nueva
        $imageUrl = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '/var/www/html/storage/images/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $filename = uniqid('prod_') . '_' . basename($_FILES['image']['name']);
            $targetPath = $uploadDir . $filename;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                $imageUrl = '/storage/images/' . $filename;
            }
        }

        if ($imageUrl !== null) {
            $stmt = $db->prepare("UPDATE products SET name = :name, description = :description, price = :price, stock_quantity = :stock_quantity, image_url = :image_url WHERE id = :id AND provider_id = :pid");
            $params = [
                'name' => $name,
                'description' => $description,
                'price' => $price,
                'stock_quantity' => $stock,
                'image_url' => $imageUrl,
                'id' => $id,
                'pid' => $providerId
            ];
        }
        else {
            $stmt = $db->prepare("UPDATE products SET name = :name, description = :description, price = :price, stock_quantity = :stock_quantity WHERE id = :id AND provider_id = :pid");
            $params = [
                'name' => $name,
                'description' => $description,
                'price' => $price,
                'stock_quantity' => $stock,
                'id' => $id,
                'pid' => $providerId
            ];
        }

        try {
            $stmt->execute($params);
            SessionManager::setFlash('success', 'Producto editado exitosamente.');
        }
        catch (\PDOException $e) {
            SessionManager::setFlash('error', 'Error al editar el producto.');
        }

        header('Location: /proveedor/productos');
        exit;
    }
}