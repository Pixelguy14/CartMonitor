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

## Instalaciones

Tecnologías a instalar dentro de Docker
PHP 8+ con extensiones: pdo, pdo_mysql, mysqli, mbstring, openssl.
MySQL como servicio separado (contenedor independiente).

Nginx como servidor web. 
y docker compose para orquestar todo.


dbdiagram.io fue usado para generar el diagrama entidad relacion de la base de datos.
![Diagrama de Entidad Relacion](/public/images/Diagrama_entidad_relacion.png)

// que lleva la navbar? 
// debe llevar: index/explicacion, catalogo (main), login/register/profile, cart, checkout/pago, logout

// Vista de detalle de producto en UI solo aumenta el tamaño del div, la imagen, y muestra la descripcion, la cantidad del stock, y cuanto fue creado. El precio y el nombre se ven desde la vista de catalogo (Dejar diseño front al final)

// No olvidar Front Controller architecture (Front Controller en public/index.php), router manual y MCV parcial con capas adicionales

// no es mejor cambiar el docker a nginx? 

