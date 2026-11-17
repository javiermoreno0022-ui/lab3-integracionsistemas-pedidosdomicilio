# Ficha de uso crítico de Inteligencia Artificial

**Proyecto:** Sistema de Pedidos a Domicilio  
**Asignatura:** Integración de Sistemas  
**Laboratorio:** III  
**Fecha:** 16 de noviembre de 2026

## 1. Objetivo

Documentar el uso de herramientas de Inteligencia Artificial durante el desarrollo de la API REST, incluyendo las solicitudes realizadas, las propuestas obtenidas, los problemas identificados, las correcciones aplicadas y la justificación técnica de las decisiones.

La IA se utilizó como apoyo para generar y revisar código. Las propuestas fueron contrastadas con la estructura del proyecto y verificadas mediante pruebas locales.

## 2. Caso 1: validación diferenciada para PUT y PATCH

### Solicitud realizada a la IA

**Prompt utilizado (resumen de la solicitud):**

"Revisar el Form Request de actualización de pedidos en Laravel para permitir que PUT exija todos los campos y PATCH permita actualizar únicamente los campos enviados, manteniendo las reglas de validación existentes."

### Código propuesto

La solución utiliza una regla condicional:

```php
$regla = $this->isMethod('PUT') ? 'required' : 'sometimes';
```

Esta regla se aplica a los atributos del pedido dentro del método `rules()`.

### Problema identificado

La implementación inicial utilizaba `required` para todos los atributos. Esto provocaba que una solicitud PATCH con un solo campo fuera rechazada por falta de los demás atributos.

### Corrección aplicada

Se incorporó la regla condicional en:

`app/Http/Requests/Api/UpdatePedidoRequest.php`

De esta manera:

- PUT exige todos los campos.
- PATCH valida únicamente los campos enviados.
- Se mantienen las restricciones de tipo, longitud, valores permitidos y cantidades mínimas.

### Pruebas de verificación

| Prueba | Resultado |
|---|---|
| PATCH enviando únicamente el estado | HTTP 200 |
| PUT enviando únicamente el estado | HTTP 422 |
| Consultar después del PUT rechazado | HTTP 200, datos anteriores conservados |

### Justificación técnica

Se decidió utilizar un único Form Request para ambos métodos HTTP, diferenciando sus reglas según el método de la solicitud. Esto evita duplicar las reglas de validación y respeta el comportamiento esperado de PUT y PATCH.

### Revisión crítica de la propuesta

Se revisó que la regla condicional no eliminara las validaciones originales de los atributos. Se comprobó que la implementación conservara las restricciones de tipo, longitud, cantidad mínima, valores permitidos mediante `Rule::in()` y límites máximos para los campos monetarios.

**Decisión final:** Se aceptó la estrategia de utilizar `required` para PUT y `sometimes` para PATCH, porque permitió realizar actualizaciones parciales sin debilitar las validaciones existentes. La implementación fue comprobada mediante solicitudes HTTP.

## 3. Caso 2: refactorización mediante una capa de servicios

### Solicitud realizada a la IA

**Prompt utilizado (resumen de la solicitud):**

"Refactorizar el controlador API de pedidos en Laravel para trasladar la lógica de negocio a una clase PedidoService, utilizar inyección de dependencias y conservar las respuestas y el comportamiento de los endpoints."

### Código propuesto

Se propuso una clase de servicio con operaciones independientes:

```php
class PedidoService
{
    public function listar(User $usuario): Collection
    {
        return $usuario->pedidos()
            ->with('user')
            ->latest()
            ->get();
    }

    public function crear(User $usuario, array $datos): Pedido
    {
        return $usuario->pedidos()->create($datos);
    }
}
```

La misma clase contiene los métodos `mostrar`, `actualizar` y `eliminar`.

El controlador recibe el servicio mediante inyección de dependencias en su constructor.

### Problema identificado durante la integración

Durante la integración se detectó que el controlador destinado a la API había sido colocado accidentalmente en el archivo del controlador web.

Esto produjo un conflicto de declaración de clase y un error de carga PSR-4.

### Corrección aplicada

- Se restauró `app/Http/Controllers/PedidoController.php`, correspondiente al controlador web.
- Se colocó la implementación refactorizada en `app/Http/Controllers/Api/PedidoController.php`.
- Se creó `app/Services/PedidoService.php`.
- Se verificó la carga automática de clases con Composer.

### Pruebas de verificación

| Prueba | Resultado |
|---|---|
| `composer dump-autoload` | Correcto, sin advertencias PSR-4 |
| Listar pedidos propios | HTTP 200 |
| Consultar pedido propio | HTTP 200 |
| Actualizar pedido | HTTP 200 |
| Actualizar parcialmente | HTTP 200 |
| Eliminar pedido | HTTP 204 |
| Consultar pedido de otro usuario | HTTP 404 |

### Justificación técnica

Se decidió separar las responsabilidades para que el controlador gestione las solicitudes HTTP y el servicio concentre las operaciones de negocio.

Esta estructura facilita el mantenimiento, la reutilización y las futuras ampliaciones de la API. También permite mantener el aislamiento de pedidos mediante las relaciones del usuario autenticado.

### Revisión crítica de la propuesta

Se revisó que los métodos del servicio utilizaran la relación `pedidos()` del usuario autenticado para mantener el aislamiento de información. También se comprobó que las operaciones de consulta, creación, actualización y eliminación conservaran sus responsabilidades y que el controlador utilizara inyección de dependencias.

Se verificó la carga automática de las clases mediante Composer y se repitieron las solicitudes HTTP para comprobar que el comportamiento de los endpoints se mantuviera.

**Decisión final:** Se aceptó la separación de la lógica de negocio en `PedidoService`, porque mejora la organización y mantenibilidad del proyecto sin modificar el comportamiento esperado de la API.

## 4. Evaluación crítica del uso de IA

Durante el desarrollo se aplicaron los siguientes criterios:

- No se aceptaron las propuestas de código sin revisarlas.
- Se verificaron los espacios de nombres y las rutas de los archivos antes de integrar los cambios.
- Se utilizaron comandos de PHP y Composer para detectar errores de sintaxis y carga automática.
- Se ejecutaron solicitudes HTTP para comprobar el comportamiento real de los endpoints.
- Se revisaron los resultados de las pruebas antes de considerar terminada cada modificación.
- No se proporcionaron tokens reales, contraseñas ni contenido de `.env` a la herramienta de IA.

## 5. Conclusión

La Inteligencia Artificial facilitó la identificación de soluciones para mejorar las validaciones y la organización del código de la API REST.

Sin embargo, sus propuestas no se incorporaron automáticamente. Fue necesario revisar el código generado, comprobar su compatibilidad con la arquitectura del proyecto, identificar problemas de integración y aplicar las correcciones correspondientes.

Las decisiones finales se fundamentaron en los requisitos del laboratorio, los principios de seguridad y mantenibilidad, las verificaciones de sintaxis y carga automática, y los resultados de las pruebas HTTP realizadas en el entorno local.

Se concluye que la IA fue utilizada como una herramienta de apoyo al desarrollo, manteniendo la revisión técnica y la responsabilidad de las decisiones de implementación.