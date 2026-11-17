# Evidencia de pruebas: CRUD y validaciones de la API REST

**Proyecto:** Sistema de Pedidos a Domicilio — Laboratorio III  
**Fecha:** 16 de noviembre de 2026  
**Entorno:** Local  
**URL base:** `http://127.0.0.1:8000`  
**Herramienta utilizada:** PowerShell (`Invoke-RestMethod`)  
**Autenticación:** Laravel Sanctum mediante tokens Bearer

## 1. Objetivo

Verificar las operaciones CRUD de pedidos, las reglas de validación, la persistencia de los cambios y el aislamiento de información entre usuarios autenticados.

## 2. Registro de pruebas

| ID | Caso | Procedimiento | Resultado esperado | Resultado observado | Estado |
|---|---|---|---|---|---|
| CRUD-01 | Listar pedidos propios | GET `/api/pedidos` con token válido | HTTP 200 y colección del usuario | Se obtuvo la colección del usuario autenticado | APROBADO |
| CRUD-02 | Crear pedido válido | POST `/api/pedidos` con datos correctos | HTTP 201 y recurso creado | Se creó un pedido y se recibió el recurso JSON | APROBADO |
| CRUD-03 | Consultar pedido propio | GET `/api/pedidos/5` con token del propietario | HTTP 200 | Se obtuvieron los datos del pedido | APROBADO |
| CRUD-04 | Actualizar pedido completo | PUT `/api/pedidos/5` con todos los campos | HTTP 200 | Se actualizaron los datos del pedido | APROBADO |
| CRUD-05 | Verificar actualización | Consultar nuevamente el pedido actualizado | HTTP 200 y datos persistidos | Se comprobaron los nuevos valores | APROBADO |
| CRUD-06 | Eliminar pedido | DELETE `/api/pedidos/5` con token del propietario | HTTP 204 | La API respondió sin contenido | APROBADO |
| CRUD-07 | Consultar pedido eliminado | GET `/api/pedidos/5` después de eliminarlo | HTTP 404 | La API indicó que el pedido no existe | APROBADO |
| VAL-01 | Estado inválido | POST `/api/pedidos` con estado `En espera` | HTTP 422 | La validación rechazó el estado | APROBADO |
| VAL-02 | Cantidad igual a cero | POST `/api/pedidos` con cantidad `0` | HTTP 422 | La validación rechazó la cantidad | APROBADO |
| VAL-03 | Total negativo | POST `/api/pedidos` con total `-10.00` | HTTP 422 | La validación rechazó el total | APROBADO |
| VAL-04 | Total superior al máximo en creación | POST `/api/pedidos` con total `100000000.00` | HTTP 422 | La validación rechazó el valor superior al límite | APROBADO |
| VAL-05 | Total superior al máximo en actualización | PATCH `/api/pedidos/7` con total `100000000.00` | HTTP 422 | La validación rechazó el valor superior al límite | APROBADO |
| VAL-06 | Costo express superior al máximo en creación | POST `/api/pedidos` con `costo_express` `100000000.00` | HTTP 422 | La validación rechazó el valor superior al límite | APROBADO |
| VAL-07 | Costo express superior al máximo en actualización | PATCH `/api/pedidos/7` con `costo_express` `100000000.00` | HTTP 422 | La validación rechazó el valor superior al límite | APROBADO |
| SEG-01 | Acceso a pedido ajeno | GET `/api/pedidos/6` usando el token del primer usuario | HTTP 404 | El pedido de otro usuario no fue accesible | APROBADO |
| SEG-02 | Aislamiento en listado | GET `/api/pedidos` usando el token del primer usuario | HTTP 200 sin pedidos ajenos | No se mostró el pedido del segundo usuario | APROBADO |
| PATCH-01 | Actualización parcial | PATCH `/api/pedidos/7` enviando solamente `estado` | HTTP 200 y demás campos conservados | Se actualizó el estado a `Preparando` y los demás campos permanecieron iguales | APROBADO |
| PUT-01 | PUT incompleto | PUT `/api/pedidos/7` enviando solamente `estado` | HTTP 422 por campos obligatorios ausentes | La API rechazó la solicitud y señaló los campos requeridos | APROBADO |
| PATCH-02 | Persistencia después de PUT fallido | GET `/api/pedidos/7` después del PUT incompleto | HTTP 200 y estado anterior conservado | El pedido mantuvo el estado `Preparando` y sus demás datos | APROBADO |
| USU-01 | Consultar usuario autenticado y sus pedidos | GET `/api/usuarios` usando el token del segundo usuario | HTTP 200 y únicamente los datos del usuario autenticado y sus pedidos | Se obtuvo el usuario ID 5 y su pedido ID 7; no se expusieron contraseñas ni tokens | APROBADO |

