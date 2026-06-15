# Módulo de Solicitudes — SIIAA Laravel 13

## 1. Resumen ejecutivo

El módulo de Solicitudes del SIIAA Laravel 13 es un rediseño funcional del módulo existente en SIIAA_10. Su objetivo es permitir el registro, edición, consulta, envío, revisión operativa, seguimiento administrativo, archivo lógico y futura migración histórica de solicitudes institucionales relacionadas con permisos de ausencia, recursos, visitantes y apoyos para estudiantes.

El módulo debe adaptarse a la arquitectura nueva del SIIAA:

* Laravel 13.
* Livewire.
* Alpine.js.
* Tailwind CSS.
* Componentes Blade UI propios del SIIAA.
* Catálogos institucionales mediante `catalogos_items`.
* Capa de identidad institucional basada en `IdentityLink`.
* Permisos simplificados.
* Separación clara de responsabilidades.
* Flujo de expediente institucional, no CRUD plano.

El diseño prioriza uso diario, velocidad de captura, mantenimiento, trazabilidad y consistencia institucional.

---

## 2. Objetivo del módulo

El módulo permitirá registrar y gestionar estos tipos de solicitud:

| Clave      |  ID | Uso funcional                                        |
| ---------- | --: | ---------------------------------------------------- |
| `AUS_REC`  | 554 | Permiso de ausencia con recursos                     |
| `AUSENCIA` | 517 | Permiso de ausencia sin recursos                     |
| `SOLOREC`  | 518 | Solo recursos                                        |
| `VISITA`   | 325 | Visitante                                            |
| `ESOLREC`  | 516 | Solicitud de recursos al IRyA para estudiantes SIIAP |

No existe tipo de solicitud `OTRO`.
`OTRO` aplica únicamente como motivo de solicitud.

El módulo debe permitir:

* Que un propietario registre y gestione sus solicitudes.
* Que un usuario con permiso autorizado cree solicitudes a nombre de otra identidad institucional.
* Que un perfil operativo revise solicitudes y edite campos específicos.
* Que se registren recursos, documentos, visitantes y requerimientos asociados.
* Que se conserve histórico mediante archivo lógico.
* Que posteriormente se migren datos históricos desde SIIAA_10.

---

## 3. Principios de diseño

### 3.1 Usabilidad

* Evitar formularios excesivos.
* Evitar campos innecesarios.
* Usar campos libres cuando un catálogo pueda volverse barrera operativa.
* Subir documentos de forma natural mediante drag & drop o selector tradicional.
* No obligar a clasificar documentos.
* Usar catálogos solo donde aporten control institucional.
* Las validaciones dependen del tipo de solicitud y motivo.

### 3.2 Mantenibilidad

* No crear tabla independiente de observaciones en esta versión.
* `observaciones_sacad` y `observaciones_administracion` viven directamente en `solicitudes`.
* Visitantes sí se separan en tabla hija porque tienen lógica propia.
* Recursos conservan estructura cercana al sistema anterior porque ya funciona operativamente.
* Requerimientos se guardan como parte del expediente principal de solicitud.

### 3.3 Consistencia institucional

Todas las referencias de propiedad, autoría y auditoría deben resolverse mediante `identity_links.id`, no directamente mediante `users.id`.

Aplica a:

* `owner_id`
* `created_by`
* `updated_by`
* `archived_by`
* `politica_aceptada_by`
* `uploaded_by`
* `tutor_id`
* campos equivalentes de auditoría

---

## 4. Identidad institucional

### 4.1 Propietario de la solicitud

Cada solicitud tiene un propietario institucional:

```text
solicitudes.owner_id -> identity_links.id
```

El propietario puede ser:

* Persona IRyA.
* Estudiante SIIAP.
* Otra identidad institucional futura.

### 4.2 Creación propia y creación a nombre de otra identidad

| Caso                             | Regla                                                |
| -------------------------------- | ---------------------------------------------------- |
| Crear para mí                    | Disponible si el usuario tiene `IdentityLink` válida |
| Crear a nombre de otra identidad | Disponible si el usuario tiene `solicitudes.manage`  |

Cuando un gestor crea una solicitud a nombre de otra identidad:

```text
owner_id = identidad representada
created_by = identidad activa del gestor, si existe
updated_by = identidad activa del gestor, si existe
```

Si el gestor crea una solicitud para sí mismo:

```text
owner_id = identidad propia
created_by = identidad propia
updated_by = identidad propia
```

Si un usuario admin/superadmin no tiene identidad activa, debe poder crear a nombre de otro si tiene `solicitudes.manage`, pero debe quedar claro en auditoría que no existe `IdentityLink` activa del actor.

