<?php

namespace App\Services;

use App\Repositories\ProviderRepository;
use Exception;

/**
 * ProductService maneja la lógica de negocio para los productos
 */
class ProviderService
{
    private $repository;
    private $uploadDir;

    private const MAX_PRICE = 1000000;

    public function __construct(ProviderRepository $repository)
    {
        $this->repository = $repository;
        $this->uploadDir = dirname(__DIR__, 2) . '/storage/images/';
    }

    /**
     * Obtiene todos los productos de un proveedor
     */
    public function getProviderProducts(int $providerId): array
    {
        return $this->repository->getAllByProvider($providerId);
    }

    /**
     * Crea un nuevo producto
     */
    public function createProduct(array $data, ?array $file): bool
    {
        // Si el precio es inferior a 0 o el nombre esta vacio, lanzamos una excepcion
        if ($data['price'] <= 0 || empty($data['name'])) {
            throw new Exception("Datos del producto invalidos.");
        }

        // Si el precio supera los 1000000, lanzamos una excepcion
        if ($data['price'] > self::MAX_PRICE) {
            throw new Exception("El precio del producto no puede superar los " . self::MAX_PRICE . ".");
        }

        if ($file && $file['error'] === UPLOAD_ERR_OK) {
            $data['image_url'] = $this->uploadImage($file);
        }

        return $this->repository->create($data);
    }

    /**
     * Buscamos un producto para preparar en la vista de editar
     */
    public function getProductForEditing(int $id, int $providerId): array
    {
        $product = $this->repository->findByIdAndProvider($id, $providerId);

        if (!$product) {
            throw new Exception("Producto no encontrado o no autorizado.");
        }

        // Logica de negocio: Sanitizamos los datos antes de enviarlos al controlador
        return array_map(function ($value) {
            return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
        }, $product);
    }

    /**
     * Actualiza un producto
     */
    public function updateProduct(int $id, int $providerId, array $input, ?array $file): bool
    {
        // Regla de negocio: El precio debe ser mayor a 0 y el nombre no debe estar vacio
        if ($input['price'] <= 0 || empty($input['name'])) {
            throw new Exception("Datos inválidos. Verifica el nombre y el precio.");
        }

        // Preparamos los datos
        $data = [
            'name' => $input['name'],
            'description' => $input['description'],
            'price' => $input['price'],
            'stock_quantity' => $input['stock_quantity']
        ];

        // Manejamos la subida de la imagen
        $oldImageUrl = null;
        $newImageUploaded = false;

        if ($file && $file['error'] === UPLOAD_ERR_OK) {
            // Buscamos el producto para obtener la imagen anterior
            $oldProduct = $this->repository->findByIdAndProvider($id, $providerId);
            $oldImageUrl = $oldProduct['image_url'] ?? null;

            $data['image_url'] = $this->uploadImage($file);
            $newImageUploaded = true;
        }

        // Realizamos la actualización en la base de datos (siempre ocurre)
        $success = $this->repository->update($id, $providerId, $data);

        // Si la actualización fue exitosa y subimos una imagen nueva, borramos la anterior del disco
        if ($success && $newImageUploaded && $oldImageUrl) {
            $this->delete_physical_file($oldImageUrl);
        }

        return $success;
    }

    /**
     * Maneja la subida de imágenes
     */
    private function uploadImage(array $file): string
    {
        if (!is_dir($this->uploadDir))
            mkdir($this->uploadDir, 0777, true);

        $filename = uniqid('prod_') . '_' . basename($file['name']);
        $target = $this->uploadDir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $target)) {
            throw new Exception("Error al subir la imagen.");
        }

        return '/storage/images/' . $filename;
    }

    /**
     * Borra la imagen del disco
     */
    private function delete_physical_file(?string $imageUrl): void
    {
        if (empty($imageUrl))
            return;

        // Extraemos el nombre del archivo
        $filename = basename($imageUrl);

        // CLo concadenamos al directorio original
        $absolutePath = $this->uploadDir . $filename;

        // Por seguridad, verificamos que el archivo existe y es un archivo
        if (file_exists($absolutePath) && is_file($absolutePath)) {
            unlink($absolutePath);
        }
    }

    /**
     * Elimina un producto
     */
    public function deleteProduct(int $id, int $providerId): string
    {
        try {
            $product = $this->repository->findByIdAndProvider($id, $providerId);
            if (!$product)
                return "not_found";

            if ($this->repository->hardDelete($id, $providerId)) {
                $this->delete_physical_file($product['image_url']);
                return "success_deleted";
            }
            return "not_found";
        }
        catch (\PDOException $e) {
            // Si un producto ya tiene una orden de compra, no se puede eliminar fisicamente, solo descontinuamos.
            if ($e->getCode() == 23000) {
                $this->repository->softDelete($id, $providerId);
                return "success_discontinued";
            }
            throw $e;
        }
    }
}