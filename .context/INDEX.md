# Índice de contexto SIIAA

Este directorio contiene documentos de referencia para mantener continuidad de diseño, reglas de negocio y estilo de desarrollo del proyecto SIIAA Laravel 13.

## Documentos disponibles

| Área / módulo | Archivo | Tipo | Tamaño aproximado | Fecha local | Uso recomendado |
|---|---|---:|---:|---:|---|
| Capa de identidad institucional | `Capa_de_identidad_institucional_en_SIIAA_Laravel_13.pdf` | PDF | 282984 bytes | Jun 10 12:05 | Contexto de identidad |
| Consejo Interno | `Modulo Consejo Interno - SIIAA Laravel 13.docx` | DOCX | 24032 bytes | Jun 8 10:38 | Contexto de módulo |
| Solicitudes | `Modulo de Solicitudes - SIIAA Laravel 13.md` | MD | texto | Actualizado | **Fuente preferente para desarrollo** |
| Solicitudes | `Modulo de Solicitudes - SIIAA Laravel 13.pdf` | PDF | 144027 bytes | Jun 4 12:56 | Fuente original / respaldo |
| Visión general SIIAA | `Resumen_ejecutivo_del_SIIAA_Laravel_13.pdf` | PDF | 743980 bytes | Jun 4 12:57 | Contexto general |

## Rutas exactas

```text
.context/Capa_de_identidad_institucional_en_SIIAA_Laravel_13.pdf
.context/Modulo Consejo Interno - SIIAA Laravel 13.docx
.context/Modulo de Solicitudes - SIIAA Laravel 13.md
.context/Modulo de Solicitudes - SIIAA Laravel 13.pdf
.context/Resumen_ejecutivo_del_SIIAA_Laravel_13.pdf
```

## Uso esperado

Antes de modificar o diseñar un módulo, revisar primero el documento correspondiente en este directorio.

Prioridad de consulta:

1. Reglas explícitas del documento de contexto del módulo.
2. Código vigente en GitHub `main`.
3. `.codex/SIIAA.md` como memoria operativa compacta.
4. Conversación actual.

## Nota para Solicitudes

El módulo Solicitudes debe tratarse como expediente institucional por pasos/secciones, no como CRUD simple.

Para desarrollo de Solicitudes, consultar primero la versión Markdown porque es legible directamente por herramientas de trabajo:

```text
.context/Modulo de Solicitudes - SIIAA Laravel 13.md
```

La versión PDF queda como respaldo original:

```text
.context/Modulo de Solicitudes - SIIAA Laravel 13.pdf
```
