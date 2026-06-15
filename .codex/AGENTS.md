# SIIAA Laravel 13 - Memoria Operativa Codex

Proyecto: SIIAA Laravel 13 del IRyA-UNAM.

Ruta de trabajo:

- `/home/leonidas/code/laravel/siiaa_13`

Este archivo concentra reglas locales vigentes para que los cambios futuros respeten la arquitectura, convenciones y decisiones aprobadas del proyecto. Mantenerlo ordenado, sin repeticiones innecesarias y sin recuerdos obsoletos.

Criterio rector: todo código debe sentirse como parte natural del mismo SIIAA: institucional, ordenado, sobrio, modular, explícito, mantenible, compatible con la capa de identidad y respetuoso de modelos y migraciones aprobadas.

## Reglas Base

- Las identidades institucionales se resuelven mediante `IdentityLink`.
- No usar `users.id` ni `personas.id` cuando conceptualmente corresponde `identity_links.id`.
- Usar servicios explícitos para lógica crítica o de negocio.
- Livewire debe quedar enfocado en interfaz, estado de UI y validación inmediata.
- Evitar observers, triggers o automatizaciones mágicas.
- No tocar `public/build` salvo instrucción explícita.
- No ejecutar `git add .`, commit ni push salvo instrucción explícita.
- Antes de editar, mostrar los archivos objetivo.
- Después de editar, mostrar `git diff --stat`, archivos modificados y validaciones ejecutadas.

## Arquitectura Esperada

- Respetar modelos, migraciones, nombres de campos, relaciones y estructura de base de datos existentes.
- No inventar campos, tablas, relaciones ni permisos.
- Mantener separación clara entre modelos Eloquent, servicios de dominio, componentes Livewire, vistas Blade, policies/permisos y catálogos.
- No duplicar lógica de negocio en componentes Livewire.
- Preferir cambios mínimos y alineados al contexto aprobado.
- No tocar archivos no relacionados con la tarea.
- Ante duda, no inventar: revisar primero el código existente o dejar el supuesto claramente señalado.

## Documentación y Comentarios en Código

- Por ahora, no crear documentación aparte salvo instrucción explícita; la documentación externa del proyecto queda pendiente.
- La documentación requerida es código documentado mediante comentarios/docblocks útiles dentro del propio código.
- Los comentarios en código son importantes para mantenimiento y seguimiento futuro; no deben omitirse en flujos nuevos o críticos.
- Comentar de forma eficiente, clara y útil el flujo, las acciones de negocio, decisiones institucionales, reglas no evidentes y puntos donde identidad, permisos, auditoría, archivos o transacciones sean relevantes.
- No comentar lo obvio, asignaciones triviales, nombres autoexplicativos ni ruido narrativo que no ayude a mantener el código.
- Preferir comentarios breves antes de bloques con intención de negocio sobre comentarios línea por línea.
- En servicios, documentar reglas persistentes, transacciones, cambios de estado, folios, auditoría institucional y efectos posteriores como notificaciones.
- En Livewire, documentar por qué una acción delega en servicios o por qué una sección es estado de UI y no estado institucional.
- En Blade, usar comentarios solo para orientar secciones complejas; no explicar HTML evidente ni repetir texto visible.
- En migraciones, documentar intención de campos, llaves foráneas institucionales, restricciones y decisiones de integridad cuando aporten contexto de mantenimiento.
- En interfaces, documentar contratos cuando aclaren responsabilidades entre UI, servicio y efectos externos.

## Identidad Institucional

- `identity_links` es la fuente de verdad para identidad institucional.
- Distinguir entre usuario del sistema, identidad institucional, persona IRyA, estudiante SIIAP y perfil público.
- No asumir que `auth()->id()` equivale a autoría institucional.
- Cuando aplique autoría institucional, usar `identity_link_id` o el mecanismo vigente del proyecto.
- Admin y super-admin pueden no tener identidad institucional asociada; no mostrar advertencias innecesarias para esos casos.

## Servicios y Livewire

