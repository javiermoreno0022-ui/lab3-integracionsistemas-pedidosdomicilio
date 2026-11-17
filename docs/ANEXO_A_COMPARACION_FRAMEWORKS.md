# Anexo A. Comparación de frameworks backend

**Proyecto:** Sistema de Pedidos a Domicilio  
**Asignatura:** Integración de Sistemas  
**Laboratorio:** III

## 1. Comparación: Laravel vs. Express.js

| Criterio | Laravel | Express.js |
|---|---|---|
| Lenguaje | PHP | JavaScript |
| Arquitectura | Framework con estructura definida y convenciones | Framework minimalista y flexible para Node.js |
| Rutas y controladores | Sistema integrado de rutas y controladores | Enrutamiento mediante middleware y módulos |
| Validación | Form Requests y reglas integradas | Requiere incorporar bibliotecas o validaciones propias |
| Base de datos | Eloquent ORM integrado | Requiere integrar herramientas como Sequelize o Prisma |
| Autenticación | Compatible con Laravel Sanctum | Requiere implementar o integrar soluciones de autenticación |
| Migraciones | Sistema de migraciones integrado | Depende de herramientas adicionales |
| Mantenimiento | Estructura organizada, servicios y herramientas propias | Mayor libertad, pero requiere definir convenciones del proyecto |

## 2. Análisis

Laravel proporciona herramientas integradas para desarrollar APIs REST, validar solicitudes, administrar relaciones con bases de datos y proteger rutas mediante autenticación.

Express.js ofrece flexibilidad y permite construir APIs ligeras, pero requiere seleccionar e integrar diferentes bibliotecas para cubrir varias de esas necesidades.

Para el sistema de pedidos, Laravel permite mantener una estructura organizada mediante controladores, Form Requests, modelos Eloquent, API Resources, Sanctum y clases de servicios.

## 3. Justificación de la elección

Se eligió Laravel porque el proyecto está desarrollado en PHP y necesita operaciones CRUD, validaciones, autenticación mediante tokens, aislamiento de información por usuario y respuestas JSON.

Además, sus herramientas integradas facilitan el desarrollo, las pruebas y el mantenimiento del sistema. La incorporación de `PedidoService` permite separar la lógica de negocio de los controladores y mejorar la organización del código.

Aunque Express.js también permite desarrollar una API REST, Laravel se adapta mejor a las tecnologías y a la estructura utilizadas en este proyecto.

**Conclusión:** Laravel es la opción más conveniente para el Sistema de Pedidos a Domicilio porque proporciona las herramientas necesarias para implementar y mantener la API con una arquitectura organizada, segura y coherente con el entorno de desarrollo del equipo.