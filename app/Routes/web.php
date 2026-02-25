<?php

/**
 * app/Routes/web.php
 * Aquí se definen todas las rutas web de la aplicación.
 * La variable $router está disponible desde public/index.php.
 */

// Catálogo y Producto (Rutas publicas)
$router->get('/', 'ProductController@index');
$router->get('/producto/{id}', 'ProductController@show');

// Autenticacion (Rutas publicas)
$router->get('/login', 'AuthController@showLogin');
$router->post('/login', 'AuthController@login');
$router->get('/register', 'AuthController@showRegister');
$router->post('/register', 'AuthController@register');
$router->get('/logout', 'AuthController@logout');

// Carrito y Órdenes (Rutas protegidas que requieren autenticarse)
$router->group(['middleware' => [\App\Middlewares\AuthMiddleware::class]], function ($router) {
    $router->get('/carrito', 'CartController@showCart');
    $router->post('/carrito/agregar', 'CartController@add');
    $router->post('/carrito/actualizar', 'CartController@update');
    $router->post('/carrito/eliminar', 'CartController@remove');
    $router->post('/carrito/checkout', 'CartController@checkout');
    $router->get('/mis-ordenes', 'OrderController@index');
});

// Admin (Rutas protegidas que requieren autenticarse como admin)
$router->group(['prefix' => '/admin', 'middleware' => [\App\Middlewares\AdminMiddleware::class]], function ($router) {
    $router->get('/usuarios', 'AdminController@index');
    $router->post('/usuarios/rol', 'AdminController@updateRole');
    $router->get('/usuarios/{id}/editar', 'AdminController@edit');
    $router->post('/usuarios/{id}/editar', 'AdminController@update');
    $router->post('/usuarios/eliminar', 'AdminController@delete');
});

// Proveedor (Rutas para gestión de inventario, accesible a proveedores y admins)
$router->group(['prefix' => '/proveedor', 'middleware' => [\App\Middlewares\ProviderMiddleware::class]], function ($router) {
    $router->get('/productos', 'ProviderController@index');
    $router->post('/productos/agregar', 'ProviderController@store');
    $router->post('/productos/eliminar', 'ProviderController@delete');
});