---

## 5. Permisos

### 5.1 Permisos principales

| Permiso              | Alcance                                                                                                  |
| -------------------- | -------------------------------------------------------------------------------------------------------- |
| `solicitudes.access` | Crear, ver, editar y borrar solicitudes propias según estado                                             |
| `solicitudes.review` | Ver todas las solicitudes y editar campos específicos de revisión/operación                              |
| `solicitudes.manage` | Gestión completa: ver todo, crear a nombre de otra identidad, editar, cambiar estados, borrar y archivar |

### 5.2 Estudiantes asociados

Permiso específico:

```text
estudiantes-asociados.manage
```

Los usuarios generales no administran libremente el mini módulo, pero sí pueden buscar, seleccionar o crear estudiante asociado desde el flujo de solicitud de visitante cuando aplique.

---

## 6. Estados de solicitud

Estados funcionales:

| Clave       | Estado             |
| ----------- | ------------------ |
| `BORRADOR`  | Borrador           |
| `SENV`      | Enviada            |
| `APRCI`     | Aprobada en CI     |
| `RECI`      | Rechazada en CI    |
| `TRPAG`     | En trámite de pago |
| `PAG`       | Pagada             |
| `CLO`       | Cerrada            |
| `CANCELADA` | Cancelada          |

Estado histórico:

| Clave    | Estado          |
| -------- | --------------- |
| `REVCIC` | En revisión CIC |

`REVCIC` se conserva por compatibilidad histórica, pero no forma parte del flujo normal nuevo.

No se usarán como estados funcionales nuevos:

```text
EN_REVISION
OBSERVADA
```

Las correcciones se manejarán mediante:

```text
observaciones_sacad
observaciones_administracion
```

sin cambiar a un estado intermedio.

### 6.1 Edición por propietario

| Estado      | Editable por propietario |
| ----------- | ------------------------ |
| `BORRADOR`  | Sí                       |
| `SENV`      | Sí                       |
| `APRCI`     | No                       |
| `RECI`      | No                       |
| `TRPAG`     | No                       |
| `PAG`       | No                       |
| `CLO`       | No                       |
| `CANCELADA` | No                       |

El propietario solo puede transicionar:

```text
BORRADOR -> SENV
```

Los demás cambios de estado quedan para `solicitudes.manage`.

---

## 7. Motivos de solicitud

Catálogo:

```text
SOLMOT
```

| Clave    |  ID | Motivo                   |
| -------- | --: | ------------------------ |
| `EVACAD` | 323 | Evento académico         |
| `ESTT`   | 324 | Estancia de trabajo      |
| `ACTDIV` | 328 | Actividad de divulgación |
| `TCAMP`  | 329 | Trabajo de campo         |
| `OTRO`   | 555 | Otro / especificar       |

Todos los motivos aplican a todos los tipos excepto `VISITA`, que tiene lógica propia mediante `solicitudes_visitantes`.

Cuando el motivo sea `OTRO`, el campo `motivo_otro` es obligatorio.
`informacion_adicional` queda como campo opcional para contexto extra.

---

## 8. Flujo general

El módulo conserva un flujo de cuatro pasos dentro de un solo componente Livewire principal:

```text
Paso 1. Datos generales
Paso 2. Recursos
Paso 3. Documentos
Paso 4. Revisión y envío
```

### 8.1 Create

`SolicitudesCreate` debe crear un borrador mínimo y redirigir a edición.

Debe capturar lo mínimo necesario:

* Propietario, si el usuario tiene `solicitudes.manage`.
* Tipo de solicitud.
* Estado inicial `BORRADOR`.

No debe convertirse en formulario completo.

### 8.2 Edit

`SolicitudesEdit` es el expediente principal.
Debe ser un solo componente Livewire, no componentes separados por paso.

Métodos internos esperados:

```php
guardarPaso1()
guardarVisitante()
guardarRequerimientos()
guardarRecursos()
subirDocumentos()
validarEnvio()
enviarSolicitud()
```

---

## 9. Paso 1 — Datos generales

En este paso se capturan:

* Propietario.
* Tipo de solicitud.
* Motivo, excepto visitante.
* Motivo otro, si aplica.
* Fechas.
* País.
* Nombre del evento.
* Tipo de presentación.
* Institución.
* Anfitrión.
* Lugar.
* Tutor, si aplica.
* Información adicional.
* Seguro UNAM, si aplica.
* Visitante, si el tipo es `VISITA`.
* Requerimientos, si el tipo es `VISITA`.

