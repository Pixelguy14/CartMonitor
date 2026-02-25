<?php

namespace App\Controllers;

use App\Services\ProductService;

/**
 * ProductController maneja las peticiones relacionadas con el catálogo
 */
class ProductController extends BaseController
{
    private ProductService $productService;

    public function __construct()
    {
        $this->productService = new ProductService();
    }

    /**
     * Muestra el catálogo principal con filtros opcionales
     */
    public function index()
    {
        $q = isset($_GET['q']) ? trim($_GET['q']) : null;
        $minPrice = isset($_GET['min_price']) && is_numeric($_GET['min_price']) ? (float)$_GET['min_price'] : null;
        $maxPrice = isset($_GET['max_price']) && is_numeric($_GET['max_price']) ? (float)$_GET['max_price'] : null;

        $productsRaw = $this->productService->listAllProducts($q, $minPrice, $maxPrice);

        // Zero Raw Data: Escapamos todos los productos
        $products = [];
        foreach ($productsRaw as $p) {
            $p['name'] = $this->escape($p['name']);
            $p['description'] = $this->escape($p['description']);
            $p['price'] = $this->escape($p['price']);
            $p['image_url'] = $this->escape($p['image_url'] ?? '');
            $products[] = $p;
        }

        $searchQuery = $this->escape($q ?? '');
        $searchMin = $this->escape((string)$minPrice ?? '');
        $searchMax = $this->escape((string)$maxPrice ?? '');

        require_once __DIR__ . '/../../resources/views/catalog/index.php';
    }

    /**
     * Muestra el detalle de un solo producto
     */
    public function show($id)
    {
        $productRaw = $this->productService->getProductById((int)$id);

        if (!$productRaw) {
            header("HTTP/1.0 404 Not Found");
            echo "<h1>Producto no encontrado</h1>";
            return;
        }

        // Zero Raw Data
        $p = [];
        $p['id'] = $this->escape($productRaw['id']);
        $p['name'] = $this->escape($productRaw['name']);
        $p['description'] = $this->escape($productRaw['description']);
        $p['price'] = $this->escape($productRaw['price']);
        $p['stock_quantity'] = $this->escape($productRaw['stock_quantity']);
        $p['image_url'] = $this->escape($productRaw['image_url'] ?? '');

        require_once __DIR__ . '/../../resources/views/catalog/show.php';
    }
}