# CartMonitor: A Pure PHP Layered E-commerce Engine

Ejercicio de arquitectura en capas con seguridad básica y despliegue moderno.

## Explicacion de la arquitectura

docker compose up -d --build # forzamos que se construya la imagen al iniciar

docker ps # verificar los contenedores activos

docker logs cartmonitor_app # verificar logs del contenedor

docker stop cartmonitor_app # detener contenedor
docker stop cartmonitor_db # detener contenedor
docker rm cartmonitor_app -f # eliminar contenedor
docker rm cartmonitor_db -f # eliminar contenedor

docker compose up -d # para reactivar el docker

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
│   ├── Core/        (Router manual, Singleton DB)
│   ├── Middlewares/ (Manejo de seguridad y sesiones de los distintos roles)
│   ├── Repositories/(Queries SQL con PDO)
│   ├── Routes/      (Mapeo de URLs)
│   └── Services/    (Lógica de negocio: stock, totales)
├── database/        (Base de datos y scripts de inicialización)
├── public/          (Único punto de entrada: index.php, assets)
│   ├── css/         (Estilos CSS)
│   ├── images/      (Diagramas y otras imagenes relacionadas al desarrollo)
│   └── storage/     (Almacenamiento de imágenes con link simbolico)
├── resources/views/ (Plantillas HTML/Bootstrap)
│   ├── admin/       (Plantillas para el admin)
│   ├── auth/        (Plantillas de inicio de sesion y registro)
│   ├── cart/        (Plantillas del carrito)
│   ├── catalog/     (Plantillas del catalogo)
│   ├── layout/      (Plantillas de layout)
│   ├── order/       (Plantillas de orden)
│   ├── profile/     (Plantillas de perfil)
│   └── provider/    (Plantillas de proveedor)
├── storage/images/  (Almacenamiento de imágenes)
└── .env             (Configuración sensible)


## Configuración de Almacenamiento (Imágenes)
El sistema guarda las imágenes en la carpeta raíz `storage/` para mayor seguridad. Para que estas sean visibles en la web, es necesario crear un enlace simbólico y ajustar permisos:

### Linux / macOS
Ejecuta desde la raíz del proyecto:
```bash
# Crear enlace simbólico
ln -s ../storage public/storage

# Dar permisos de escritura para que el contenedor pueda subir archivos
chmod -R 777 storage
```

### Windows (PowerShell Administrador)
Ejecuta desde la raíz del proyecto:
```powershell
# Crear enlace simbólico
New-Item -ItemType SymbolicLink -Path "public\storage" -Target "..\storage"
```
*Nota: En Windows, asegúrate de que Docker tenga permisos de escritura sobre la carpeta del proyecto.*