- La lógica crítica debe vivir en servicios explícitos.
- Los servicios concentran creación, actualización, eliminación, cambios de estado, carga de relaciones y operaciones transaccionales.
- Usar `DB::transaction()` cuando una acción de negocio afecte más de un dato.
- Livewire debe encargarse principalmente de estado visual, validación inmediata, llamadas a servicios, toasts y modales.
- Proteger acciones en backend con policies, permisos o validación equivalente; no confiar solo en controles visuales.

## Catálogos

- La tabla correcta de catálogos es `catalogos_items`.
- No usar `catalogo_items`.
- Para validaciones usar `Rule::exists('catalogos_items', 'id')` o el modelo correcto si ya existe.
- Las variables de catálogos deben llevar prefijo `c_`.

## Solicitudes

- Para Solicitudes, respetar modelos y migraciones aprobadas.
- Los catálogos de Solicitudes ya existen con datos en la base; no crear seeders de catálogos salvo instrucción explícita.
- Las evaluaciones y acciones propias de Consejo Interno no deben mostrarse ni ejecutarse desde Solicitudes; son módulos relacionados pero separados.
- Preservar auditoría ligada a identidad en transiciones de estado.
- Mantener permisos del módulo alineados con `solicitudes.access`, `solicitudes.review` y `solicitudes.manage`, salvo decisión explícita posterior.
- Usuarios con `solicitudes.manage`, especialmente admin/super-admin sin identidad institucional propia, pueden crear solicitudes a nombre de otra identidad institucional. En ese caso es obligatorio elegir solicitante desde una lista de `identity_links` activos de personal IRyA y estudiantes SIIAP; no usar `users.id`, `personas.id` ni `estudiantes.id` como propietario.
- Roles y permisos de Solicitudes se cargarán manualmente por administración; no asumir que se debe correr `RolesAndPermissionsSeeder`.
- Las migraciones de Solicitudes pueden ejecutarse de forma parcial solo para el cluster del módulo, sin tocar el resto de migraciones. Antes de ejecutar cualquier migración parcial, pedir confirmación explícita.
- Los recursos, documentos y requerimientos deben manejarse como subprocesos de una solicitud.
- No implementar historial custom adicional si ya existe auditoría básica mediante modelos/logs.

## Notificaciones

- En ambiente local o testing, las notificaciones por correo deben enviarse a un destinatario de prueba configurado.
- En producción se usan destinatarios reales resueltos desde usuarios/identidades del sistema.
- Si falta destinatario de prueba en local, registrar advertencia y no romper el flujo institucional.

## Validación y Trabajo Diario

- Revisar primero contexto, modelos, migraciones, relaciones reales y componentes existentes.
- Usar `with()` para evitar N+1 y `paginate()` en listados administrativos grandes.
- Evitar `all()` en listados grandes.
- Evitar lógica compleja o consultas pesadas en Blade.
- Usar componentes visuales existentes de SIIAA y mantener estética sobria, institucional y compacta.
- En vistas Blade que van dentro del dashboard, no incluir headers de página redundantes si el dashboard ya los provee.
- Preferir Sail para comandos Laravel del repo cuando aplique: `./vendor/bin/sail artisan ...`.
- No ejecutar migraciones sin confirmación previa cuando afecten Solicitudes; si se autoriza, usar rutas parciales del módulo y no `migrate:fresh` global.

## Memoria Local

- Este archivo debe mantenerse como memoria local vigente del proyecto.
- Mantener `.codex/AGENTS.md` y las memorias persistentes de Codex en sincronía cuando el usuario agregue o corrija reglas de arquitectura, estilo de programación, documentación o flujo de trabajo.
- Al agregar reglas nuevas, compactar o eliminar notas antiguas si ya están cubiertas por reglas más claras.
- Eliminar inconsistencias, duplicados y criterios obsoletos cuando se detecten.
- Eliminar o compactar recuerdos intermedios que ya no sean vigentes para conservar una memoria limpia y funcional.
- No convertir supuestos temporales en reglas permanentes sin confirmación explícita.
