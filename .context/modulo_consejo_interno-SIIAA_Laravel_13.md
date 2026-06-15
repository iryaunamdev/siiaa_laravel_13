Módulo Consejo Interno — SIIAA Laravel 13
1. Descripción general
El módulo Consejo Interno forma parte de la actualización del SIIAA a Laravel 13 y
tiene como objetivo administrar el proceso institucional mediante el cual el Consejo
Interno del IRyA revisa solicitudes, evalúa asuntos internos, registra resoluciones,
genera actas y publica documentos oficiales.
Este módulo se conecta directamente con el módulo Solicitudes, pero no lo
sustituye. Su función es servir como espacio de revisión, evaluación y resolución
institucional. Las solicitudes siguen viviendo en su propio módulo y Consejo Interno
únicamente las consulta, evalúa y resuelve cuando corresponda.
El módulo se diseña bajo la filosofía general del SIIAA: no como un CRUD simple, sino
como un expediente compuesto, organizado por secciones funcionales, con lógica
clara, flexible y mantenible.

2. Objetivo del módulo
El módulo permitirá a SACAD organizar reuniones del Consejo Interno, integrar
solicitudes enviadas, agregar otros asuntos, adjuntar documentos, permitir la
evaluación interna de los consejeros, registrar resoluciones finales y gestionar actas
institucionales.
Sus objetivos principales son:
•
•
•
•
•
•
•
•
•

Centralizar la preparación de reuniones del Consejo Interno.
Permitir la revisión de solicitudes enviadas.
Registrar evaluaciones internas de consejeros.
Registrar resoluciones finales por punto.
Vincular resoluciones de solicitudes con el módulo Solicitudes.
Crear, editar, evaluar y publicar actas.
Permitir consulta institucional de actas publicadas.
Mantener separación entre proceso interno del CI y documentos publicados.
Evitar flujos excesivamente complejos o automatismos difíciles de mantener.

3. Principios de diseño aplicados
El diseño sigue criterios generales definidos para SIIAA:

3.1 Expedientes compuestos
Las reuniones y actas no se diseñan como CRUDs simples. Se tratan como
expedientes compuestos.
Una reunión agrupa:
•
•
•
•
•
•
•

Datos generales.
Participantes.
Solicitudes.
Otros puntos.
Documentos.
Evaluaciones.
Resoluciones.

Un acta agrupa:
•
•
•
•
•

Datos generales.
Contenido.
Evaluaciones internas.
Publicación.
Vista institucional.

3.2 Simplicidad para el usuario
Aunque internamente existan relaciones, permisos, catálogos y servicios, la interfaz
debe ser clara y directa.
El usuario debe ver secciones funcionales, resúmenes, acciones explícitas y la
información necesaria para trabajar sin perder contexto.

3.3 Mostrar información y permitir filtrar
Se mantiene la regla:
Mostrar todo lo relevante y dar herramientas para filtrar, ordenar y buscar.
En especial, para solicitudes dentro de reuniones, el sistema debe mostrar todas las
solicitudes disponibles para Consejo Interno, incluyendo aquellas ya programadas
en otra reunión, pero indicándolas claramente y deshabilitando su selección.

3.4 Lógica explícita y mantenible
Se evitarán comportamientos mágicos, triggers complejos, observers excesivos,
flujos ocultos o automatizaciones difíciles de rastrear.
La lógica crítica debe vivir en servicios explícitos y reutilizables.

3.5 Flexibilidad administrativa
SACAD debe conservar capacidad de corrección posterior, incluso en reuniones
concluidas o actas publicadas, siempre apoyándose en la auditoría existente del
SIIAA.

3.6 Guardar solo cambios reales
El sistema debe evitar guardar si no existen cambios reales, para no generar
timestamps, auditoría o ruido operativo innecesario.

3.7 Documentación de código
El código debe documentarse de forma breve, clara y elegante. Se deben comentar
decisiones no obvias o lógica institucional relevante, evitando comentar lo evidente o
llenar los archivos de texto innecesario.

4. Alcance funcional
El módulo incluye:
•
•
•
•
•
•
•
•
•
•
•
•
•

Gestión de reuniones del Consejo Interno.
Selección de participantes.
Integración de solicitudes enviadas.
Captura de otros puntos.
Carga de documentos anexos.
Evaluación de puntos por consejeros.
Registro de resolución final por SACAD.
Integración de resolución con Solicitudes.
Gestión de actas.
Evaluación interna de actas.
Publicación de actas.
Consulta institucional de actas publicadas.
Vista imprimible para generar PDF desde navegador.

