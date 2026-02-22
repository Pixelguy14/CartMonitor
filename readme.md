# CartMonitor: A Pure PHP Layered E-commerce Engine

Ejercicio de arquitectura en capas con seguridad básica y despliegue moderno.

## explicacion de la arquitectura

docker-compose up -d --build

Accede en http://localhost:8080. 


Servicios:
        App Container: PHP 8.2 + Apache (con mod_rewrite habilitado para el Front Controller).
        DB Container: MySQL 8.0.

        
Estructura de Directorios:
/CartMonitor
├── app/
│   ├── Controllers/ (Recibe request, llama service)
│   ├── Services/    (Lógica de negocio: stock, totales)
│   ├── Repositories/(Queries SQL con PDO)
│   ├── Core/        (Router manual, Singleton DB)
│   └── Routes/      (Mapeo de URLs)
├── public/          (Único punto de entrada: index.php, assets)
├── resources/views/ (Plantillas HTML/Bootstrap)
└── .env             (Configuración sensible)