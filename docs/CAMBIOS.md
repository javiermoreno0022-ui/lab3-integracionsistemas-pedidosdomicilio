# Registro de mantenimiento — Laboratorio III

**Proyecto:** Sistema de Pedidos a Domicilio  
**Fecha:** 16 de noviembre de 2026

## 1. Mantenimiento correctivo: actualización parcial de pedidos

**Solicitud:** Corregir el comportamiento del endpoint de actualización para permitir modificar únicamente los campos enviados mediante PATCH.

**Impacto identificado:** La implementación anterior exigía todos los atributos del pedido, por lo que una actualización parcial era rechazada con HTTP 422.

**Cambio realizado:**
- Se modificó `app/Http/Requests/Api/UpdatePedidoRequest.php`.
- Se incorporó una regla condicional mediante `$this->isMethod('PUT')`.
- PUT exige todos los campos.
- PATCH valida únicamente los campos enviados.

**Pruebas ejecutadas:**
- PATCH enviando únicamente `estado`: HTTP 200.
- PUT enviando únicamente `estado`: HTTP 422.
- Consulta posterior para verificar que el PUT rechazado no modificó los datos: HTTP 200 y estado anterior conservado.

**Resultado:** Mantenimiento correctivo implementado y verificado.

## 2. Mantenimiento evolutivo: incorporación de una capa de servicios

**Solicitud:** Mejorar la organización y mantenibilidad de la API separando la lógica de negocio del controlador de pedidos.

**Impacto identificado:** El controlador API concentraba las operaciones de consulta, creación, actualización y eliminación. Esto dificultaba separar responsabilidades y mantener la lógica.

**Cambio realizado:**
- Se creó `app/Services/PedidoService.php`.
- Se trasladaron al servicio las operaciones `listar`, `crear`, `mostrar`, `actualizar` y `eliminar`.
- Se modificó `app/Http/Controllers/Api/PedidoController.php` para utilizar inyección de dependencias.
- Se mantuvieron las respuestas JSON y los códigos HTTP de los endpoints.

**Pruebas ejecutadas después del cambio:**
- Listado de pedidos.
- Consulta individual.
- Actualización completa.
- Actualización parcial.
- Eliminación.
- Verificación de acceso restringido a pedidos propios.

**Resultado:** Mantenimiento evolutivo implementado. Las operaciones CRUD y el aislamiento de usuarios fueron comprobados nuevamente.

## 3. Mantenimiento correctivo: validación de límites numéricos

**Problema identificado:** Los campos `total` y `costo_express` aceptaban valores superiores al límite permitido por las columnas `DECIMAL(10,2)` de MySQL.

**Impacto identificado:** Al intentar guardar un valor de `100000000.00`, la base de datos generaba un error HTTP 500 por desbordamiento numérico (SQLSTATE 22003).

**Cambio realizado:**
- Se modificó `app/Http/Requests/Api/StorePedidoRequest.php`.
- Se modificó `app/Http/Requests/Api/UpdatePedidoRequest.php`.
- Se incorporó la regla `max:99999999.99` para los campos `total` y `costo_express`.
- Se conservaron las reglas diferenciadas para PUT y PATCH.

**Pruebas ejecutadas:**

| Campo | Operación | Resultado |
|---|---|---|
| `total` | POST | HTTP 422 |
| `total` | PATCH | HTTP 422 |
| `costo_express` | POST | HTTP 422 |
| `costo_express` | PATCH | HTTP 422 |

También se verificó que Laravel reconoce las reglas actualizadas mediante Tinker y que ambos Form Requests no presentan errores de sintaxis.

**Resultado:** Mantenimiento correctivo implementado y verificado. Los valores fuera del rango permitido son rechazados mediante validación HTTP 422 antes de llegar a la base de datos.

## 4. Verificación técnica complementaria

Se ejecutaron las siguientes comprobaciones:

| Comando | Resultado |
|---|---|
| `composer dump-autoload` | Correcto, sin advertencias PSR-4 |
| `php artisan route:list --path=api -v` | 10 rutas registradas |
| `php artisan test` | 2 pruebas aprobadas |
| `php -l` en controladores API | Sin errores de sintaxis |
| `php -l` en `PedidoService` | Sin errores de sintaxis |
| `php -l` en recursos JSON | Sin errores de sintaxis |
| `php -l` en Form Requests API | Sin errores de sintaxis |
| Prueba controlada de `DB::transaction()` mediante Tinker | Excepción intencional provocó rollback; usuario temporal no quedó almacenado (`exists() = false`) |

## 5. Conclusión

Se realizó mantenimiento correctivo para solucionar la actualización parcial de pedidos y la validación de límites numéricos. También se realizó mantenimiento evolutivo para incorporar una capa de servicios.

Los cambios fueron comprobados mediante solicitudes HTTP y verificaciones técnicas en el entorno local.

## 6. Versiones y entorno del proyecto

Versiones utilizadas durante el desarrollo y las pruebas del Laboratorio III:

| Componente | Versión o tecnología |
|---|---|
| Laravel | 13.42.0 |
| PHP | 8.4.23 |
| Composer | 2.10.2 |
| Base de datos | MySQL |
| Autenticación API | Laravel Sanctum 4.4.0 |
| Entorno | Local |

**Nota:** Las versiones corresponden al entorno local utilizado para desarrollar y verificar el Sistema de Pedidos a Domicilio.