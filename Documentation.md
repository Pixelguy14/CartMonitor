# CartMonitor: A Pure PHP Layered E-commerce Engine

## Documentacion:
Estructura Modular sin Frameworks implementado en un docker para reproductibilidad y escalabilidad.

Decidi usar docker para mejorar mi practica en el uso de contenedores y orquestadores y por que instalar mysql php etc. es una molestia tanto en linux como en windows, el docker simplifica ese proceso bastante. 

### Enfoque AGILE

Sprint 1: El Esqueleto (22 al 24 de febrero)
    Backend: Configuración de Docker y creación del Front Controller en public/index.php.
    Backend: Implementación del Router manual que maneje parámetros dinámicos (ej: /producto/{id}).
    DB: Creación del esquema con integridad referencial (tablas products, users, cart_items) .
    Criterio de Aceptación: El servidor responde y el router dirige a un controlador de prueba.

Sprint 2: Lógica y Capas (25 al 27 de febrero)
    Capa de Datos: Crear el PDO Singleton y los Repositories con Prepared Statements .
    Capa de Negocio: Implementar Services para calcular subtotales y validar stock .
    Capa de Control: El Controller debe validar datos antes de enviarlos al Service .
    Criterio de Aceptación: Se pueden listar productos desde la DB y agregar al carrito en sesión.

Sprint 3: Frontend y Blindaje (28 al 4 de marzo)
    Frontend: Interfaz responsive con Bootstrap 5, Cards de productos y Navbar .
    Seguridad: Implementar CSRF Tokens en formularios y Escape de HTML en la salida de datos.
    Criterio de Aceptación: El sitio es navegable en móviles y es resistente a ataques básicos (XSS/SQLi).


Diagrama de Flujo de Datos (Arquitectura):

![alt text](image.png)
Este diagrama muestra el camino de una petición: Request -> Router -> Controller -> Service -> Repository -> DB.

Diagrama de Flujo de Usuario:
![alt text](image.png)
Este diagrama representa el proceso de compra: Ver Producto -> Agregar al Carrito -> Validar Stock -> Actualizar Subtotal.


## Instalaciones

Tecnologías a instalar dentro de Docker
PHP 8+ con extensiones: pdo, pdo_mysql, mysqli, mbstring, openssl.
MySQL como servicio separado (contenedor independiente).

Nginx como servidor web. 
y docker compose para orquestar todo.


dbdiagram.io fue usado para generar el diagrama entidad relacion de la base de datos.
![alt text](image.png)