5. Fuera del alcance inicial
No se incluirá inicialmente:
•
•
•

Numeración automática de actas.
Folios de reuniones.
Versionado visible de actas.

•
•
•
•
•
•
•
•
•

Historial visual de evaluaciones o comentarios.
Comentarios generales de reunión.
Categorías complejas para otros puntos.
Bandeja especial para Administración.
Reasignación de solicitudes entre reuniones.
Módulo propio de integrantes del CI.
Generación automática de PDF en servidor.
Notificaciones por correo para reuniones, actas u otros puntos.
Flujos rígidos o asistentes tipo wizard.

6. Permisos
6.1 ci.access
Permite acceso base al módulo operativo de Consejo Interno.

6.2 ci.evaluate
Permite a miembros activos del Consejo Interno:
•
•
•
•

Ver reuniones donde participan.
Evaluar puntos.
Ver evaluaciones de otros participantes.
Evaluar actas en borrador según las reglas de acceso.

No permite editar reuniones, cambiar resoluciones finales ni ver reuniones donde no
participan.

6.3 ci.manage
Permite a SACAD, superadministradores o usuarios autorizados:
•
•
•
•
•
•
•
•
•
•

Crear y editar reuniones.
Concluir reuniones.
Editar reuniones concluidas.
Gestionar participantes.
Gestionar puntos.
Gestionar documentos.
Registrar resoluciones finales.
Crear, editar y publicar actas.
Editar actas publicadas.
Ver evaluaciones y comentarios internos.

•

Corregir o eliminar datos si fuera necesario.

6.4 actas.view
Permiso independiente del módulo CI.
Permite a usuarios autenticados autorizados:
•
•
•
•
•

Consultar actas publicadas.
Buscar actas.
Filtrar por año o fecha.
Ver actas en HTML.
Usar vista imprimible.

No permite ver reuniones, evaluaciones, comentarios, solicitudes asociadas ni entrar
al módulo operativo de CI.

7. Reglas de acceso
7.1 Reuniones
Un consejero puede ver una reunión únicamente si:
•
•

Tiene ci.evaluate.
Está registrado en ci_reuniones_participantes.

Si deja de tener el rol o permiso de CI, pierde acceso al módulo operativo.
Si vuelve a tener rol CI posteriormente, recupera acceso únicamente a las reuniones
donde participó.

7.2 Actas en borrador
Si el acta está ligada a una reunión, solo pueden evaluarla los participantes de esa
reunión que tengan ci.evaluate.
Si el acta no está ligada a reunión, pueden evaluarla todos los miembros activos del
CI con ci.evaluate.

7.3 Actas publicadas
Una vez publicada el acta:
•
•
•

Los miembros CI ya no ven comentarios ni evaluaciones internas.
Acceden únicamente mediante la vista institucional con actas.view.
SACAD conserva acceso operativo completo mediante ci.manage.

8. Reuniones
8.1 Estados
Las reuniones tendrán solo dos estados:
•
•

EN_PROCESO
CONCLUIDA

8.2 Valores por defecto al crear
Al crear una reunión:
•
•
•

tipo_reunion = ORDINARIA
modalidad = PRESENCIAL
estatus = EN_PROCESO

Estos campos estarán visibles desde el inicio y podrán modificarse.

8.3 Estructura de la pantalla de reunión
La reunión se organizará en tres secciones:
Sección 1: Datos y participantes
Incluye:
•
•
•
•
•
•

Título.
Fecha.
Tipo de reunión.
Modalidad.
Estatus.
Participantes.

Los participantes se mostrarán como lista simple de checkboxes con el nombre del
consejero.
No se mostrarán correos, cargos ni datos adicionales, porque no aportan valor en ese
contexto.
Sección 2: Solicitudes
Incluye:
•
•

Resumen de solicitudes.
Filtros.

•
•
•
•

Listado único con checkboxes.
Scroll interno.
Indicadores visuales para solicitudes seleccionadas o ya programadas.
Botón para ver solicitud en modal.

El listado debe mostrar todas las solicitudes disponibles para evaluación por CI.
Sección 3: Otros puntos y documentos adicionales
Incluye:
•
•
•
•

Captura de otros puntos.
Ordenamiento de otros puntos.
Carga de documentos adicionales.
Lista de documentos cargados.

