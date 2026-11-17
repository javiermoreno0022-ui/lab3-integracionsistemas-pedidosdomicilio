# Registro general de pruebas — Laboratorio III

**Proyecto:** Sistema de Pedidos a Domicilio  
**Fecha:** 16 de noviembre de 2026  
**Entorno:** Local  
**URL base:** `http://127.0.0.1:8000`  
**Herramientas:** PowerShell (`Invoke-RestMethod`), `curl.exe`, Laravel Sanctum y REST Client.

## 1. Objetivo

Consolidar los resultados de las pruebas manuales realizadas sobre la API REST, incluyendo autenticación, operaciones CRUD, validaciones, persistencia y aislamiento de información entre usuarios.

## 2. Resumen de resultados

| Área evaluada | Casos ejecutados | Aprobados | Fallidos |
|---|---:|---:|---:|
| Autenticación y Sanctum | 8 | 8 | 0 |
| CRUD, validaciones y seguridad | 20 | 20 | 0 |
| **Total** | **28** | **28** | **0** |

## 3. Distribución de los casos

### 3.1. Autenticación y Sanctum

| Identificadores | Casos evaluados |
|---|---|
| AUTH-01 y AUTH-02 | Acceso a rutas protegidas sin token |
| AUTH-03 y AUTH-04 | Registro e inicio de sesión |
| AUTH-05 | Consulta del usuario autenticado |
| AUTH-06 y AUTH-07 | Cierre de sesión y revocación del token |
| AUTH-08 | Inicio de sesión con contraseña incorrecta |

**Resultado:** 8 de 8 casos aprobados.

### 3.2. CRUD, validaciones y seguridad

| Identificadores | Casos evaluados |
|---|---|
| CRUD-01 a CRUD-07 | Listar, crear, consultar, actualizar y eliminar pedidos |
| VAL-01 a VAL-03 | Estado inválido, cantidad cero y total negativo |
| SEG-01 y SEG-02 | Acceso a pedidos ajenos y aislamiento del listado |
| PATCH-01 y PATCH-02 | Actualización parcial y comprobación de persistencia |
| PUT-01 | Rechazo de actualización PUT incompleta |
| USU-01 | Consulta del usuario autenticado y sus pedidos |
| VAL-04 a VAL-07 | Límites máximos de total y costo express en creación y actualización |

**Resultado:** 20 de 20 casos aprobados.

## 4. Incidencias y correcciones

Durante el desarrollo, la integración y las pruebas se identificaron las siguientes incidencias:

| N.º | Situación identificada | Acción realizada | Verificación |
|---|---|---|---|
| 1 | PATCH exigía todos los campos del pedido, impidiendo realizar actualizaciones parciales | Se diferenciaron las reglas de PUT y PATCH mediante `required` y `sometimes` en `UpdatePedidoRequest` | PATCH parcial aprobado con HTTP 200 y PUT incompleto rechazado con HTTP 422 |
| 2 | Los campos numéricos aceptaban valores superiores al límite de DECIMAL(10,2), lo que podía provocar errores al guardar en la base de datos | Se agregó `max:99999999.99` en los Form Requests de creación y actualización | Cuatro pruebas con valores superiores devolvieron HTTP 422 |
| 3 | Un conflicto de ubicación y namespace del controlador API provocó un problema de carga de clases que impedía registrar correctamente las rutas | Se restauró el controlador web y se ubicó el controlador API en `app/Http/Controllers/Api/` con su namespace correspondiente | `composer dump-autoload` finalizó sin advertencias PSR-4 y `php artisan route:list --path=api -v` mostró las 10 rutas esperadas |
| 4 | Un archivo Form Request estaba guardado con BOM UTF-8 | Se guardó nuevamente en UTF-8 sin BOM | `php -l` confirmó que la sintaxis era correcta |
| 5 | El inicio de sesión con contraseña incorrecta devolvía HTTP 422 en lugar del HTTP 401 establecido para credenciales incorrectas | Se ajustó la respuesta del controlador de autenticación | Se repitió AUTH-08 y devolvió HTTP 401 |

La incidencia de codificación fue un problema técnico del archivo y no un defecto funcional de las reglas de validación. La incidencia de envío JSON durante el registro fue un problema de construcción de la solicitud desde PowerShell, no de la API.

Las tres primeras incidencias principales corresponden a problemas de validación e integración que fueron corregidos y comprobados. Las demás se documentan como situaciones técnicas o ajustes adicionales.

## 5. Verificaciones complementarias

Además de las pruebas manuales documentadas:

- `php artisan route:list --path=api -v` permitió comprobar las rutas y sus middleware.
- `php artisan test` ejecutó las dos pruebas de ejemplo incluidas por Laravel, ambas aprobadas.
- `php -l` comprobó la sintaxis de los archivos PHP modificados.
- `composer dump-autoload` finalizó sin advertencias PSR-4.
- Se comprobó que las rutas de la API devuelven respuestas JSON ante errores.

**Alcance:** Los 28 casos corresponden a pruebas manuales de API documentadas. Las dos pruebas de `php artisan test` son pruebas automatizadas independientes; no se contabilizan dentro de esos 28 casos.

### 5.1. Verificación de transacciones de base de datos

Se comprobó el comportamiento de `DB::transaction()` utilizado durante el registro de usuarios en `app/Http/Controllers/Api/AuthController.php`.

**Procedimiento realizado:**

1. Se inició una transacción de base de datos.
2. Se insertó un usuario temporal con el correo `rollback.lab3@example.com`.
3. Se provocó intencionalmente una excepción dentro de la transacción.
4. Se consultó la existencia del usuario después de capturar la excepción.

**Resultado obtenido:**

- Excepción controlada: `Error intencional para comprobar rollback`.
- Resultado de la consulta de existencia: `false`.
- El usuario temporal no quedó almacenado.

**Conclusión:** Se comprobó que Laravel revierte los cambios realizados dentro de `DB::transaction()` cuando ocurre una excepción, evitando que se conserve una operación incompleta.

## 6. Documentos de evidencia

- `../evidencia_pruebas_autenticacion_sanctum.md`: detalle de los ocho casos de autenticación.
- `../evidencia_pruebas_crud_api.md`: detalle de los veinte casos de CRUD, validaciones y seguridad.
- `../../pruebas/pedidos_api.http`: colección de solicitudes HTTP para REST Client.

## 7. Conclusión

Se documentaron 28 casos de prueba manuales: ocho de autenticación y veinte de CRUD, validaciones y seguridad. Todos finalizaron con resultado aprobado.

Las pruebas permitieron verificar el funcionamiento de la API REST, la protección mediante Sanctum, la validación de datos, las actualizaciones PUT y PATCH, la persistencia y el aislamiento de pedidos por usuario.

Durante el desarrollo se identificaron y corrigieron tres problemas principales relacionados con las actualizaciones parciales, los límites de valores monetarios y la integración de los controladores. También se atendieron incidencias técnicas y ajustes en las respuestas HTTP. Las correcciones fueron comprobadas mediante verificaciones y pruebas posteriores.

