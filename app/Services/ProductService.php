<?php

namespace App\Services;

use App\Repositories\ProductRepository;

/**
 * ProductService maneja la lógica de negocio de los productos
 */
class ProductService extends BaseService
{
    private ProductRepository $productRepository;

    public function __construct()
    {
        $this->productRepository = new ProductRepository();
    }

    /**
     * Obtiene los productos, opcionalmente filtrados
     */
    public function listAllProducts(?string $q = null, ?float $minPrice = null, ?float $maxPrice = null): array
    {
        return $this->productRepository->advancedSearch($q, $minPrice, $maxPrice);
    }

    /**
     * Obtiene el detalle de un producto
     */
    public function getProductById(int $id): ?array
    {
        if ($id <= 0)
            return null;
        return $this->productRepository->findById($id);
    }

    /**
     * Valida si hay stock suficiente para una cantidad solicitada
     */
    public function hasEnoughStock(int $productId, int $requestedQty): bool
    {
        $product = $this->productRepository->findById($productId);
        if (!$product)
            return false;

        return $product['stock_quantity'] >= $requestedQty;
    }
}