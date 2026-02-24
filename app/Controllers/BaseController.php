<?php

namespace App\Controllers;

/**
 * BaseController proporciona helpers para todos los controladores
 */
abstract class BaseController
{

    /**
     * Regla de Oro: Zero Raw Data
     * Escapa una cadena para su salida segura en HTML
     */
    protected function escape($data)
    {
        if (is_array($data)) {
            return array_map([$this, 'escape'], $data);
        }
        return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Helper para enviar respuestas JSON (Para el carrito)
     */
    protected function jsonResponse(array $data, int $statusCode = 200)
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
        exit;
    }
}