8.4 Index de reuniones
Columnas:
•
•
•
•
•
•
•

Fecha.
Título.
Tipo.
Modalidad.
Estatus.
Resumen.
Acciones.

El resumen mostrará contadores con iconos:
•
•
•
•

Participantes.
Solicitudes.
Otros puntos.
Documentos.

No se mostrará acta como columna principal, porque las actas no están obligadas a
depender de reuniones.

9. Solicitudes dentro de reuniones
9.1 Listado
El listado de solicitudes debe mostrar datos ligeros:

•
•
•
•
•
•
•
•

Checkbox.
Folio.
Solicitante.
Tipo.
Motivo.
Fechas.
Indicador de recursos.
Botón Ver.

No debe mostrar estatus, porque el listado ya estará filtrado a solicitudes enviadas y
pendientes de resolución por CI.

9.2 Solicitudes ya programadas
Si una solicitud ya está incluida en otra reunión EN_PROCESO:
•
•
•
•

Se muestra en el listado.
Se indica visualmente.
El checkbox queda deshabilitado.
No se permite moverla ni reasignarla desde esta pantalla.

9.3 Filtros
Se podrán filtrar o buscar solicitudes por:
•
•
•
•
•

Folio.
Solicitante.
Tipo de solicitud.
Motivo.
Requiere recursos.

La filosofía es mostrar toda la información y permitir que SACAD filtre según su
necesidad.

9.4 Modal de solicitud
El modal de solicitud dentro de CI debe mostrar la solicitud completa en modo solo
lectura, equivalente a la vista Show del módulo Solicitudes.
Debe incluir:
•
•
•
•

Datos generales.
Solicitante.
Tipo y motivo.
Fechas.

•
•
•
•
•
•

Lugar, institución o destino.
Recursos.
Visitante, si aplica.
Documentos.
Observaciones.
Estado actual.

CI no edita solicitudes.
SACAD podrá editar observaciones_sacad desde el modal si se requiere, pero
cualquier edición completa de la solicitud debe realizarse en el módulo Solicitudes.
El modal puede incluir un botón:
•

Abrir en Solicitudes.

Al cerrar el modal, la interfaz debe mantener foco visual sobre la última solicitud
consultada.

10. Puntos del orden del día
Las solicitudes y otros asuntos viven juntos conceptualmente como puntos del orden
del día, almacenados en ci_puntos.

10.1 Tipos de punto
El tipo de punto usará catálogo:
Catálogo:
•

C_RCI_TP

Valores:
•
•

TP_SOL — Solicitudes.
TP_OTRO — Otro.

10.2 Solicitudes
Para puntos de tipo solicitud:
•
•
•

solicitud_id es obligatorio.
titulo es nullable.
descripcion es nullable.

La información se toma desde la solicitud.

10.3 Otros puntos
Para puntos de tipo otro:
•
•
•

solicitud_id es null.
titulo es obligatorio.
descripcion es obligatoria.

10.4 Orden
El orden se maneja por tipo de punto.
Las solicitudes se ordenan dentro del grupo de solicitudes.
Los otros puntos se ordenan dentro del grupo de otros puntos.
La UX debe permitir drag & drop dentro de cada grupo, sin mezclar solicitudes con
otros puntos.

11. Evaluaciones de puntos
11.1 Naturaleza de la evaluación
La evaluación es una opinión interna de cada consejero. Ayuda a tomar decisiones,
pero no equivale a la resolución final.

11.2 Catálogo de evaluación
Catálogo:
•

C_RCI_EVA

Valores:
•
•
•

EV_A — Aceptar.
EV_DIS — Discutir.
EV_RE — Rechazar.

11.3 Datos de evaluación
Cada evaluación incluye:
•
•
•
•

Punto.
Consejero.
Evaluación.
Comentario opcional.

No habrá evaluated_at, porque la fecha se obtiene de created_at y updated_at.

11.4 Visualización
Siempre se muestra:
•
•

Autor.
Evaluación.

Si existe comentario, se muestra también.
Si no existe comentario, solo se muestra autor y evaluación.
No habrá anonimato.

11.5 Edición
Mientras la reunión esté EN_PROCESO, cada consejero puede modificar su evaluación
y comentario.
Cuando la reunión esté CONCLUIDA, los consejeros ya no pueden editar.
SACAD puede modificar o eliminar evaluaciones si fuera necesario, con auditoría.

