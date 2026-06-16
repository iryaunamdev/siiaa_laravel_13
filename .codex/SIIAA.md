# SIIAA - Memoria operativa

Fecha de actualización: 2026-06-16
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
- Evitar CRUDs planos cuando el módulo está definido como expediente institucional.
- Preferir secciones funcionales, servicios por dominio, componentes UI reutilizables y código explícito.
- No duplicar headers si el layout dashboard ya los aporta.
- Mantener código sobrio; comentarios solo cuando aclaren reglas de negocio.
- Usar componentes UI existentes antes de crear nuevos.
- Confirmaciones de eliminación deben usar componentes modales homogéneos, no `wire:confirm`, cuando ya exista componente institucional.

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

## 4. Módulo Solicitudes — diseño aprobado

### 4.1 Catálogos

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

### 4.2 Permisos

- `solicitudes.access`: acceso normal del propietario.
- `solicitudes.review`: revisión SACAD/CI; permite ver todas y editar observaciones de revisión/administración.
- `solicitudes.manage`: gestión completa; crear en representación de otra identidad, cambiar estados, archivar/eliminar y enviar administrativamente.

### 4.3 Flujo aprobado

- `SolicitudesCreate`: creación mínima de borrador.
  - `owner_id` si puede crear a nombre de otra identidad.
  - `tipo_solicitud_id`.
  - Redirige a edición del expediente.
- `SolicitudesEdit`: expediente por pasos.
  1. Datos generales + visitante/requerimientos si aplica.
  2. Recursos si `requiere_recursos`.
  3. Documentos.
  4. Revisión/envío. **Pendiente.**

---

## 5. Autoría y operación administrativa

Regla aprobada:

```text
owner_id = identidad propietaria real de la solicitud.
created_by / updated_by / uploaded_by / submitted_by = identidad activa si existe.
Si el operador tiene solicitudes.manage y no tiene identidad activa, esos campos pueden ser null.
```

Helper interno usado en `SolicitudesEdit`:

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

`owner_id` no debe ser nullable.

---

## 6. Estado implementado y probado en Solicitudes

### 6.1 Limpieza de estados y observaciones

Hecho:

- Se eliminaron usos funcionales de `cancel_reason` y `reject_reason`.
- Cancelación escribe en `observaciones_administracion`.
- Rechazo CI/SACAD escribe en `observaciones_sacad`.
- Se corrigió uso de estados funcionales actuales.
- Se corrigió el uso de `currentIdentityId()`.

### 6.2 Creación mínima

Hecho:

- `SolicitudesCreate` crea borrador mínimo.
- Campos principales:
  - `owner_id` si `solicitudes.manage`.
  - `tipo_solicitud_id`.
- El resto se captura en `SolicitudesEdit`.

### 6.3 Paso 1: datos generales

Hecho:

- Campos:
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
- Catálogos:
  - `SOLTIPOS`
  - `SOLMOT`
  - `PAISES`
  - tutores por `identity_links` tipo `persona`.
- Consulta de tutores queda amplia temporalmente; después podrá restringirse a investigadores IRyA si se define relación exacta.

### 6.4 Visitante y requerimientos

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
- Requerimientos por checkboxes desde `VIS_REQ`.
- No hay requerimiento “Otro”.
- `requerimientosCatalogo()` en modelo `Solicitud` usa `belongsToMany` vía `solicitudes_requerimientos`.

### 6.5 Recursos

Hecho:

- Paso 2 activo solo si `requiere_recursos`.
- Múltiples bloques de recursos.
- Catálogos:
  - `C_OREC`
  - `DIVISAS`
- MXN como divisa default cuando exista en catálogo.
- Campos:
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

### 6.6 Documentos

Hecho y probado:

- Paso 3 de documentos funciona.
- Adjuntar documentos funciona.
- Descargar documentos funciona.
- Eliminar documentos funciona.
- Eliminación usa componente homogéneo `x-ui.confirm-delete-modal`.
- Upload usa componente existente `x-ui.input-file` con:
  - `drag-drop`
  - `multiple`
  - `wire:model`

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

Errores resueltos:

1. `Unable to retrieve the file_size`:
   - Causa: se obtenía tamaño/mime después de mover archivo temporal Livewire.
   - Solución: capturar `originalName`, `mimeType`, `size` antes de `store()`.

2. `Call to undefined method SolicitudDocumentoDownloadController::authorize()`:
   - Solución: agregar trait `AuthorizesRequests`.

3. `Multiple root elements detected`:
   - Causa: modal fuera del `<div class="space-y-6">` raíz.
   - Solución: colocar `<x-ui.confirm-delete-modal />` dentro del único root.

4. Confirmación de eliminación:
   - Se reemplazó `wire:confirm` por `x-ui.confirm-delete-modal`.
   - `SolicitudesEdit` controla:
     - `public ?int $documentoEliminarId = null;`
     - `public bool $confirmDeleteModal = false;`
     - `confirmarEliminarDocumento()`
     - `cancelarEliminarDocumento()`
     - `eliminarDocumento()` sin ID directo desde botón.

---

## 7. Commits recientes relevantes

- `8884715` — Usa confirmación modal para eliminar documentos de solicitudes.
- `1743d45` — Alinea modal de eliminación de documentos con componente UI.
- Corrección local posterior: modal dentro del único root Livewire.
- `ad0e912` — Elimina memoria de respaldo ubicada fuera de `.agents`.

---

## 8. Estado funcional actual

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

## 9. Pendiente explícito para continuar

### 9.1 Paso 4 Revisión y envío

Propuesto pero no implementado.

Debe incluir:

1. Resumen de datos generales.
2. Resumen de visitante si tipo `VISITA`.
3. Resumen de recursos si `requiere_recursos`.
4. Resumen de documentos.
5. Advertencias no bloqueantes.
6. Validaciones bloqueantes antes del envío.
7. Envío formal con generación de folio.

### 9.2 `validarEnvio()` pendiente

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
- Seguro UNAM podrá exigir PDFs en etapa posterior si se decide.

### 9.3 `enviarSolicitud()` pendiente

Debe llamar antes de enviar:

```php
$this->resetErrorBag('envio');
$this->validarEnvio();
```

Luego invocar:

```php
$solicitudService->enviar($this->solicitud, $identityId);
```

### 9.4 Revisar `SolicitudService::enviar()` pendiente

Verificar:

- Asignar folio solo al enviar.
- Folio formato `YYYY/NNN`.
- Borradores sin folio.
- Setear `estatus_id` a `SENV`.
- Setear `submitted_at`, `submitted_by`, `updated_by`.
- Permitir envío por propietario o por `solicitudes.manage`.
- No romper si admin no tiene identidad activa.

### 9.5 Blade del Paso 4 pendiente

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

## 10. Comandos útiles para retomar

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

## 11. Prioridad siguiente

Al continuar:

1. No reabrir diseño de documentos salvo bug.
2. Iniciar con Paso 4 `Revisión y envío`.
3. Revisar primero `SolicitudService::enviar()` antes de tocar Blade si hay dudas sobre folio.
4. Mantener componentes UI existentes; evitar crear duplicados.
5. Todo cambio de eliminación debe usar `x-ui.confirm-delete-modal`.
