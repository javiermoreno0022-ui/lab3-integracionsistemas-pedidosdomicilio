# Sistema de Pedidos a Domicilio — Laboratorio III

API REST protegida para la gestión de pedidos a domicilio, desarrollada con Laravel 13, PHP y MySQL, como continuación del proyecto de Integración de Sistemas realizado en los laboratorios I y II.

Esta etapa incorpora autenticación mediante Laravel Sanctum, operaciones CRUD mediante endpoints REST, respuestas JSON con API Resources, validaciones con Form Requests, aislamiento de información por usuario y una capa de servicios para mejorar la mantenibilidad.

## 1. Descripción del proyecto

El Sistema de Pedidos a Domicilio permite registrar, consultar, actualizar y eliminar pedidos.

La aplicación cuenta con dos modalidades:

- **Aplicación web:** gestión de pedidos mediante vistas Blade y autenticación por sesiones.
- **API REST:** gestión de pedidos mediante solicitudes HTTP y respuestas JSON, protegida mediante tokens de Laravel Sanctum.

Cada usuario autenticado puede administrar únicamente sus propios pedidos. La API utiliza las relaciones entre usuarios y pedidos para evitar el acceso no autorizado a información de otras cuentas.

## 2. Integrantes del equipo

| Carné | Integrante |
|---|---|
| MN-64016-23 | Francisco Javier Moreno Navas |
| PG-64792-23 | Alexander Maximiliano Pérez García |
| CL-64224-24 | Aaron Steven Cabrera López |
| CD-64257-23 | Denys Ezequiel Córdova Domínguez |

### Coevaluación

| Integrante | Porcentaje |
|---|---:|
| Francisco Javier Moreno Navas | 90% |
| Alexander Maximiliano Pérez García | 80% |
| Aaron Steven Cabrera López | 90% |
| Denys Ezequiel Córdova Domínguez | 80% |

Los porcentajes representan la evaluación de participación individual acordada por el equipo.

### Responsables de defensa — Laboratorio III

| Bloque | Responsable |
|---|---|
| A. API REST y JSON | Francisco Javier Moreno Navas |
| B. Autenticación con Sanctum | Aaron Steven Cabrera López |
| C. Pruebas y manejo de errores | Alexander Maximiliano Pérez García |
| D. Uso crítico de IA y mantenimiento | Denys Ezequiel Córdova Domínguez |

## 3. Tecnologías utilizadas

- PHP 8.4.23
- Laravel 13
- Composer 2.10.2
- Laravel Sanctum 4.4.0
- MySQL
- Eloquent ORM
- Blade
- HTML5, CSS y JavaScript
- Git y GitHub
- REST Client para Visual Studio Code

## 4. Funcionalidades de la API REST

### Autenticación

La API implementa autenticación mediante Laravel Sanctum:

- Registro de usuarios.
- Inicio de sesión.
- Generación de tokens personales.
- Consulta del usuario autenticado.
- Cierre de sesión.
- Revocación del token utilizado.

Las rutas privadas requieren un token Bearer válido en la cabecera `Authorization`.

### Gestión de pedidos

El usuario autenticado puede:

- Listar sus propios pedidos.
- Registrar nuevos pedidos.
- Consultar un pedido por su identificador.
- Actualizar un pedido completamente mediante PUT.
- Actualizar parcialmente un pedido mediante PATCH.
- Eliminar pedidos.

### Validaciones

Se utilizan Form Requests para validar los datos recibidos por la API.

Entre las reglas implementadas se encuentran:

- Campos obligatorios.
- Tipos de datos.
- Longitud máxima de texto.
- Cantidad mínima de productos.
- Valores numéricos y monetarios no negativos.
- Estados permitidos.
- Fechas válidas.
- Validación diferenciada para PUT y PATCH.

### Respuestas JSON

Los API Resources permiten controlar la estructura de los datos devueltos por los endpoints.

Se utilizan códigos HTTP según el resultado de las operaciones:

| Código | Descripción |
|---|---|
| 200 | Solicitud procesada correctamente |
| 201 | Recurso creado correctamente |
| 204 | Operación completada sin contenido de respuesta |
| 401 | Usuario no autenticado |
| 404 | Recurso no encontrado o no accesible |
| 422 | Error de validación |

## 5. Endpoints de la API

La URL base de desarrollo es:

`http://127.0.0.1:8000/api`

