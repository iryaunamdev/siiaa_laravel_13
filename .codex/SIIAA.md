# SIIAA - Memoria operativa

## Fuente de verdad

- Repositorio: `iryaunamdev/siiaa_laravel_13`.
- Rama base: `main`.
- Antes de proponer o modificar codigo, revisar los archivos actuales en GitHub.
- Cuando falten datos de diseno, revisar `.context/`.
- Los documentos de `.context/` contienen resumenes ejecutivos por modulo o vision general y deben prevalecer sobre recuerdos incompletos.

## Estilo de programacion SIIAA / IRyA

- Laravel 13 + Livewire + Alpine.js + Tailwind CSS.
- Usar documentacion oficial como base.
- Evitar CRUDs planos cuando el modulo esta definido como expediente institucional.
- Preferir secciones funcionales, servicios por dominio y componentes claros.
- No duplicar headers si el layout dashboard ya los aporta.
- Mantener codigo sobrio, explicito y con comentarios solo cuando aclaran reglas de negocio.

## Convenciones generales

- Tabla correcta de catalogos: `catalogos_items`.
- Variables de catalogos con prefijo `c_`.
- Autoría, propiedad y auditoria institucional mediante `identity_links.id`.
- Helpers reales de identidad actual:
  - `currentIdentityId()`.
  - `currentIdentityType()`.
  - `currentIdentity()`.
- No usar `activeIdentityLinkId()` en el proyecto actual.

## Solicitudes

El modulo Solicitudes debe tratarse como expediente institucional por pasos, no como CRUD simple.

Flujo conceptual:

1. Crear borrador.
2. Datos generales y propietario.
3. Visitante principal, cuando aplique.
4. Requerimientos de visita, cuando aplique.
5. Recursos, cuando aplique.
6. Documentos.
7. Revision y envio.

Reglas de propiedad:

- `owner_id` es la identidad propietaria de la solicitud.
- `created_by` y `updated_by` representan la identidad que actua, si existe.
- `solicitudes.manage` puede crear a nombre de otra identidad.
- Para `solicitudes.manage`, elegir propietario es obligatorio al crear.
- En edicion, el propietario debe mostrarse siempre a usuarios con `solicitudes.manage`.
- El propietario solo es editable si la solicitud fue creada por administracion a nombre de otra identidad.
- Si la solicitud fue creada por el propietario, admin puede ver el owner, pero no modificarlo.

Visitante:

- Una solicitud de visita tiene un solo visitante principal.
- El visitante forma parte del expediente.
- Modelo vigente: `App\Models\Solicitudes\SolicitudVisitante`.

Requerimientos:

- Los requerimientos pertenecen al expediente principal de solicitud, no al visitante.
- Modelo vigente: `App\Models\Solicitudes\SolicitudRequerimiento`.
- Tabla: `solicitudes_requerimientos`.

## Regla de cierre de cambios en GitHub

Cuando se hagan cambios directos en GitHub, cerrar siempre con:

1. Objetivo corregido.
2. Archivos modificados.
3. Cambios aplicados por archivo.
4. Commits generados.
5. Reglas de negocio afectadas o confirmadas.
6. Pasos locales que debe ejecutar el usuario.
7. Pruebas especificas.
8. Pendientes o riesgos detectados.

Resumen puntual, claro y sin paja.