11.6 Sin historial visual
No se mostrará historial de cambios de evaluaciones ni comentarios.
Solo se muestra la evaluación vigente.
La trazabilidad se delega a la auditoría general del SIIAA.

12. Resolución final de puntos
La resolución vive en ci_puntos.
Representa el veredicto final del Consejo Interno, independientemente de las
evaluaciones individuales.
La resolución será texto, no catálogo.
Valores esperados:
•
•
•

ACEPTAR
DISCUTIR
RECHAZAR

SACAD registra la resolución final.

No habrá observaciones generales en ci_puntos; los comentarios viven
exclusivamente en evaluaciones.

13. Integración con Solicitudes
Cuando SACAD registra resolución final sobre un punto de tipo solicitud:

13.1 Si se acepta una solicitud sin recursos
Flujo:
•
•
•

ENVIADA
APROBADA_CI
CERRADA

13.2 Si se acepta una solicitud con recursos
Flujo:
•
•

ENVIADA
APROBADA_CI

Después continúa el proceso en Solicitudes, con intervención administrativa si
aplica.

13.3 Si se rechaza una solicitud
Flujo:
•
•
•

ENVIADA
RECHAZADA_CI
CERRADA

13.4 Correos
Solo se enviarán correos relacionados con solicitudes:
•
•

Al enviar solicitud.
Al registrar resolución de CI.

El correo de resolución incluirá:
•
•
•
•

Folio.
Resumen de solicitud.
Resolución.
Observaciones SACAD si existen.

•

Observaciones Administración si existen.

No se enviarán correos por reuniones, otros puntos, actas, documentos o
evaluaciones.

14. Actas
Las actas son parte del módulo Consejo Interno, pero deben tratarse como entidad
autónoma. No deben sentirse forzadas a pertenecer a una reunión.

14.1 Relación con reunión
ci_actas.reunion_id será nullable.

Una acta puede estar ligada a una reunión o existir de forma independiente.

14.2 Estados
Las actas tendrán solo dos estados:
•
•

BORRADOR
PUBLICADA

14.3 Número de acta
Se conservará numero_acta.
Este campo será manual.
No habrá numeración automática ni validación de consecutivos.

14.4 Fecha y año
Las actas tendrán:
•
•

fecha.
year.

El campo year se deriva automáticamente desde fecha y se usará para filtros.

14.5 Contenido
Las actas tendrán:
•
•
•

contenido_json.
contenido_html.
search_text.

Estos campos serán opcionales mientras el acta esté en borrador.
Para publicar, el acta debe tener contenido.

14.6 Editor
El editor recomendado es Tiptap.
Se usará una configuración simple y estable, enfocada en documentos
institucionales:
•
•
•
•
•
•

Negritas.
Itálicas.
Encabezados.
Listas.
Tablas.
Enlaces.

No se deben incluir herramientas innecesarias como videos, embeds o componentes
complejos.

14.7 Generación de contenido inicial
Si el acta se crea ligada a una reunión y el contenido está vacío, el sistema puede
generar un texto mínimo editable con:
•
•
•
•
•
•

Encabezado básico.
Fecha.
Tipo de reunión.
Modalidad.
Participantes.
Espacio para acuerdos.

Si el acta se crea sin reunión, el editor inicia vacío.
Si un acta ya tiene contenido y luego se liga a una reunión, el contenido no se
modifica ni se sobrescribe.

14.8 Búsqueda
search_text se genera automáticamente a partir de:

•
•
•

numero_acta.
titulo.
contenido_html.

Se eliminan etiquetas HTML y se normaliza el texto.

El usuario nunca editará search_text manualmente.

14.9 Publicación
Al publicar:
•
•
•

estatus = PUBLICADA.
publicada_at = now().

El acta aparece en la consulta institucional para usuarios con actas.view.

La publicación no requiere unanimidad ni validación obligatoria de evaluaciones.
SACAD publica cuando considere que existe consenso suficiente.

15. Evaluación de actas
Las actas también pueden ser evaluadas por miembros del Consejo Interno.

15.1 Evaluación de acta vs evaluación de puntos
Son dos procesos distintos.
La evaluación de puntos corresponde a solicitudes u otros asuntos de una reunión.
La evaluación de acta corresponde al contenido redactado del acta.

15.2 Valores
Se usará el mismo catálogo C_RCI_EVA o la equivalencia funcional correspondiente:
•
•
•

Aceptada.
Rechazada.
Aceptada con comentarios.