---

## 10. Paso 2 — Recursos

Este paso aparece solo cuando:

```text
requiere_recursos = true
```

| Tipo       | Requiere recursos |
| ---------- | ----------------- |
| `AUS_REC`  | Sí                |
| `AUSENCIA` | No                |
| `SOLOREC`  | Sí                |
| `ESOLREC`  | Sí                |
| `VISITA`   | Seleccionable     |

Si `requiere_recursos = true`, antes de enviar debe existir:

* Al menos un recurso.
* Política de recursos aceptada.

---

## 11. Paso 3 — Documentos

Los documentos son flexibles:

* El paso siempre existe.
* No bloquea el envío si no hay documentos.
* Se muestra advertencia si no se adjuntan archivos.
* SACAD o administración pueden solicitar documentación posteriormente mediante observaciones.
* No se agregan campos adicionales por archivo.
* Se recomienda usar nombres descriptivos.

Ruta aprobada:

```text
documentos/solicitudes/{year}/{solicitud_id}/
```

Ejemplo:

```text
documentos/solicitudes/2026/125/
```

La ruta no depende del folio.

---

## 12. Paso 4 — Revisión y envío

Debe mostrar resumen y validar:

* Propietario válido.
* Tipo válido.
* Motivo válido, excepto visitante.
* Motivo otro si aplica.
* Datos requeridos por tipo y motivo.
* Recursos si `requiere_recursos = true`.
* Política aceptada si aplica.
* Visitante completo si aplica.
* Documentos solo como advertencia.

Al enviar:

```text
estatus = SENV
submitted_at = now()
submitted_by = identidad activa
folio = YYYY/NNN
```

---

## 13. Folio institucional

El folio se asigna hasta el envío formal.

Mientras la solicitud esté en borrador:

```text
folio = null
folio_year = null
folio_number = null
```

Formato:

```text
YYYY/NNN
```

Ejemplo:

```text
2026/001
2026/002
```

No se reutilizan folios aunque la solicitud sea cancelada, cerrada, archivada o borrada administrativamente.

---

## 14. Tabla principal `solicitudes`

Estructura conceptual:

```text
id
folio
folio_year
folio_number
owner_id
created_by
updated_by
tipo_solicitud_id
motivo_id
motivo_otro
requiere_recursos
fecha_inicio
fecha_fin
pais_id
nombre_evento
tipo_presentacion
institucion
anfitrion
lugar
tutor_id
informacion_adicional
requiere_seguro_unam
seguro_unam_beneficiario
observaciones_sacad
observaciones_administracion
estatus_id
submitted_at
submitted_by
approved_at
approved_by
rejected_at
rejected_by
closed_at
closed_by
cancelled_at
cancelled_by
politica_aceptada_at
politica_aceptada_by
politica_version
archived_at
archived_by
archive_reason
created_at
updated_at
```

Para solicitudes generales no visitantes se usará:

```text
institucion
```

como campo libre.
No se usará `institucion_id` en `solicitudes`.

`tutor_id` apunta a:

```text
identity_links.id
```

---

## 15. Recursos

Tabla:

```text
solicitudes_recursos
- id
- solicitud_id
- origen_id
- proyecto_id
- proyecto_nombre
- dias_n
- dias_i
- cuota
- cuota_divisa
- avion
- avion_divisa
- otro
- otro_divisa
- informacion_adicional
- created_by
- updated_by
- created_at
- updated_at
```

Campos monetarios:

```text
decimal(12,2)
```

Catálogo de orígenes:

```text
C_OREC
```

| Clave      |  ID | Origen             |
| ---------- | --: | ------------------ |
| `R_PI`     | 286 | Partida individual |
| `R_PAPIIT` | 287 | Proyecto PAPIIT    |
| `R_PAPIME` | 288 | PAPIME             |
| `CONV`     | 327 | Convenio           |
| `R_IRYA`   | 522 | Recursos del IRyA  |
| `SECIHT`   | 539 | SECIHTI            |

Catálogo de divisas:

```text
DIVISAS
```

| Clave |  ID | Divisa          |
| ----- | --: | --------------- |
| `MXN` | 519 | Pesos Mexicanos |
| `USD` | 520 | Dólares EEUU    |
| `EUR` | 521 | Euros           |

`MXN` debe ser default.

---

## 16. Visitantes

Tabla:

```text
solicitudes_visitantes
- id
- solicitud_id
- tipo_visitante_id
- estudiante_asociado_id
- nombre
- apellidos
- email
- pais_id
- institucion_id
- institucion
- lugar
- fecha_inicio
- fecha_fin
- created_by
- updated_by
- created_at
- updated_at
```

