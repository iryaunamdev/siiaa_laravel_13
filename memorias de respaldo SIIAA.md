# Memorias de respaldo SIIAA

Fecha de corte: 2026-06-16
Proyecto: SIIAA Laravel 13 + Livewire + Alpine.js + TailwindCSS
Repositorio: iryaunamdev/siiaa_laravel_13

> Este archivo consolida lo aprobado y lo realizado hasta este punto para continuar el desarrollo sin perder contexto. El bloque de revisión final/envío queda explícitamente pendiente.

---

## 1. Contexto general del proyecto

- Proyecto institucional IRyA/UNAM en Laravel 13 + Livewire.
- Desarrollo local vigente en WSL2 Ubuntu:
  - Linux: `/home/leonidas/code/laravel/siiaa_13`
  - Windows: `\\wsl$\Ubuntu-24.04\home\leonidas\code\laravel\siiaa_13`
- Convención SIIAA:
  - Usar `catalogos_items`, nunca `catalogo_items`.
  - Variables de catálogos con prefijo `c_`.
  - Autoría/propiedad/auditoría por `identity_links.id`.
  - Helper vigente para identidad activa: `currentIdentityId()`.
  - Evitar `activeIdentityLinkId()`; causó error fatal por función inexistente.
- Admin/super-admin/SACAD pueden no tener identidad activa. Esto es válido.
- `owner_id` representa la identidad propietaria real de la solicitud, no necesariamente quien opera el sistema.
- Si SACAD/admin opera sin identidad activa, los campos de auditoría pueden quedar `null` cuando aplique.
- No usar `owner_id` como fallback de actor, porque falsea la auditoría.

---

## 2. Reglas aprobadas del módulo Solicitudes

### 2.1 Catálogos

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

Regla: si motivo es `OTRO`, `motivo_otro` será obligatorio en la validación final.

Estados `SOL_EST`:

- `BORRADOR` [556]
- `SENV` [553]
- `APRCI` [532]
- `RECI` [533]
- `TRPAG` [534]
- `PAG` [537]
- `CLO` [538]
- `CANCELADA` [557]
- `REVCIC` [535] queda histórico/no funcional.

Reglas de edición:

- Propietario puede editar en `BORRADOR` y `SENV`.
- Bloqueado desde `APRCI`, `RECI`, `TRPAG`, `PAG`, `CLO`, `CANCELADA`.
- Borrado físico por propietario solo en BORRADOR.
- SACAD/admin con `solicitudes.manage` puede gestionar administrativamente.

### 2.2 Permisos

- `solicitudes.access`: acceso normal del propietario.
- `solicitudes.review`: revisión SACAD/CI; permite ver todas y editar observaciones de revisión/administración.
- `solicitudes.manage`: gestión completa, crear en representación de otra identidad, cambiar estados, archivar/eliminar y enviar administrativamente.

### 2.3 Flujo aprobado

- `SolicitudesCreate`: creación mínima de borrador.
  - Solo `owner_id` si puede crear a nombre de otro.
  - `tipo_solicitud_id`.
  - Redirige a edición del expediente.
- `SolicitudesEdit`: expediente por pasos.
  1. Datos generales + visitante/requerimientos si aplica.
  2. Recursos si `requiere_recursos`.
  3. Documentos.
  4. Revisión/envío. **Pendiente.**

---

## 3. Decisiones de arquitectura y autoría

Regla aprobada para admin/SACAD:

```text
owner_id = identidad propietaria real de la solicitud.
created_by / updated_by / uploaded_by / submitted_by = identidad activa si existe.
Si el operador tiene solicitudes.manage y no tiene identidad activa, esos campos pueden ser null.
```

Se agregó/usa helper interno en `SolicitudesEdit`:

```php
protected function actorIdentityId(bool $allowManageWithoutIdentity = false): ?int
{
    $identityId = \currentIdentityId();

    if ($identityId) {
        return $identityId;
    }

    if ($allowManageWithoutIdentity && auth()->user()?->can('solicitudes.manage')) {
        return null;
    }

    abort(403, 'No se encontro una identidad institucional activa.');
}
```

Los métodos del servicio aceptan `?int $actorIdentityId` donde aplique.

Campos de auditoría que deben permitir `null` cuando opere admin sin identidad activa:

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

`owner_id` no debe ser nullable.

---

## 4. Bloques ya implementados y probados

### 4.1 Limpieza de estados y observaciones