A nivel operativo, la evaluación del acta no bloquea publicación.

15.3 Acta ligada a reunión
Solo pueden evaluar los participantes de la reunión asociada que tengan
ci.evaluate.

15.4 Acta sin reunión
Pueden evaluar todos los miembros activos del CI con ci.evaluate.

15.5 Acta publicada
Una vez publicada:

•
•
•

CI ya no ve evaluaciones ni comentarios.
CI accede por la vista institucional si tiene actas.view.
SACAD conserva acceso a evaluaciones y comentarios internos.

15.6 Sin historial visual
Igual que en puntos, solo se muestra la evaluación vigente.
No se implementa historial visual de cambios.

16. Publicación, impresión y PDF
El sistema no generará PDF automático desde servidor en la primera versión.
La ruta aprobada es:
•
•
•

HTML institucional.
Vista imprimible.
Guardar como PDF desde navegador.

La vista imprimible debe contener únicamente:
•
•
•
•

Número de acta.
Título.
Fecha.
Contenido del acta.

Nunca debe incluir:
•
•
•
•
•

Evaluaciones.
Comentarios.
Solicitudes asociadas.
Datos operativos internos.
Información no publicada.

Esto aplica para todos los usuarios, incluyendo SACAD.
Se incluirá una mini guía en modal para explicar cómo guardar el PDF sin
encabezados, pies o marcas del navegador.

17. Consulta institucional de actas
La consulta institucional será independiente del módulo operativo de CI.

Ruta sugerida:
•
•

/actas
/actas/{acta}

Solo muestra actas publicadas.
Permite:
•
•
•
•
•

Buscar por texto.
Filtrar por año.
Filtrar por rango de fechas.
Ver acta.
Usar vista imprimible.

No permite:
•
•
•
•
•

Ver evaluaciones.
Ver comentarios.
Ver reuniones internas.
Ver solicitudes asociadas.
Editar contenido.

18. Tablas conceptuales
Las tablas usarán prefijo ci_ y nombres en plural, incluyendo tablas de relación.

18.1 Tablas
•
•
•
•
•
•
•

ci_reuniones
ci_reuniones_participantes
ci_puntos
ci_puntos_evaluaciones
ci_documentos
ci_actas
ci_actas_evaluaciones

19. Campos conceptuales
19.1 ci_reuniones
•
•

id
titulo

•
•
•
•
•
•
•
•
•
•

fecha
tipo_reunion
modalidad
estatus
created_by
updated_by
concluida_at
concluida_by
created_at
updated_at

19.2 ci_reuniones_participantes
•
•
•
•
•

id
reunion_id
identity_link_id
created_at
updated_at

19.3 ci_puntos
•
•
•
•
•
•
•
•
•
•
•
•

id
reunion_id
tipo_punto_id
solicitud_id
titulo
descripcion
orden
resolucion
resolved_at
resolved_by
created_at
updated_at

19.4 ci_puntos_evaluaciones
•
•
•
•
•
•
•

id
punto_id
identity_link_id
evaluacion_id
comentarios
created_at
updated_at

19.5 ci_documentos
•
•
•
•
•
•
•
•
•
•

id
reunion_id
filename
original_name
path
mime_type
size
uploaded_by
created_at
updated_at

19.6 ci_actas
•
•
•
•
•
•
•
•
•
•
•
•
•
•
•

id
reunion_id
numero_acta
titulo
fecha
year
estatus
contenido_json
contenido_html
search_text
publicada_at
created_by
updated_by
created_at
updated_at

19.7 ci_actas_evaluaciones
•
•
•
•
•
•
•

id
acta_id
identity_link_id
evaluacion_id
comentarios
created_at
updated_at

20. Identidad y autoría
Todos los campos de autoría, evaluación, resolución o acción deben apuntar
conceptualmente a:
•

identity_links.id

Esto aplica a:
•
•
•
•
•
•

created_by
updated_by
concluida_by
resolved_by
uploaded_by
identity_link_id

No se debe usar users.id salvo excepción explícitamente documentada.

21. Catálogos
21.1 Tipo de punto
Catálogo:
•

C_RCI_TP

Valores:
•
•

TP_SOL — Solicitudes.
TP_OTRO — Otro.

21.2 Evaluaciones
Catálogo:
•

C_RCI_EVA

Valores:
•
•
•

EV_A — Aceptar.
EV_DIS — Discutir.
EV_RE — Rechazar.

