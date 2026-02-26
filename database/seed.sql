-- Datos predeterminados para la validación rápida del portafolio.
-- Se inserta después de `cartmonitor.sql` inicial.

-- Usuario Administrador
INSERT IGNORE INTO users (username, email, type, password_hash) 
VALUES ('Administrador', 'admin@cartmonitor.com', 'admin', '$2y$10$hLh85tAE/7np.hKrrSXn4usX0JTbA.5kjBjKcoBrDJlLrOkFoSOZ6');

-- Usuario Proveedor
INSERT IGNORE INTO users (username, email, type, password_hash) 
VALUES ('ProveedorTech', 'proveedor@cartmonitor.com', 'proveedor', '$2y$10$hLh85tAE/7np.hKrrSXn4usX0JTbA.5kjBjKcoBrDJlLrOkFoSOZ6');

-- Productos Iniciales
INSERT IGNORE INTO products (name, description, price, provider_id, stock_quantity) 
VALUES 
('Laptop Master Pro', 'Equipo de alto rendimiento para desarrolladores con 32GB RAM y procesador M-Series', 2499.50, (SELECT id FROM users WHERE email='proveedor@cartmonitor.com'), 10),
('Teclado Mecánico', 'Teclado estilo hacker con switches Blue y layout en inglés para máxima productividad', 120.00, (SELECT id FROM users WHERE email='proveedor@cartmonitor.com'), 50),
('Monitor Ultrawide 34"', 'Pantalla ultra ancha 4K perfecta para multitarea y edición de video o lectura de código', 650.99, (SELECT id FROM users WHERE email='proveedor@cartmonitor.com'), 5);
