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
     * Muestra el catálogo completo
     */
    public function index()
    {
        $products = $this->productService->listAllProducts();

        // En una implementación real, aquí cargaríamos una view
        // Por ahora para el Sprint 2, mostramos datos escapados
        echo "<h1>Catálogo de Productos</h1>";
        foreach ($products as $p) {
            $name = $this->escape($p['name']);
            $price = $this->escape($p['price']);
            echo "<div><strong>{$name}</strong> - \${$price} <a href='/producto/{$p['id']}'>Ver detalle</a></div>";
        }
    }

    /**
     * Muestra el detalle de un solo producto
     */
    public function show($id)
    {
        $product = $this->productService->getProductById((int)$id);

        if (!$product) {
            header("HTTP/1.0 404 Not Found");
            echo "<h1>Producto no encontrado</h1>";
            return;
        }

        $name = $this->escape($product['name']);
        $desc = $this->escape($product['description']);
        $price = $this->escape($product['price']);
        $stock = $this->escape($product['stock_quantity']);

        echo "<h1>Detalle de: {$name}</h1>";
        echo "<p>{$desc}</p>";
        echo "<p>Precio: \${$price}</p>";
        echo "<p>Stock disponible: {$stock}</p>";
        echo "<a href='/'>Regresar al catálogo</a>";
    }
}