21.3 Texto / constantes internas
Se manejarán como texto:

•
•
•
•

tipo_reunion
modalidad
estatus
resolucion

22. Servicios recomendados
22.1 CiReunionService
Responsable de:
•
•
•
•
•
•
•
•

Crear reunión.
Actualizar reunión.
Sincronizar participantes.
Incluir/quitar solicitudes.
Crear/actualizar otros puntos.
Reordenar puntos.
Concluir reunión.
Evitar guardar sin cambios reales.

22.2 CiEvaluacionService
Responsable de:
•
•
•
•
•

Guardar evaluación de punto.
Actualizar comentario vigente.
Validar si el usuario puede evaluar.
Bloquear edición para CI en reuniones concluidas.
Permitir intervención de SACAD.

22.3 CiResolucionService
Responsable de:
•
•
•
•

Registrar resolución de punto.
Si el punto es solicitud, actualizar la solicitud.
Coordinar correo de resolución.
Integrarse con SolicitudService.

22.4 CiActaService
Responsable de:
•

Crear acta.

•
•
•
•
•
•

Asociar opcionalmente reunión.
Generar contenido inicial si aplica.
Guardar contenido.
Generar search_text.
Publicar acta.
Validar evaluadores.

22.5 Servicios transversales
Se contempla usar servicios reutilizables:
•
•
•

SolicitudService
NotificationService
MailService

Los correos deben pasar por configuración centralizada para distinguir entorno
local/testing/producción.

23. Correos
Solo se enviarán correos para solicitudes.

23.1 Al enviar solicitud
Correo de recepción con:
•
•
•
•

Folio.
Resumen.
Confirmación de recepción.
Aviso de espera de revisión.

23.2 Al resolver solicitud en CI
Correo con:
•
•
•
•
•

Folio.
Resumen.
Resolución.
Observaciones SACAD si existen.
Observaciones Administración si existen.

No habrá correos para:
•

Reuniones.

•
•
•
•

Actas.
Otros puntos.
Evaluaciones.
Documentos.

24. Entornos de correo
El envío de correos debe estar centralizado.
En local/testing no deben enviarse correos reales.
Opciones:
•
•
•
•
•

Log.
Mailpit.
Mailhog.
Array.
Buzón de pruebas.

En producción se habilita envío real.
La regla es:
Ningún módulo debe enviar correos directamente ni asumir que puede
contactar usuarios reales.

25. Orden de implementación recomendado
25.1 Migraciones
Crear las tablas del módulo y verificar catálogos.

25.2 Modelos
Crear modelos y relaciones.

25.3 Permisos
Registrar permisos:
•
•
•
•

ci.access
ci.evaluate
ci.manage
actas.view

25.4 Servicios
Crear servicios base del módulo.

25.5 Reuniones
Implementar Index, Edit y Show.

25.6 Evaluaciones
Implementar evaluación de puntos.

25.7 Resoluciones
Integrar resolución de solicitudes con Solicitudes.

25.8 Actas
Implementar actas, editor, búsqueda y publicación.

25.9 Consulta institucional
Implementar vista pública/institucional de actas publicadas.

25.10 Pulido UX
Agregar:
•
•
•
•
•

Resúmenes.
Iconos con cantidades.
Scroll interno.
Foco visual tras cerrar modal.
Guía para impresión PDF.

26. Convenciones Livewire
Se usará la convención:
•
•
•

Index
Show
Edit

No se crearán componentes separados Create si la pantalla de creación y edición es
la misma.
Rutas distintas pueden apuntar al mismo componente Edit.
Ejemplo:

•
•

/ci/reuniones/crear
/ci/reuniones/{reunion}/editar

Ambas usan Reuniones/Edit.

27. Conclusión
El módulo Consejo Interno queda definido como un expediente institucional
compuesto, flexible y mantenible, orientado al flujo real de SACAD y del Consejo
Interno.
Su diseño evita duplicar responsabilidades con Solicitudes, mantiene independencia
operativa de Actas, protege comentarios y evaluaciones internas, permite consulta
institucional de documentos publicados y conserva la flexibilidad administrativa
necesaria para el trabajo cotidiano.
La arquitectura propuesta mantiene el estilo general de SIIAA Laravel 13: interfaces
claras, lógica explícita, servicios reutilizables, catálogos institucionales cuando
aportan valor, identidad mediante identity_links y mínima complejidad accidental.