| Método | Ruta | ¿Protegida? | Descripción |
|---|---|---|---|
| POST | `/register` | No | Registrar usuario |
| POST | `/login` | No | Iniciar sesión y generar token |
| POST | `/logout` | Sí | Revocar el token actual |
| GET | `/user` | Sí | Consultar usuario autenticado |
| GET | `/pedidos` | Sí | Listar pedidos propios |
| POST | `/pedidos` | Sí | Crear pedido |
| GET | `/pedidos/{pedido}` | Sí | Consultar pedido |
| PUT | `/pedidos/{pedido}` | Sí | Actualizar pedido completo |
| PATCH | `/pedidos/{pedido}` | Sí | Actualizar pedido parcialmente |
| DELETE | `/pedidos/{pedido}` | Sí | Eliminar pedido |
| GET | `/usuarios` | Sí | Consultar usuario autenticado y sus pedidos |

Las rutas protegidas utilizan el middleware `auth:sanctum`.

## 6. Seguridad y aislamiento de información

La API utiliza Laravel Sanctum para autenticar las solicitudes mediante tokens personales.

Para acceder a una ruta protegida se debe enviar:

```http
Authorization: Bearer {token}
```

El token no debe enviarse como parámetro de la URL ni almacenarse en el repositorio.

El aislamiento de pedidos se realiza mediante las relaciones del usuario autenticado:

```php
$request->user()->pedidos()
```

De esta manera, las consultas, actualizaciones y eliminaciones se limitan a los pedidos del propietario.

Si un usuario intenta consultar un pedido perteneciente a otra cuenta, la API devuelve HTTP 404.

También se implementan:

- Validación de datos de entrada.
- Protección de rutas mediante Sanctum.
- Contraseñas almacenadas mediante hashing.
- Revocación de tokens al cerrar sesión.
- Respuestas JSON para errores de las rutas API.
- Transacción de base de datos durante el registro de usuarios y creación de su token.

## 7. Arquitectura y organización del código

El proyecto utiliza controladores, modelos, Form Requests, API Resources y una capa de servicios.

### Componentes principales

| Componente | Responsabilidad |
|---|---|
| `Api/AuthController.php` | Registro, inicio y cierre de sesión |
| `Api/PedidoController.php` | Recibir solicitudes HTTP de pedidos |
| `Api/UsuarioController.php` | Operaciones del recurso de usuarios |
| `PedidoService.php` | Centralizar la lógica de negocio de pedidos |
| `StorePedidoRequest.php` | Validar la creación de pedidos |
| `UpdatePedidoRequest.php` | Validar actualizaciones PUT y PATCH |
| `PedidoResource.php` | Estructurar respuestas JSON de pedidos |
| `UsuarioResource.php` | Estructurar respuestas JSON de usuarios |

### Capa de servicios

Se incorporó `app/Services/PedidoService.php` para separar la lógica de negocio de los controladores.

El servicio implementa las operaciones:

- `listar`
- `crear`
- `mostrar`
- `actualizar`
- `eliminar`

El controlador utiliza inyección de dependencias para acceder al servicio.

Esta separación facilita el mantenimiento y evita concentrar todas las operaciones en un único controlador.

## 8. Base de datos y relaciones

El proyecto utiliza MySQL y Eloquent ORM.

La relación principal es de uno a muchos:

```text
User (1) ───────── (N) Pedido
```

Cada pedido contiene un `user_id` que identifica al propietario.

La tabla `pedidos` incluye los siguientes campos:

| Campo | Descripción |
|---|---|
| `id` | Identificador del pedido |
| `user_id` | Usuario propietario |
| `cliente` | Nombre del cliente |
| `telefono` | Teléfono de contacto |
| `direccion` | Dirección de entrega |
| `producto` | Producto solicitado |
| `cantidad` | Cantidad solicitada |
| `total` | Total del pedido |
| `estado` | Estado actual |
| `costo_express` | Costo adicional express |
| `fecha_pedido` | Fecha del pedido |
| `created_at` | Fecha de creación |
| `updated_at` | Fecha de actualización |

## 9. Instalación y configuración

### Requisitos

- PHP 8.4 o versión compatible con el proyecto.
- Composer.
- MySQL.
- Node.js y npm.
- Git.

### Paso 1. Clonar el repositorio

```bash
git clone https://github.com/javiermoreno0022-ui/lab3-integracionsistemas-pedidosdomicilio.git
```

Ingresar al directorio:

```bash
cd lab3-integracionsistemas-pedidosdomicilio
```

### Paso 2. Instalar dependencias

```bash
composer install
npm install
npm run build
```

### Paso 3. Configurar el archivo `.env`

En Windows:

```powershell
copy .env.example .env
```

En Linux/macOS:

```bash
cp .env.example .env
```

Configurar las credenciales de MySQL en el archivo `.env`.

Ejemplo:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pedidos_domicilio
DB_USERNAME=root
DB_PASSWORD=
```

La base de datos debe existir previamente en MySQL.

### Paso 4. Generar la clave de aplicación

```bash
php artisan key:generate
```

### Paso 5. Ejecutar las migraciones

```bash
php artisan migrate
```

### Paso 6. Iniciar el servidor

```bash
php artisan serve
```

La aplicación estará disponible en:

`http://127.0.0.1:8000`

