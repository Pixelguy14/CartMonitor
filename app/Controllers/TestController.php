<?php

namespace App\Controllers;

class TestController
{

    // Test the base route
    public function index()
    {
        echo "<h2>TestController Loaded!</h2>";
        echo "<p>The manual Router mapped '/prueba' to the index method correctly.</p>";
    }

    // Test a dynamic route
    public function showProduct($id)
    {
        // Sanitize generic HTML output as a golden rule 
        $safeId = htmlspecialchars($id, ENT_QUOTES, 'UTF-8');

        echo "<h2>TestController Dynamic Route</h2>";
        echo "<p>Showing product ID: {$safeId}</p>";
    }

}