Hecho:

- Se eliminaron rastros funcionales de `cancel_reason` y `reject_reason`.
- Cancelación escribe en `observaciones_administracion`.
- Rechazo CI/SACAD escribe en `observaciones_sacad`.
- Se corrigió uso de estados funcionales actuales.
- Se corrigió uso de `currentIdentityId()`.

### 4.2 Creación mínima de solicitud

Hecho:

- `SolicitudesCreate` reducido a borrador mínimo.
- Campos principales:
  - `owner_id` si `solicitudes.manage`.
  - `tipo_solicitud_id`.
- El resto se captura en `SolicitudesEdit`.

### 4.3 Paso 1: datos generales

Hecho:

- Campos en formulario:
  - `owner_id`
  - `tipo_solicitud_id`
  - `requiere_recursos`
  - `motivo_id`
  - `motivo_otro`
  - `fecha_inicio`
  - `fecha_fin`
  - `pais_id`
  - `nombre_evento`
  - `tipo_presentacion`
  - `institucion`
  - `anfitrion`
  - `lugar`
  - `tutor_id`
  - `informacion_adicional`
  - `requiere_seguro_unam`
  - `seguro_unam_beneficiario`
- Catálogos cargados:
  - `SOLTIPOS`
  - `SOLMOT`
  - `PAISES`
  - tutores por `identity_links` tipo `persona`.
- La consulta de tutores queda amplia temporalmente; después podrá restringirse a investigadores IRyA si se define la relación exacta.

### 4.4 Visitante y requerimientos

Hecho:

- Visitante condicionado a tipo `VISITA`.
- Un visitante principal por solicitud.
- Campos de visitante:
  - `tipo_visitante_id`
  - `estudiante_asociado_id`
  - `nombre`
  - `apellidos`
  - `email`
  - `pais_id`
  - `institucion_id`
  - `institucion`
  - `lugar`
  - `fecha_inicio`
  - `fecha_fin`
- Requerimientos por checkboxes desde catálogo `VIS_REQ`.
- No hay “Otro” en requerimientos.
- `requerimientosCatalogo()` en modelo `Solicitud` usa `belongsToMany` vía tabla `solicitudes_requerimientos`.

### 4.5 Recursos

Hecho:

- Paso 2 activo solo si `requiere_recursos`.
- Múltiples bloques de recursos.
- Catálogos:
  - `C_OREC`
  - `DIVISAS`
- MXN se usa como divisa default cuando exista en catálogo.
- Campos por recurso:
  - `origen_id`
  - `proyecto_id`
  - `proyecto_nombre`
  - `dias_n`
  - `dias_i`
  - `cuota`
  - `cuota_divisa`
  - `avion`
  - `avion_divisa`
  - `otro`
  - `otro_divisa`
  - `informacion_adicional`
- `guardarRecursos()` filtra bloques vacíos.

### 4.6 Documentos

Hecho y probado:

- Paso 3 de documentos funcionando.
- Adjuntar documentos funciona.
- Descargar documentos funciona.
- Eliminar documentos funciona.
- Eliminación usa componente homogéneo:
  - `resources/views/components/ui/confirm-delete-modal.blade.php`
  - uso como `<x-ui.confirm-delete-modal />`.
- Upload usa componente existente:
  - `resources/views/components/ui/input-file.blade.php`
  - con `drag-drop`, `multiple`, `wire:model`.

Ruta de almacenamiento aprobada:

```text
documentos/solicitudes/{year}/{solicitud_id}/archivo.pdf
```

Disco:

```php
$disk = config('filesystems.default', 'local');
```

Se mantiene `FILESYSTEM_DISK=local`; no se creó disco privado adicional.

Descarga:

- Controlador autorizado `SolicitudDocumentoDownloadController`.
- Usa `$this->authorize('view', $documento->solicitud)`.
- Se agregó trait `AuthorizesRequests` al controlador.
- No se exponen documentos por disco público.

Errores resueltos en documentos:

1. `Unable to retrieve the file_size`:
   - Causa: se obtenía tamaño/mime después de mover archivo temporal Livewire.
   - Solución: capturar `originalName`, `mimeType`, `size` antes de `store()`.

2. `Call to undefined method SolicitudDocumentoDownloadController::authorize()`:
   - Solución: agregar `use AuthorizesRequests;` en controlador.

