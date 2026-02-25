# CartMonitor: A Pure PHP Layered E-commerce Engine

Ejercicio de arquitectura en capas con seguridad básica y despliegue moderno.

## explicacion de la arquitectura

docker compose up -d --build

docker ps # verificar los contenedores activos

docker logs cartmonitor_app # verificar logs del contenedor

docker stop cartmonitor_app # detener contenedor
docker stop cartmonitor_db # detener contenedor
docker rm cartmonitor_app -f # eliminar contenedor
docker rm cartmonitor_db -f # eliminar contenedor

docker compose down -v # Para borrar los contenedores y el volumen de datos viejo.
docker compose up -d # Para iniciar todo de nuevo y que MySQL ejecute el init.sql.

Accede en http://localhost:8081. 


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