**Resultado documentado:** 20 casos aprobados de 20 ejecutados.

## 3. Incidencias y correcciones verificadas

### Incidencia 1: actualización parcial mediante PATCH

**Problema:** La solicitud PATCH exigía todos los campos del pedido, aunque solamente se enviara un campo para modificar.

**Causa:** Las reglas de `UpdatePedidoRequest` utilizaban `required` para todos los atributos.

**Corrección:** Se incorporó una regla condicional:

`$regla = $this->isMethod('PUT') ? 'required' : 'sometimes';`

Así, PUT exige todos los campos y PATCH valida solamente los campos enviados.

**Verificación:** PATCH con únicamente `estado` respondió correctamente y PUT incompleto devolvió HTTP 422. Se comprobó posteriormente que los datos se conservaron.

### Incidencia 2: codificación del archivo UpdatePedidoRequest

**Problema:** PHP mostró un error indicando que la declaración del namespace no estaba al principio del archivo.

**Causa:** El archivo fue guardado con marca BOM UTF-8 durante su edición desde PowerShell.

**Corrección:** Se volvió a guardar el archivo en UTF-8 sin BOM.

**Verificación:** `php -l app/Http/Requests/Api/UpdatePedidoRequest.php` respondió `No syntax errors detected`.

**Nota:** Esta segunda incidencia fue de codificación del archivo, no un defecto funcional de las reglas de validación.

### Incidencia 3: límites máximos de valores numéricos

**Problema:** Los campos `total` y `costo_express` aceptaban valores superiores al máximo permitido por las columnas `DECIMAL(10,2)` de la base de datos.

**Causa:** Los Form Requests de la API no tenían una regla que limitara el valor máximo de esos campos.

**Corrección:** Se agregó la regla `max:99999999.99` en `StorePedidoRequest` y `UpdatePedidoRequest`.

**Verificación:** Se ejecutaron cuatro pruebas con valores de `100000000.00`, tanto en creación como en actualización, para ambos campos. Todas devolvieron HTTP 422.

## 4. Seguridad y aislamiento de usuarios

Se realizaron pruebas con dos usuarios autenticados mediante tokens diferentes de Sanctum.

El segundo usuario creó un pedido de prueba. Al intentar consultarlo con el token del primer usuario, la API respondió HTTP 404.

También se verificó que el listado del primer usuario no incluyera pedidos pertenecientes al segundo.

Esto demuestra que las consultas de pedidos se limitan al usuario autenticado y que no se permite acceder a pedidos ajenos mediante la modificación del identificador.

## 5. Conclusión

Las pruebas realizadas verificaron:

- Operaciones de creación, consulta, actualización y eliminación de pedidos.
- Respuestas HTTP 200, 201, 204, 404 y 422.
- Validación de estado, cantidad y total.
- Actualizaciones completas mediante PUT.
- Actualizaciones parciales mediante PATCH.
- Persistencia de los datos después de una solicitud inválida.
- Aislamiento de pedidos entre usuarios autenticados.

Los resultados corresponden a las pruebas ejecutadas en el entorno local. Esta evidencia se complementa con el documento `evidencia_pruebas_autenticacion_sanctum.md`.