Una solicitud de visitante tiene un solo visitante principal.
Si se requiere más de un visitante, se crean solicitudes separadas.

Para visitantes sí se usa estructura flexible:

```text
institucion_id nullable
institucion nullable
```

Catálogo:

```text
C_SOLTVIS
```

| Clave    |  ID | Tipo                     |
| -------- | --: | ------------------------ |
| `VACAD`  | 523 | Académico / investigador |
| `VEASOC` | 524 | Estudiante asociado      |
| `VEST`   | 525 | Estudiante no asociado   |
| `VOTRO`  | 526 | Otro                     |

Si el visitante es estudiante asociado:

```text
estudiante_asociado_id
```

es obligatorio.

---

## 17. Requerimientos de visita

Decisión vigente:

Los requerimientos pertenecen directamente al expediente principal de la solicitud.

Aunque funcionalmente solo aplican a solicitudes de tipo `VISITA`, la relación técnica debe colgar de:

```text
solicitudes.id
```

mediante:

```text
solicitudes_requerimientos.solicitud_id
```

No usar:

```text
solicitud_visitante_id
```

Tabla vigente:

```text
solicitudes_requerimientos
- id
- solicitud_id
- requerimiento_id
- created_by
- updated_by
- created_at
- updated_at
```

Reglas:

* Se muestran solo cuando el tipo de solicitud es `VISITA`.
* Son opcionales.
* Se capturan como checkboxes.
* No hay opción `Otro`.
* No hay campo de información adicional.
* Una visita puede no tener requerimientos.

Catálogo:

```text
VIS_REQ
```

| Clave      |  ID | Requerimiento                               |
| ---------- | --: | ------------------------------------------- |
| `REQ_OF`   | 279 | Requiere oficina                            |
| `REQ_CCOP` | 280 | Requiere cuenta de cómputo                  |
| `REQ_ECOP` | 281 | Requiere equipo de cómputo                  |
| `REQ_ACER` | 282 | Requiere acceso al acervo                   |
| `D_COLOQ`  | 283 | Tiene disponibilidad para impartir coloquio |

---

## 18. Estudiantes asociados

Los estudiantes asociados trabajan ligados a solicitudes de visitante, pero también existen como mini módulo administrativo.

Tablas:

```text
estudiantes_asociados
estudiantes_asociados_ingresos
```

Reglas:

* Usuario general puede buscar estudiante asociado desde solicitud.
* Usuario general puede crear estudiante asociado desde solicitud cuando aplique.
* Usuario general puede registrar ingreso desde solicitud.
* Admin puede administrar el mini módulo completo.
* Estudiante asociado no puede solicitar recursos directamente.
* Si un estudiante asociado requiere recursos, la solicitud debe hacerla su tutor o responsable institucional.
* `ESOLREC` es exclusiva para estudiantes SIIAP adscritos al IRyA.

---

## 19. Observaciones

No se crea tabla `solicitudes_observaciones` en esta versión.

Campos directos:

```text
observaciones_sacad
observaciones_administracion
```

`solicitudes.review` puede editar inicialmente:

```text
observaciones_sacad
observaciones_administracion
```

Los cambios de estado quedan reservados a `solicitudes.manage`.

---

## 20. Archivo lógico e histórico

No se usarán soft deletes.

El archivo lógico se maneja con:

```text
archived_at
archived_by
archive_reason
```

Reglas:

* El listado normal excluye archivadas.
* Solo `solicitudes.manage` puede archivar.
* Archivar no cambia el estado.
* Una solicitud puede estar `CLO` y archivada.
* Una solicitud puede estar `PAG` y archivada.

---

## 21. Borrado real

No se usan soft deletes.

Propietario puede borrar físicamente solo si:

* Es su solicitud.
* Está en `BORRADOR`.
* No ha sido enviada.
* No tiene trámite institucional iniciado.

`solicitudes.manage` puede borrar más casos cuando corresponda institucionalmente.

Al borrar deben eliminarse:

* Recursos.
* Visitante.
* Requerimientos.
* Documentos.
* Archivos físicos asociados.

El borrado debe tener confirmación fuerte.

---

## 22. Listados y rendimiento

El listado principal debe cargar solo:

* Solicitud.
* Tipo.
* Estado.
* Motivo.
* Propietario.
* Fechas principales.

No debe cargar por defecto:

* Documentos.
* Recursos completos.
* Visitante completo.
* Requerimientos.

Filtros recomendados:

* Búsqueda.
* Año.
* Estado.
* Tipo.
* Motivo.
* Activas / archivadas / todas.

---

## 23. Componentes Livewire

Componentes principales:

```text
SolicitudesIndex
SolicitudesCreate
SolicitudesEdit
SolicitudesShow
SolicitudesReview
EstudiantesAsociadosIndex
EstudiantesAsociadosEdit
```

`SolicitudesEdit` debe ser un solo componente para los cuatro pasos.

---

## 24. Servicio principal

Servicio:

```text
SolicitudService
```

Responsabilidades:

* Crear borrador.
* Guardar datos generales.
* Guardar visitante.
* Sincronizar requerimientos.
* Guardar recursos.
* Aceptar política.
* Validar envío.
* Generar folio al enviar.
* Enviar solicitud.
* Cambiar estado.
* Archivar.
* Borrar.

---

## 25. Correos y notificaciones

Al enviar solicitud, el sistema debe notificar a:

* Solicitante.
* Grupo de revisión o instancias configuradas.
* Visitante, si aplica y existe correo.
* Otros destinatarios configurables según tipo o política institucional.

La generación de correo debe ocurrir después de confirmar la transacción de envío.
Idealmente mediante cola.

---

## 26. Migración histórica desde SIIAA_10

La migración histórica no forma parte de la primera etapa.

Se hará después de:

1. Crear el módulo nuevo.
2. Probarlo.
3. Estabilizarlo.
4. Validar reglas de identidad y catálogos.
5. Diseñar y ejecutar el importador.

Comando futuro:

```bash
php artisan siiaa10:import-solicitudes
```

Para solicitudes históricas sin `IdentityLink`, el comportamiento base será estricto:

* No migrar.
* Generar CSV de omitidos/inconsistencias.

---

## 27. Convenciones técnicas

Catálogos:

```text
catalogos_items
```

No usar:

```text
catalogo_items
```

Variables de catálogos con prefijo:

```text
c_
```

Ejemplos:

```php
$c_tipos_solicitud
$c_motivos
$c_estatus
$c_paises
$c_divisas
$c_requerimientos
```

Validaciones:

* Usar `Rule::exists` preferentemente contra `catalogos_items`.
* Validar claves funcionales cuando aplique.
* Evitar IDs mágicos dentro de Livewire cuando pueda resolverse por catálogo/servicio/helper.

---

## 28. Orden recomendado de desarrollo

1. Migraciones y campos faltantes.
2. Modelos y relaciones.
3. Catálogos/helpers de claves funcionales.
4. Policies/permisos.
5. `SolicitudService`.
6. `SolicitudesIndex`.
7. `SolicitudesCreate` como borrador mínimo.
8. `SolicitudesEdit` Paso 1.
9. Visitantes y estudiantes asociados.
10. Requerimientos de visita.
11. Recursos.
12. Documentos.
13. Paso 4: envío, folio y correos.
14. `SolicitudesShow`.
15. Observaciones, revisión administrativa y estados.
16. Pruebas operativas.
17. Importador histórico desde SIIAA_10.

---

## 29. Estado actual detectado en código

Puntos alineados:

* `SolicitudRequerimiento` usa `solicitud_id`.
* `Solicitud::requerimientos()` relaciona por `solicitud_id`.
* `SolicitudService::sincronizarRequerimientos()` recibe `Solicitud`, no `SolicitudVisitante`.
* `SolicitudServiceInterface` documenta que los requerimientos pertenecen a solicitud, no al visitante.

Puntos a corregir:

* Constantes de estados en `Solicitud` deben alinearse con claves reales `SENV`, `APRCI`, `RECI`, `TRPAG`, `PAG`, `CLO`, `CANCELADA`.
* `puedeEditarPropietario()` debe permitir edición en `BORRADOR` y `SENV`.
* `SolicitudesCreate` debe reducirse a borrador mínimo.
* `SolicitudesEdit` debe convertirse en expediente por pasos.
* `SolicitudesEdit` debe incorporar campos faltantes de Paso 1.
* Visitante debe integrarse dentro del Paso 1 cuando tipo sea `VISITA`.
* Requerimientos deben mostrarse como checkboxes solo para `VISITA`.

---

## 30. Resultado esperado

El módulo de Solicitudes debe ser:

* Más limpio que el módulo anterior.
* Alineado con identidad institucional.
* Mantenible.
* Rápido en listados.
* Flexible para usuarios reales.
* Seguro en propiedad y auditoría.
* Preparado para revisión, pagos, proyectos, observaciones e importación histórica.
