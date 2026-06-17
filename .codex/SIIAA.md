# SIIAA - Memoria operativa

Fecha de actualización: 2026-06-17
Repositorio: `iryaunamdev/siiaa_laravel_13`
Rama base: `main`

Este archivo es la memoria operativa para Codex/agentes. Cada vez que se actualicen memorias relevantes del proyecto, también se debe actualizar este archivo vía GitHub.

`.context/` queda reservado para documentos ejecutivos por módulo/proyecto, resúmenes de diseño y documentación de referencia.

---

## 1. Fuente de verdad y flujo de trabajo

- Antes de proponer o modificar código, revisar los archivos actuales en GitHub.
- Rama base usual: `main`.
- Si el usuario trabaja localmente y aún no hizo push, el conector de GitHub no verá esos cambios.
- Si hay diferencia entre memoria y repo, priorizar el repo y pedir confirmación antes de sobrescribir decisiones locales.
- Para el proyecto SIIAA no crear ramas adicionales salvo que el usuario lo pida explícitamente.
- Mantener cambios pequeños, verificables y alineados al estilo SIIAA/IRyA.

---

## 2. Estilo de programación SIIAA / IRyA

- Stack: Laravel 13 + Livewire + Alpine.js + TailwindCSS.
- Usar documentación oficial como base conceptual.
- SIIAA es un sistema integral: distintos módulos atienden distintos procesos, pero comparten información, servicios y acciones transversales como identidad, correos, notificaciones, datos de personal, catálogos y componentes UI.
- Diseñar servicios, helpers, componentes y elementos compartidos cuando resuelvan necesidades comunes entre módulos.
- No atomizar ni separar funciones de forma exagerada. Evitar dispersar archivos, clases y métodos si una solución general, funcional y compacta mantiene mejor el sistema.
- Reutilizar servicios existentes entre módulos cuando tenga sentido institucional. Ejemplo: notificaciones/correos de Solicitudes pueden ser consumidos por Consejo Interno para estados posteriores.
- Evitar CRUDs planos cuando el módulo está definido como expediente institucional.
- Preferir secciones funcionales, servicios por dominio, componentes UI reutilizables y código explícito.
- No duplicar headers si el layout dashboard ya los aporta.
- Mantener código sobrio; comentarios solo cuando aclaren reglas de negocio.
- Usar componentes UI existentes antes de crear nuevos.
- Confirmaciones de eliminación deben usar componentes modales homogéneos, no `wire:confirm`, cuando ya exista componente institucional.
- Servicios e interfaces van juntos en `app/Services/{Modulo}`; no usar `app/Contracts`.

---

## 3. Convenciones generales

- Tabla correcta de catálogos: `catalogos_items`.
- No usar `catalogo_items`.
- Variables de catálogos con prefijo `c_`.
- Autoría, propiedad y auditoría institucional mediante `identity_links.id`.
- Helpers reales de identidad actual:
  - `currentIdentityId()`
  - `currentIdentityType()`
  - `currentIdentity()`
- No usar `activeIdentityLinkId()` en el proyecto actual; causó error fatal por función inexistente.
- Admin/super-admin/SACAD pueden no tener identidad activa. Esto es válido.
- `owner_id` representa la identidad propietaria real del registro, no necesariamente quien opera el sistema.
- Si admin/SACAD opera sin identidad activa y tiene permiso de gestión, los campos de auditoría pueden quedar `null` donde aplique.
- No usar `owner_id` como fallback de actor porque falsea auditoría.

---

## 4. Componentes UI SIIAA

Antes de escribir Tailwind manual en vistas nuevas o refactorizadas, revisar y priorizar componentes en `resources/views/components/ui`.

Inventario actual usable:

- `x-ui.alert`: alertas success/error/warning/info/status y flash messages.
- `x-ui.badge`: estados, etiquetas, pills y contadores.
- `x-ui.button`: botones primary/secondary/danger/ghost/link; también renderiza enlace si recibe `href`.
- `x-ui.checkbox`: checkboxes simples o listas; ya soporta ayuda contextual con `x-ui.help`.
- `x-ui.confirm-delete-modal`: confirmación institucional de eliminación. Debe reemplazar `wire:confirm`.
- `x-ui.help`: ayuda contextual tipo tooltip/popover. Escapa texto por defecto; usar `html` solo con contenido controlado por desarrollador.
- `x-ui.icon`: íconos internos disponibles: `trash`, `edit`, `chevron-down`, `plus`, `exclamation-triangle`, `cancel`, `eye-open`, `eye-closed`, `eye-cancel`.
- `x-ui.identity-warning`: advertencias de identidad/suplantación.
- `x-ui.input`: inputs generales; texto, email, número, fecha, etc.
- `x-ui.input-file`: carga de archivos normal o drag & drop, compatible con Livewire, `multiple` y `accept`.
- `x-ui.modal`: modal base con `wire:model`, Alpine y `closeAction` opcional.
- `x-ui.radio`: radio buttons simples.
- `x-ui.select`: select simple/múltiple para arrays, Collections y Eloquent Collections. Ideal para catálogos SIIAA por defaults `id`/`nombre`.
- `x-ui.switch`: booleanos visuales; activo/inactivo, visible/oculto, requiere autorización, habilitar configuración.
- `x-ui.textarea`: campos largos con label, error, ayuda y popover.

