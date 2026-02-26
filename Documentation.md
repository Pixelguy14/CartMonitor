# CartMonitor: A Pure PHP Layered E-commerce Engine

## Documentacion:
Estructura Modular sin Frameworks implementado en un docker para reproductibilidad y escalabilidad.

En este proyecto se usó docker para mejorar mi practica en el uso de contenedores y orquestadores, y por que instalar mysql php etc. es una molestia tanto en linux como en windows, el docker simplifica ese proceso bastante. 


Definiciones importantes:

CSRF: Vulnerabilidad de seguridad donde un atacante engaña a un usuario autenticado para que realice acciones no deseadas en una aplicación web en la que confía. Lo corregimos con validateCsrf() en los controllers

Singleton PHP: Es un patrón de diseño creacional que garantiza que una clase tenga una única instancia en todo el ciclo de vida de la solicitud y proporciona un punto de acceso global a ella. Es lo primero que se instancio en el codigo y se define en app/Core.

PDO PHP: (PHP Data Objects) Es una capa de abstracción de acceso a datos, es parte de PHP nativo y se uso en Core y en los repositories.

Arquitectura profesional basada en capas: Es un estilo arquitectónico donde las responsabilidades se dividen en niveles horizontales, controllers, services y repositories.

Arquitectura MVC:(Model-View-Controller) Patrón de arquitectura de software que separa la aplicación en tres componentes: Modelo, Vista y Controlador. Modelo es la capa de datos, Vista es la capa de presentacion y Controlador es la capa de logica de negocio.

Logica de negocio: rutinas y algoritmos que codifican las reglas de la aplicación del mundo real dentro del software. (Ejemplo, que un proovedor no pueda vender un producto a más de 30,000)

Logica empresarial: las reglas de alto nivel que rigen a toda la organización y sus flujos de trabajo transversales. define quien tiene permiso para ejecutar cual accion, separado en mi sistema en usuario, proveedor y administrador.

Prepared Statements PHP: característica de seguridad y rendimiento utilizada para ejecutar la misma sentencia SQL repetidamente con alta eficiencia y protección contra Inyección SQL. Lo usamos en las sentencias de SQL en repositories.



### Enfoque AGILE

Sprint 1: El Esqueleto (22 de febrero)
    Backend: Configuración de Docker y creación del Front Controller en public/index.php.
    Backend: Implementación del Router manual que maneje parámetros dinámicos (ej: /producto/{id}).
    DB: Creación del esquema con integridad referencial (tablas products, users, cart_items) .
    Criterio de Aceptación: El servidor responde y el router dirige a un controlador de prueba.

Sprint 2: Lógica y Capas (23 de febrero)
    Capa de Datos: Crear el PDO Singleton y los Repositories con Prepared Statements .
    Capa de Negocio: Implementar Services para calcular subtotales y validar stock .
    Capa de Control: El Controller debe validar datos antes de enviarlos al Service .
    Criterio de Aceptación: Se pueden listar productos desde la DB y agregar al carrito en sesión.

Sprint 3: Frontend y Blindaje (23 de marzo)
    Frontend: Interfaz responsive con Bootstrap 5, Cards de productos y Navbar .
    Seguridad: Implementar CSRF Tokens en formularios y Escape de HTML en la salida de datos.
    Criterio de Aceptación: El sitio es navegable en móviles y es resistente a ataques básicos (XSS/SQLi).

Sprint 4: Ruteo Avanzado y Administración (24 de marzo)
    Backend: Refactorización del Router para soportar grupos y Middlewares (estilo Gin/Go).
    Seguridad: Implementación de AdminMiddleware para proteger rutas críticas.
    Admin: Panel para edición de roles y gestión de usuarios.
    Criterio de Aceptación: Solo administradores pueden cambiar roles; las rutas están protegidas por capas.

