# Evidencia de pruebas: autenticación API con Laravel Sanctum

**Proyecto:** Sistema de Pedidos a Domicilio — Laboratorio III  
**Fecha:** 16 de noviembre de 2026  
**Entorno:** Local  
**URL base:** `http://127.0.0.1:8000`  
**Herramienta utilizada:** PowerShell (`Invoke-RestMethod`) y `curl.exe`  
**Autenticación:** Laravel Sanctum mediante tokens Bearer

## 1. Objetivo

Comprobar que la API permite registrar usuarios e iniciar sesión, protege los endpoints privados mediante `auth:sanctum` y revoca correctamente el token al cerrar sesión.

## 2. Rutas verificadas

| Método | Endpoint | Acceso esperado |
|---|---|---|
| POST | `/api/register` | Público |
| POST | `/api/login` | Público |
| POST | `/api/logout` | Requiere token |
| GET | `/api/user` | Requiere token |

La salida de `php artisan route:list --path=api -v` confirmó que `api/logout` y `api/user` tienen el middleware `Illuminate\Auth\Middleware\Authenticate:sanctum`. Las rutas `api/register` y `api/login` no tienen ese middleware.

## 3. Registro de pruebas

| ID | Caso | Procedimiento | Resultado esperado | Resultado observado | Estado |
|---|---|---|---|---|---|
| AUTH-01 | Consultar usuario sin token | `GET /api/user` sin encabezado Authorization | HTTP 401 y respuesta JSON | HTTP 401; `{"message":"Unauthenticated."}` | APROBADO |
| AUTH-02 | Cerrar sesión sin token | `POST /api/logout` sin encabezado Authorization | HTTP 401 | HTTP 401; `{"message":"Unauthenticated."}` | APROBADO |
| AUTH-03 | Registrar usuario | `POST /api/register` con nombre, correo, contraseña y confirmación válidos | HTTP 201 y token | HTTP 201; mensaje `Usuario registrado correctamente.`, datos del usuario y token de acceso devuelto | APROBADO |
| AUTH-04 | Iniciar sesión | `POST /api/login` con correo y contraseña registrados | HTTP 200 y token | HTTP 200; inicio de sesión correcto y token de acceso guardado en variable local de PowerShell | APROBADO |
| AUTH-05 | Consultar usuario con token | `GET /api/user` con `Authorization: Bearer <token>` | HTTP 200 y datos del usuario autenticado | Se devolvieron los datos de `Usuario Prueba` (`prueba.api@example.com`) | APROBADO |
| AUTH-06 | Cerrar sesión con token | `POST /api/logout` con el token válido | HTTP 200 y confirmación | Mensaje `Sesión cerrada correctamente.` | APROBADO |
| AUTH-07 | Reutilizar token revocado | Repetir `GET /api/user` con el mismo token después del logout | HTTP 401 | PowerShell informó HTTP 401 No autorizado | APROBADO |
| AUTH-08 | Inicio de sesión con contraseña incorrecta | POST `/api/login` con correo existente y contraseña incorrecta | HTTP 401 y mensaje JSON genérico | Inicialmente devolvía 422; se corrigió el controlador para responder 401. Se repitió la prueba y fue aprobada | APROBADO |

**Resultado:** 8 pruebas aprobadas de 8 ejecutadas.

## 4. Incidencia durante el envío del registro y solución

En los primeros intentos de registro usando `curl.exe` desde PowerShell, el servidor respondió HTTP 422 e indicó que los campos `name`, `email` y `password` eran obligatorios. El cuerpo JSON no estaba llegando correctamente por la forma en que PowerShell interpretaba las comillas y los argumentos del comando.

Se corrigió el envío construyendo el cuerpo con `ConvertTo-Json -Compress` y enviándolo mediante `Invoke-RestMethod`, con `Content-Type: application/json` y `Accept: application/json`. Con ese procedimiento el registro se completó correctamente.

Esta incidencia fue un problema en la construcción de la solicitud de prueba; no se modificó el controlador para resolverla.

## 5. Conclusión

Las pruebas confirman que:

- Las rutas públicas de registro e inicio de sesión están disponibles sin token.
- Las rutas privadas rechazan solicitudes no autenticadas con HTTP 401.
- Un token válido permite consultar los datos del usuario autenticado.
- El cierre de sesión elimina el token de acceso actual.
- El token revocado deja de permitir el acceso a las rutas protegidas.

Por tanto, el flujo básico de autenticación y autorización con Laravel Sanctum funciona en el entorno local probado.

## 6. Consideraciones de seguridad y alcance

- No se incluye ningún token real en este documento.
- Las credenciales utilizadas corresponden a un usuario de prueba local.
- No se debe subir `.env`, contraseñas ni tokens al repositorio.
- Las pruebas de CRUD, validaciones y aislamiento de pedidos se documentan en `evidencia_pruebas_crud_api.md`.
- El registro general consolida 28 pruebas manuales aprobadas entre autenticación, CRUD, validaciones y seguridad.

## 7. Verificación complementaria en instalación limpia (17 de noviembre de 2026)

Se realizó una instalación independiente del proyecto en la carpeta `prueba-lab3-aaron`, utilizando una base de datos MySQL separada de la instalación original.

Se verificó el funcionamiento de Laravel Sanctum mediante dos cuentas de prueba:

- Se registró e inició sesión con el primer usuario (`Aaron Prueba`).
- Se consultó `/api/user` utilizando su token y se recibieron sus datos correctamente.
- Se registró e inició sesión con un segundo usuario (`Segundo Usuario`).
- Se consultó `/api/usuarios` con los tokens de ambas cuentas y se comprobó que cada respuesta correspondía exclusivamente al usuario autenticado.
- Se confirmó que el segundo usuario conservaba su propio pedido y que sus datos no se mezclaban con los del primer usuario.

También se verificó el cierre de sesión y la revocación del token del segundo usuario: después del cierre de sesión, las solicitudes protegidas con ese token devolvieron HTTP 401.

**Resultado:** autenticación, autorización, aislamiento de información y revocación de tokens verificados en la copia independiente del proyecto.




