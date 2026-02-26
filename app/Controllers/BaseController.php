<?php

namespace App\Controllers;

/**
 * BaseController proporciona helpers para todos los controladores
 */
abstract class BaseController
{

    /**
     * Regla de Oro: Zero Raw Data
     * Genera una cadena para su salida segura en HTML
     */
    protected function escape($data)
    {
        if (is_array($data)) {
            return array_map([$this, 'escape'], $data); // Aplica la función a cada elemento del array
        }
        return htmlspecialchars($data, ENT_QUOTES, 'UTF-8'); // Convierte caracteres especiales a entidades HTML
    }

    /**
     * Helper para enviar respuestas JSON (Para el carrito)
     */
    protected function jsonResponse(array $data, int $statusCode = 200)
    {
        header('Content-Type: application/json'); // Establece el tipo de contenido de la respuesta
        http_response_code($statusCode); // Establece el código de respuesta HTTP
        echo json_encode($data); // Convierte el array a JSON y lo envía
        exit; // Termina la ejecución
    }
}