Sprint 5: Panel de Proveedor y Transacciones (24 de marzo)
    Backend: Lógica de inventario para proveedores y carga de imágenes.
    Seguridad: Checkout transaccional con `SELECT FOR UPDATE` para evitar overselling.
    UX: Historial de órdenes detallado con snapshots de precios al momento de compra.
    Criterio de Aceptación: El stock se deduce correctamente en condiciones de carrera y el proveedor gestiona sus activos.

Sprint 6: Technical Maximalism & Refinado Final (25 de marzo)
    Frontend: Sistema de diseño propio que utiliza CSS personalizado con paletas Cobalt, Sun y Lime.
    Optimizacion: Uso de modales y expansión inline en el catálogo para evitar recargas de paginas.
    Criterio de Aceptación: La interfaz es intuitiva, rápida y atractiva.

Sprint 7: Corrección de errores y mejoras de seguridad (26 de marzo)
    Backend: Corrección de errores en la estructura clean.
    Frontend: Corrección de errores en formularios.
    Criterio de Aceptación: Correr el código desde 0 y sin errores.


Diagrama de Caso de Uso:
![Diagrama de Caso de Uso](/public/images/Diagrama_caso_uso.png)
Define el alcance funcional del sistema. Identifica quiénes interactúan con la plataforma (Actores) y qué acciones principales pueden realizar.
Identificamos los actores como el usuario, el proveedor y el administrador, establecimos lo que puede acceder cada uno y mapeamos las necesidades de cada usuario.

Diagrama de Flujo del sistema inicial:
![Diagrama de Flujo](/public/images/Vista_inicial.png)
Muestran la experiencia del usuario (UX) y las decisiones lógicas paso a paso. Son el puente entre la idea y el algoritmo.

Diagrama de Flujo del registro o inicio de sesion:
![Diagrama de Flujo](/public/images/Registro_inicio_sesion.png)
Determinan cuándo el sistema debe interrumpir al usuario para pedirle credenciales antes de realizar una acción crítica (como añadir al carrito).

Diagrama de Flujo de la vista del carrito:
![Diagrama de Flujo](/public/images/Vista_carrito.png)

Diagrama de Flujo de la busqueda y filtrado de productos:
![Diagrama de Flujo](/public/images/Busqueda_y_filtrado.png)
Tanto el de la vista del carrito como el de busqueda Definen estados vacíos (Empty States), validaciones de stock y recálculos de subtotales para que el Frontend sepa qué mostrar en cada escenario.

Diagrama de Secuencia del proceso de checkout:
![Diagrama de Secuencia](/public/images/Proceso_checkout.png)
Se centran en el cómo interactúan los componentes internos del software (Controllers, Services, Repositories) a través del tiempo.
Mostramos cómo se bloquea el stock (SELECT FOR UPDATE) y cómo se confirma el pago solo si la base de datos está lista.

Diagrama de Secuencia de la validación de sesion y middleware:
![Diagrama de Secuencia](/public/images/Validacion_sesion.png)
Explica la capa de seguridad que intercepta peticiones para validar el token_hash en la tabla user_sessions antes de permitir el acceso al controlador.

Añadimos de casos de uso y de secuencia para tener una mejor idea de como funciona el sistema y como interactuan los componentes internos del software.

### Filosofía "Zero-Loopholes" (Seguridad)

Para este proyecto la seguridad fue el centro, no algo secundario:
1.  **Inyección SQL Prohibida**: Al usar Repositories con PDO y Prepared Statements, es imposible meter código SQL por los inputs.
2.  **Protección CSRF**: Cada formulario tiene un token que expira, evitando que alguien engañe al usuario para hacer acciones sin querer.
3.  **Salida Limpia (XSS)**: Todo lo que viene de la base de datos pasa por un escape de HTML antes de mostrarse. "Zero Raw Data" en la vista.
4.  **Transacciones Reales**: El checkout usa transacciones de base de datos. Si dos personas compran el último producto al mismo tiempo, el sistema bloquea una hasta que la otra termine, evitando ventas duplicadas (problema de concurrencia).
5.  **Validación de Sesión y Middleware**: Explica la capa de seguridad que intercepta peticiones para validar el token_hash en la tabla user_sessions antes de permitir el acceso al controlador.
6.  **Validación de Roles**: Explica la capa de seguridad que intercepta peticiones para validar el rol del usuario antes de permitir el acceso al controlador correspondiente. de la mano con el middleware.

