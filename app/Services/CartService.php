<?php

namespace App\Services;

use App\Repositories\CartRepository;
use App\Repositories\ProductRepository;

/**
 * CartService maneja la lógica de negocio del carrito
 * Implementa validaciones estrictas requeridas
 */
class CartService extends BaseService
{
    private CartRepository $cartRepository;
    private ProductRepository $productRepository;

    public function __construct(CartRepository $cartRepository, ProductRepository $productRepository)
    {
        $this->cartRepository = $cartRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * Agrega un producto al carrito previa validación de stock e ID
     */
    public function addToCart(int $userId, int $productId, int $quantity): array
    {
        // Regla de Oro: Validación Estricta
        if ($quantity <= 0) {
            return ['success' => false, 'message' => 'La cantidad debe ser mayor a cero.'];
        }

        $product = $this->productRepository->findById($productId);
        if (!$product) {
            return ['success' => false, 'message' => 'El producto no existe.'];
        }

        // Validación de Stock
        if ($product['stock_quantity'] < $quantity) {
            return ['success' => false, 'message' => 'Stock insuficiente.'];
        }

        $result = $this->cartRepository->addItem($userId, $productId, $quantity);
        return [
            'success' => $result,
            'message' => $result ? 'Producto agregado al carrito.' : 'Error al agregar al carrito.'
        ];
    }

    /**
     * Actualiza la cantidad de un producto en el carrito (Validando stock)
     */
    public function updateCartItem(int $userId, int $productId, int $newQuantity): array
    {
        if ($newQuantity <= 0) {
            return $this->removeCartItem($userId, $productId);
        }

        $product = $this->productRepository->findById($productId);
        if (!$product) {
            return ['success' => false, 'message' => 'El producto no existe.'];
        }

        if ($product['stock_quantity'] < $newQuantity) {
            return ['success' => false, 'message' => 'Stock insuficiente para esa cantidad.'];
        }

        $result = $this->cartRepository->updateQuantity($userId, $productId, $newQuantity);
        return [
            'success' => $result,
            'message' => $result ? 'Cantidad actualizada.' : 'Error al actualizar.'
        ];
    }

    /**
     * Elimina un producto del carrito
     */
    public function removeCartItem(int $userId, int $productId): array
    {
        $result = $this->cartRepository->removeItem($userId, $productId);
        return [
            'success' => $result,
            'message' => $result ? 'Producto eliminado del carrito.' : 'Error al eliminar.'
        ];
    }

    /**
     * Obtiene el carrito con totales calculados
     */
    public function getCartTotals(int $userId): array
    {
        $items = $this->cartRepository->getItemsByUser($userId);
        $subtotal = 0;

        foreach ($items as &$item) {
            $item['item_total'] = $item['price'] * $item['quantity'];
            $subtotal += $item['item_total'];
        }

        return [
            'items' => $items,
            'subtotal' => $subtotal,
            'total' => $subtotal // Aquí se podrían añadir impuestos o envío
        ];
    }
}