# Índice de contexto SIIAA

Este directorio contiene documentos de referencia para mantener continuidad de diseño, reglas de negocio y estilo de desarrollo del proyecto **SIIAA Laravel 13**.

La fuente viva del código es el repositorio:

```text
https://github.com/iryaunamdev/siiaa_laravel_13
```

## Documentos disponibles

| Área / módulo | Archivo | Tipo | Tamaño aproximado | Fecha local | Uso recomendado |
|---|---|---:|---:|---:|---|
| Índice de contexto | `INDEX.md` | MD | 2093 bytes | Jun 15 13:19 | Punto de entrada documental |
| Capa de identidad institucional | `capa_de_identidad_institucional-SIIAA_Laravel_13.md` | MD | 9517 bytes | Jun 15 13:31 | Identidad, autoría, ownership y permisos por identidad |
| Consejo Interno | `modulo_consejo_interno-SIIAA_Laravel_13.md` | MD | 23874 bytes | Jun 15 13:41 | Diseño funcional aprobado del módulo CI |
| Solicitudes | `modulo_solicitudes-SIIAA_Laravel_13.md` | MD | 20993 bytes | Jun 15 13:35 | Diseño funcional y técnico aprobado del módulo Solicitudes |
| Sistema base | `resumen_ejecutivo_sistema_base-SIIAA_Laravel_13.md` | MD | 38561 bytes | Jun 15 13:32 | Arquitectura general, convenciones y decisiones base |

## Rutas exactas

```text
.context/INDEX.md
.context/capa_de_identidad_institucional-SIIAA_Laravel_13.md
.context/modulo_consejo_interno-SIIAA_Laravel_13.md
.context/modulo_solicitudes-SIIAA_Laravel_13.md
.context/resumen_ejecutivo_sistema_base-SIIAA_Laravel_13.md
```

## Prioridad de consulta

Antes de modificar o diseñar un módulo, revisar primero el documento correspondiente en este directorio.

Orden recomendado:

1. Documento específico del módulo o capa.
2. Código vigente en GitHub `main`.
3. Documentación compacta operativa del proyecto, si existe.
4. Conversación actual.

## Uso por módulo o capa

### Sistema base

Consultar:

```text
.context/resumen_ejecutivo_sistema_base-SIIAA_Laravel_13.md
```

Usar como referencia para arquitectura general, convenciones del proyecto, estilo SIIAA/IRyA, estructura base, criterios de UX y decisiones globales.

### Capa de identidad institucional

Consultar:

```text
.context/capa_de_identidad_institucional-SIIAA_Laravel_13.md
```

Usar como referencia obligatoria para cualquier módulo que necesite:

- identidad activa;
- `identity_links`;
- autoría;
- ownership;
- trazabilidad;
- permisos dependientes de identidad institucional.

### Solicitudes

Consultar:

```text
.context/modulo_solicitudes-SIIAA_Laravel_13.md
```

El módulo Solicitudes debe tratarse como expediente institucional por pasos/secciones, no como CRUD simple.

Este documento es la fuente vigente para tipos, estados, reglas de negocio, flujo, recursos, visitantes, documentos, folios, permisos y notificaciones.

### Consejo Interno

Consultar:

```text
.context/modulo_consejo_interno-SIIAA_Laravel_13.md
```

Usar como referencia para reuniones, puntos, evaluaciones, participantes, documentos, actas, permisos y reglas de acceso.

## Nota importante

Ya no se usan PDFs ni DOCX como fuente principal de contexto dentro de `.context/`.

Los documentos vigentes son archivos Markdown (`.md`) versionados en GitHub.