## Instalaciones

Tecnologías a instalar dentro de Docker: (Solo valido en Sprint 1)
PHP 8+ con extensiones: pdo, pdo_mysql, mysqli, mbstring, openssl.
MySQL como servicio separado (contenedor independiente).

Nginx como servidor web. (Se uso apache)
y docker compose para orquestar todo. 


dbdiagram.io fue usado para generar el diagrama entidad relacion de la base de datos.
![Diagrama de Entidad Relacion](/public/images/Diagrama_entidad_relacion.png)

¿Cómo insertamos datos de prueba al docker? (Solo relevante en Sprint 1-2)
```bash
docker exec -i cartmonitor_db mysql -u CartMonitor_user -pCartMonitor_password CartMonitor <<EOF
INSERT INTO users (username, email, type, password_hash) 
VALUES ('proveedor1', 'pro@test.com', 'proveedor', 'hash');

INSERT INTO products (name, description, price, provider_id, stock_quantity) 
VALUES ('Producto de Prueba', 'Una descripción corta', 99.99, 1, 50);
EOF
```

Una vez creado el usuario test test@test.com 1234, ocupamos hacerlo admin, lo haremos mediante una inyeccion SQL para ascenderlo a admin y pueda ascender a usuarios a proveedores.
 (Solo relevante en Sprint 3-5 pero el comando se puede seguir ejecutando)
```bash
docker exec -i cartmonitor_db mysql -u CartMonitor_user -pCartMonitor_password CartMonitor <<EOF
UPDATE users SET type = 'admin' WHERE email = 'test@test.com';
EOF
```
Si lo hiciste con la sesion iniciada, simplemente haz logout y inicia de nuevo para ver los privilegios de admin


Orden de la arquitectura:
Routes -> Controllers -> Services -> Repositories -> Database

Este desarrollo no ocupo ningun framework. Todo funciona con un **Autoloader PSR-4 manual** que se encuentra en app/Core/Autoloader.php y el patrón **Singleton** que se encuentra en app/Core/Database.php y el resto de la carpeta Core, con el fin de que la conexión a la base de datos sea eficiente y no se duplique en cada rincón del código.

Orden de creacion:
Database -> Repositories -> Services -> Controllers -> Routes -> Views -> Correcciones

Los diagramas de flujo y de secuencia nos ayudan a entender el funcionamiento interno del software y como interactuan los componentes internos del software.

En la arquitectura solicitada, los controles solo manejan peticiones y respuestas, dicen que recibe del frontend, validan campos vacios y los envian a las capas inferiores. sin conocer a ciencia cierta la logica detras de los valores. tambien validan el CSRF.

Los servicios manejan la logica de negocio. En esta capa veremos las peticiones a la base de datos, validaciones de las variables enviadas, y manejo de errores.

Los repositorios manejan la logica de base de datos. En esta capa solo vamos a ver las solicitudes hacia la base de datos.

Los elementos más complicados de trabajar fueron además de los diagramas, el flujo de trabajo de un proovedor, agregar imagenes, editar, y que el frontend se vea bien en todos los dispositivos.
Segmentar esa logica en capas fue un desafio por que al hacerlo todo en el mismo archivo no comprendia bien que parte iba en cada capa (ejemplo en el update, la logica de la imagen como reestructurarla.)

De ahi en más, admin, profile y auth eran bastante similares y otros eran más simples, como order o product.