La API estará disponible en:

`http://127.0.0.1:8000/api`

## 10. Pruebas realizadas

Se realizaron pruebas funcionales de autenticación, CRUD, validaciones y seguridad mediante solicitudes HTTP locales.

### Autenticación

- Registro de usuario.
- Inicio de sesión.
- Consulta del usuario autenticado.
- Acceso sin token.
- Cierre de sesión.
- Revocación del token.

### CRUD y validaciones

- Listado de pedidos.
- Creación de pedidos.
- Consulta individual.
- Actualización completa.
- Actualización parcial.
- Eliminación.
- Estados inválidos.
- Cantidades no permitidas.
- Totales negativos.
- PUT incompleto.

### Seguridad

- Acceso a rutas sin autenticación.
- Consulta de pedidos propios.
- Intentos de acceso a pedidos de otro usuario.
- Verificación del aislamiento de información.

### Resultados documentados

Se registraron 28 pruebas manuales aprobadas:

- 8 pruebas de autenticación.
- 20 pruebas de CRUD, validaciones y seguridad.

También se ejecutó `php artisan test`, con 2 pruebas de ejemplo aprobadas.

Los resultados y procedimientos se encuentran en la carpeta `docs/pruebas/`.

## 11. Documentación del laboratorio

La carpeta `docs/` contiene los siguientes documentos:

| Archivo | Contenido |
|---|---|
| `CAMBIOS.md` | Registro de mantenimiento correctivo y evolutivo |
| `FICHA_USO_CRITICO_IA.md` | Casos de uso crítico de Inteligencia Artificial |
| `ANEXO_A_COMPARACION_FRAMEWORKS.md` | Comparación de Laravel y Express.js |
| `evidencia_pruebas_autenticacion_sanctum.md` | Evidencias de autenticación |
| `evidencia_pruebas_crud_api.md` | Evidencias de CRUD, validaciones y seguridad |
| `pruebas/registro_pruebas_lab3.md` | Registro general de pruebas ejecutadas |

## 12. Colección de solicitudes HTTP

En la carpeta `pruebas/` se encuentra:

`pedidos_api.http`

Este archivo contiene solicitudes HTTP para probar:

- Registro e inicio de sesión.
- Consulta y cierre de sesión.
- Operaciones CRUD.
- Validaciones.
- Acceso sin token.
- Intentos de acceso a pedidos ajenos.
- Actualizaciones PUT y PATCH.

Se puede utilizar mediante la extensión REST Client de Visual Studio Code.

Los valores de token e identificadores de pedidos deben configurarse localmente antes de ejecutar las solicitudes.

## 13. Evolución del proyecto

El sistema representa la continuidad del proyecto iniciado en el Laboratorio I.

### Laboratorio I

- PHP nativo.
- Programación Orientada a Objetos.
- Herencia e interfaces.
- Namespaces.
- Composer.
- PDO.
- CRUD.

### Laboratorio II

- Migración a Laravel.
- Arquitectura MVC.
- Blade.
- Eloquent ORM.
- Migraciones.
- Form Requests.
- Autenticación por sesiones.
- Aislamiento de información por usuario.

### Laboratorio III

- API REST.
- Respuestas JSON.
- API Resources.
- Laravel Sanctum.
- Tokens de autenticación.
- Protección de endpoints.
- Pruebas de API.
- Manejo de errores.
- Capa de servicios.
- Mantenimiento correctivo y evolutivo.
- Uso crítico de Inteligencia Artificial.

## 14. Uso de Inteligencia Artificial

Durante el desarrollo se utilizaron herramientas de Inteligencia Artificial como apoyo para revisar código, proponer soluciones, identificar errores y mejorar la documentación.

Las propuestas fueron revisadas, adaptadas y verificadas mediante pruebas locales.

La ficha de uso crítico documenta dos casos, los problemas identificados, las correcciones aplicadas y las justificaciones técnicas.

No se deben compartir tokens reales, contraseñas ni contenido sensible del archivo `.env` con herramientas de IA.

## 15. Control de versiones

El proyecto utiliza Git y GitHub para conservar el historial de desarrollo y registrar los cambios realizados durante los laboratorios.

**Repositorio:**

https://github.com/javiermoreno0022-ui/lab3-integracionsistemas-pedidosdomicilio/tree/lab3

El archivo `.env`, las dependencias instaladas y los datos sensibles no deben incluirse en el repositorio.

**Tablero Kanban:**

El seguimiento y la organización de las actividades del Laboratorio III se realizaron mediante GitHub Projects.

**Enlace al tablero:**
https://github.com/users/javiermoreno0022-ui/projects/7

## 16. Licencia

Proyecto académico desarrollado para la asignatura Integración de Sistemas.

No está destinado a uso comercial.