Reglas prácticas:

- Formularios: usar `x-ui.input`, `x-ui.select`, `x-ui.textarea`, `x-ui.checkbox`, `x-ui.switch`, `x-ui.radio`.
- Acciones: usar `x-ui.button`.
- Estados: usar `x-ui.badge`.
- Alertas: usar `x-ui.alert`.
- Modales: usar `x-ui.modal`; para eliminar, usar `x-ui.confirm-delete-modal`.
- Archivos: usar `x-ui.input-file`.
- Ayuda contextual: usar `x-ui.help`.
- Si no existe un componente necesario y el patrón será reutilizable, crear uno en `components/ui`; si es un caso puntual, mantenerlo local y simple.

---

## 5. Módulo Solicitudes — alcance y diseño aprobado

### 5.1 Alcance funcional

El módulo Solicitudes termina funcionalmente cuando una solicitud pasa de `BORRADOR` a `SENV`:

1. Crear borrador.
2. Editar expediente.
3. Capturar visitante/requerimientos si aplica.
4. Capturar recursos si aplica.
5. Adjuntar documentos.
6. Revisar y enviar.
7. Generar folio.
8. Notificar/correo de solicitud enviada al solicitante y al Consejo Interno.

Las revisiones y estados posteriores a `SENV` pertenecen al módulo Consejo Interno. Sin embargo, por criterio de sistema integral, servicios generales como correos/notificaciones pueden conservar métodos reutilizables para que CI los consuma.

### 5.2 Catálogos

Tipos `SOLTIPOS`:

- `AUS_REC` [554]
- `AUSENCIA` [517]
- `SOLOREC` [518]
- `VISITA` [325]
- `ESOLREC` [516]

No existe tipo OTRO.

Motivos `SOLMOT`:

- `EVACAD` [323]
- `ESTT` [324]
- `ACTDIV` [328]
- `TCAMP` [329]
- `OTRO` [555]

Regla: si motivo es `OTRO`, `motivo_otro` debe ser obligatorio en la validación final.

Estados `SOL_EST`:

- `BORRADOR` [556]
- `SENV` [553]
- `APRCI` [532]
- `RECI` [533]
- `TRPAG` [534]
- `PAG` [537]
- `CLO` [538]
- `CANCELADA` [557]
- `REVCIC` [535] histórico/no funcional.

Reglas de edición:

- Propietario edita en `BORRADOR` y `SENV`.
- Propietario queda bloqueado desde `APRCI`, `RECI`, `TRPAG`, `PAG`, `CLO`, `CANCELADA`.
- Propietario solo borra físicamente borradores propios.
- SACAD/admin con `solicitudes.manage` puede gestionar administrativamente.

### 5.3 Permisos

- `solicitudes.access`: acceso normal del propietario.
- `solicitudes.review`: revisión SACAD/CI; permite ver todas y editar observaciones de revisión/administración.
- `solicitudes.manage`: gestión completa; crear en representación de otra identidad, cambiar estados, archivar/eliminar y enviar administrativamente.

No crear permisos sueltos finos como `solicitudes.delete`. En Blade se pueden usar abilities semánticas de policy, por ejemplo `@can('delete', $solicitud)`, pero la policy debe resolver con los permisos integrados anteriores.

### 5.4 Flujo aprobado

- `SolicitudesCreate`: creación mínima de borrador.
  - `owner_id` si puede crear a nombre de otra identidad.
  - `tipo_solicitud_id`.
  - Redirige a edición del expediente.
- `SolicitudesEdit`: expediente por pasos.
  1. Datos generales + visitante/requerimientos si aplica.
  2. Recursos si `requiere_recursos`.
  3. Documentos.
  4. Revisión/envío.

### 5.5 Reglas de recursos

- `AUS_REC`, `SOLOREC`, `ESOLREC`: requieren recursos automáticamente.
- `AUSENCIA`: no requiere recursos.
- `VISITA`: es el único tipo donde `requiere_recursos` es editable por checkbox; puede requerir o no recursos según el caso.

---

