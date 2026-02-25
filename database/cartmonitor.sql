-- 1. Usuarios (Seguridad y Sesiones)
-- Esta tabla almacenará información de los usuarios registrados en el e-commerce.
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20),
    external_payment_id VARCHAR(100) NULL,
    type ENUM('admin', 'usuario','proveedor') DEFAULT 'usuario',
    password_hash VARCHAR(255) NOT NULL, -- Suficiente para password_hash() de PHP
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Productos (Catálogo) 
-- Esta tabla almacenará información sobre cada producto disponible en el e-commerce.
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    provider_id INT NOT NULL, -- relacion con los usuarios proovedores
    stock_quantity INT UNSIGNED NOT NULL DEFAULT 0,
    image_url VARCHAR(255), -- Necesario para las "Cards" de Bootstrap
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (provider_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX (name)
);

-- 3. Carrito (Gestión de Carrito)
-- Esta tabla almacena los productos seleccionados por un usuario y sus detalles durante el proceso de compra.
CREATE TABLE cart_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT UNSIGNED NOT NULL DEFAULT 1,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE, -- uno a muchos 
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE -- uno a muchos
);

-- 4. Órdenes (Historial de compras)
-- Esta tabla almacena los detalles de las compras realizadas por los usuarios.
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pendiente', 'completada', 'cancelada') DEFAULT 'pendiente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE -- uno a muchos 
);

-- 5. Detalle de Órden (Conserva el precio al momento de la compra)
-- Esta tabla almacena los productos incluidos en cada orden.
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT UNSIGNED NOT NULL,
    price_at_purchase DECIMAL(10, 2) NOT NULL, -- Regla de negocio: el precio no debe cambiar si el producto sube después
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE, -- uno a muchos 
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT -- Evita borrar productos con historial de ventas
);

-- 6. Sesiones y Seguridad (Refresh Tokens)
-- Esta tabla almacena las sesiones activas de los usuarios.
CREATE TABLE user_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token_hash VARCHAR(255) UNIQUE NOT NULL, -- El token generado se guarda hasheado
    device_info VARCHAR(255), -- Ej: "Chrome en Windows 11" para que el usuario reconozca su sesión
    ip_address VARCHAR(45), -- Soporta IPv4 e IPv6
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);