3. `Multiple root elements detected`:
   - Causa: modal colocado fuera del `<div class="space-y-6">` raíz.
   - Solución: colocar `<x-ui.confirm-delete-modal />` dentro del único root.

4. Confirmación de eliminación:
   - Se reemplazó `wire:confirm` por modal homogéneo SIIAA.
   - En `SolicitudesEdit` se controla con:
     - `public ?int $documentoEliminarId = null;`
     - `public bool $confirmDeleteModal = false;`
     - `confirmarEliminarDocumento()`
     - `cancelarEliminarDocumento()`
     - `eliminarDocumento()` sin ID directo desde el botón.

---

## 5. Commits recientes hechos vía GitHub

Commits realizados en `main` durante este bloque:

- `8884715` — Usa confirmación modal para eliminar documentos de solicitudes.
- `1743d45` — Alinea modal de eliminación de documentos con componente UI.

Nota: después se corrigió localmente la ubicación del modal dentro del único root Livewire.

---

## 6. Estado funcional actual

Confirmado por pruebas del usuario:

- `SolicitudesEdit` carga correctamente.
- Paso 1 funciona.
- Visitante/requerimientos funcionan hasta donde se ha probado.
- Recursos funcionan.
- Documentos funcionan:
  - adjuntar
  - descargar
  - eliminar con modal UI
- Admin/SACAD sin identidad activa puede operar documentos sin falsear `owner_id`.

---

## 7. Pendiente explícito para continuar mañana

### 7.1 Bloque pendiente: Paso 4 Revisión y envío

Este bloque se propuso pero queda pendiente, **no implementado**.

Debe incluir:

1. Resumen de datos generales.
2. Resumen de visitante si tipo `VISITA`.
3. Resumen de recursos si `requiere_recursos`.
4. Resumen de documentos.
5. Advertencias no bloqueantes.
6. Validaciones bloqueantes antes del envío.
7. Envío formal con generación de folio.

### 7.2 Validación previa al envío pendiente

Crear método en `SolicitudesEdit`:

```php
protected function validarEnvio(): void
```

Reglas esperadas:

- Tipo de solicitud obligatorio.
- Si el tipo requiere motivo, `motivo_id` obligatorio.
- Si motivo es `OTRO`, `motivo_otro` obligatorio.
- Si tipo es `VISITA`, debe existir visitante y campos mínimos.
- Si `requiere_recursos`, debe existir al menos un recurso.
- Documentos no bloquean envío por ahora; solo advertencia.
- Seguro UNAM podrá exigir PDFs en una etapa posterior si se decide.

### 7.3 Ajustar `enviarSolicitud()` pendiente

Debe llamar:

```php
$this->resetErrorBag('envio');
$this->validarEnvio();
```

antes de invocar:

```php
$solicitudService->enviar($this->solicitud, $identityId);
```

### 7.4 Revisar `SolicitudService::enviar()` pendiente

Verificar que:

- Asigne folio solo al enviar.
- Folio formato `YYYY/NNN`.
- Drafts no tienen folio.
- Setea:
  - `estatus_id` a `SENV`
  - `submitted_at`
  - `submitted_by`
  - `updated_by`
- Permite envío por owner o por `solicitudes.manage`.
- No rompe si admin no tiene identidad activa.

### 7.5 Paso 4 Blade pendiente

Agregar sección `@if ($paso === 4)` con:

- Estado actual.
- Errores `@error('envio')`.
- Datos generales.
- Documentos.
- Recursos.
- Visitante.
- Advertencia de envío.
- Botón `Enviar solicitud` bajo `@can('send', $solicitud)`.

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
php -l app/Policies/SolicitudPolicy.php
php artisan optimize:clear
```

Revisar referencias peligrosas:

```bash
grep -R "activeIdentityLinkId\|cancel_reason\|reject_reason\|wire:confirm" -n app resources database routes
```

Revisar componente modal:

```bash
grep -R "confirm-delete-modal" -n resources/views
```

Revisar documentos guardados:

```bash
find storage/app/documentos/solicitudes -type f | head
```

---

## 9. Prioridad siguiente

Al continuar:

1. No reabrir diseño de documentos salvo bug.
2. Iniciar con Paso 4 `Revisión y envío`.
3. Revisar primero `SolicitudService::enviar()` antes de tocar Blade si hay dudas sobre folio.
4. Mantener componentes UI existentes; evitar crear duplicados.
5. Todo cambio de eliminación debe usar `x-ui.confirm-delete-modal`.