## 6. Autoría y operación administrativa

Regla aprobada:

```text
owner_id = identidad propietaria real de la solicitud.
created_by / updated_by / uploaded_by / submitted_by = identidad activa si existe.
Si el operador tiene solicitudes.manage y no tiene identidad activa, esos campos pueden ser null.
```

`owner_id` no debe ser nullable.

Campos de auditoría que deben admitir `null` para operación admin sin identidad activa:

- `created_by`
- `updated_by`
- `uploaded_by`
- `submitted_by`
- `approved_by`
- `rejected_by`
- `closed_by`
- `cancelled_by`
- `archived_by`
- `politica_aceptada_by`

---

## 7. Estado implementado y probado en Solicitudes

Confirmado por pruebas del usuario:

- `SolicitudesEdit` carga correctamente.
- Paso 1 funciona.
- País por default: México (`PAISES` clave `MEX`) cuando el campo aún no tiene valor.
- Visitante/requerimientos funcionan hasta donde se ha probado.
- Recursos funcionan, incluyendo caso opcional para `VISITA`.
- Documentos funcionan:
  - adjuntar
  - descargar
  - eliminar con modal UI
- Paso 4 revisión/envío implementado.
- Validación previa al envío implementada:
  - tipo obligatorio
  - motivo obligatorio si aplica
  - `motivo_otro` obligatorio cuando motivo es `OTRO`
  - datos mínimos de visitante si tipo `VISITA`
  - al menos un recurso si `requiere_recursos`
  - documentos no bloquean envío; solo advertencia
- `SolicitudService::enviar()` genera folio al enviar, formato `YYYY/NNN`, y mueve a `SENV`.
- Admin/SACAD sin identidad activa puede operar documentos/envío sin falsear `owner_id`.
- Eliminación física de solicitudes desde índice implementada según policy:
  - propietario solo borrador propio
  - `solicitudes.manage` con advertencia explícita

### Documentos

Ruta aprobada de almacenamiento:

```text
documentos/solicitudes/{year}/{solicitud_id}/archivo.pdf
```

Disco:

```php
$disk = config('filesystems.default', 'local');
```

Decisión: mantener `FILESYSTEM_DISK=local`; no crear disco privado adicional.

Descarga:

- Controlador autorizado `SolicitudDocumentoDownloadController`.
- Usa `$this->authorize('view', $documento->solicitud)`.
- Requirió `AuthorizesRequests` en el controlador.
- No exponer documentos por disco público.

### Notificaciones y correo

- `SolicitudService::enviar()` llama a `NotificationServiceInterface::solicitudEnviada()`.
- `NotificationService::solicitudEnviada()` envía correo al solicitante y al Consejo Interno.
- `MailService` redirige destinatarios a prueba fuera de producción y usa reales en producción según `config('siiaa.mail.use_real_recipients')`.
- Métodos de aprobado/rechazado/cerrado se conservan en servicios de notificación/correo para reutilización desde CI.

---

## 8. Comandos útiles para retomar

Actualizar local:

```bash
git pull origin main
```

Validar PHP:

```bash
php -l app/Livewire/Solicitudes/SolicitudesEdit.php
php -l app/Services/Solicitudes/SolicitudService.php
php -l app/Services/Solicitudes/SolicitudServiceInterface.php
php -l app/Services/Notifications/NotificationService.php
php -l app/Services/Notifications/NotificationServiceInterface.php
php -l app/Services/Mail/MailService.php
php -l app/Services/Mail/MailServiceInterface.php
php -l app/Policies/SolicitudPolicy.php
php artisan optimize:clear
```

Revisar referencias peligrosas:

```bash
grep -R "activeIdentityLinkId\|cancel_reason\|reject_reason\|wire:confirm" -n app resources database routes
```

Revisar componentes UI:

```bash
grep -R "duration-zinc\|border-zinc-zinc\|zinc0/svg\|wire:confirm" -n resources/views/components resources/views/livewire
```

Revisar documentos guardados:

```bash
find storage/app/documentos/solicitudes -type f | head
```

---

## 9. Prioridad siguiente

Al continuar:

1. No reabrir diseño de documentos salvo bug.
2. Cerrar notificación/correo de envío si queda algún ajuste fino.
3. Revisar `SolicitudesShow` para que represente bien el expediente enviado.
4. Mantener métodos reutilizables de servicios si sirven a CI u otros módulos.
5. No atomizar ni dispersar archivos sin necesidad institucional real.
6. Mantener componentes UI existentes; evitar crear duplicados.
7. Todo cambio de eliminación debe usar `x-ui.confirm-delete-modal`.
8. Antes de escribir HTML/Tailwind manual, revisar si existe componente `x-ui` aplicable.
