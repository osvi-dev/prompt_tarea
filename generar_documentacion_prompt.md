# Prompt para Generar la Documentación de `local_prompt_tarea`

Copia y pega el siguiente prompt en cualquier modelo de lenguaje de Inteligencia Artificial (como Gemini, GPT-4, Claude, etc.) junto con los archivos del código fuente del proyecto para generar una documentación técnica y de usuario sumamente detallada, profesional y formateada en Markdown.

---

```markdown
Actúa como un Escritor Técnico Principal (Lead Technical Writer) con amplia experiencia en desarrollo de plugins y arquitectura de Moodle. Tu tarea consiste en generar la documentación técnica, de desarrollo y de usuario más completa, exhaustiva y clara posible para el plugin de Moodle llamado `local_prompt_tarea`.

Para hacer esto, debes utilizar la información del código del proyecto y las pautas estructurales provistas a continuación. 

La documentación debe redactarse en español y en formato Markdown (.md) profesional, utilizando tablas, diagramas Mermaid cuando corresponda para ilustrar flujos de datos, y fragmentos de código debidamente formateados con su lenguaje respectivo.

---

### Contexto del Proyecto

El plugin `local_prompt_tarea` es un plugin local para Moodle (diseñado para Moodle 5.0+ y PHP 7.4+) que permite a los docentes asociar prompts de Inteligencia Artificial a actividades de tipo Tarea (`assign`). 
Estos prompts se almacenan en una base de datos propia y están pensados para ser consumidos por otros plugins del ecosistema de Moodle, como el plugin de retroalimentación `assignfeedback_aiprompt`, el cual lee las instrucciones del docente para generar retroalimentación automatizada utilizando IA sobre los archivos o textos entregados por los alumnos.

---

### 📂 Estructura y Archivos del Proyecto

El plugin se ubica en `{moodle_root}/local/prompt_tarea/` y consta de los siguientes archivos clave que debes detallar en la documentación:

1. **`version.php`**: Define metadatos y dependencias (requiere Moodle 2024042200 o superior, versión de release 1.0, madurez estable).
2. **`lib.php`**: Contiene la lógica central y la implementación de los hooks estándar de Moodle para extender el comportamiento del módulo `assign`.
3. **`db/install.xml`**: Define la estructura física de la base de datos (tabla `local_prompt_tarea`).
4. **`db/upgrade.php`**: Gestiona las actualizaciones del esquema de base de datos a lo largo del ciclo de vida del plugin.
5. **`lang/en/local_prompt_tarea.php`**: Almacena las cadenas de idioma utilizadas para internacionalizar el plugin (interfaz gráfica y metadatos de privacidad).

---

### Estructura de Secciones Requerida para la Documentación

Genera la documentación organizada bajo los siguientes encabezados principales:

#### 1. Introducción y Arquitectura General
*   **Propósito del Plugin**: Explicar qué problema resuelve y cómo interactúa con el ecosistema de evaluación asistida por IA en Moodle (específicamente la relación con el plugin de feedback `assignfeedback_aiprompt`).
*   **Diagrama de Flujo de Trabajo (Mermaid)**: Crear un diagrama conceptual que muestre:
    1. Creación/Edición de la tarea por el docente y guardado de prompts.
    2. Visualización opcional del prompt por parte del estudiante.
    3. Consulta de prompts por plugins calificadores cuando se evalúa una entrega.

#### 2. Requisitos del Entorno e Instalación
*   Versión mínima de Moodle requerida (`5.0` / Build `2024042200`).
*   Versión mínima de PHP (`7.4+`).
*   Paso a paso detallado para instalar el plugin en un servidor Moodle (copia de archivos en la ruta correcta, actualización de base de datos mediante la interfaz web de Moodle o comandos CLI `/admin/cli/upgrade.php`).

#### 3. Esquema de Base de Datos
*   Detallar la tabla `local_prompt_tarea` mediante una tabla de Markdown con columnas: **Campo**, **Tipo**, **Nulo**, **Clave/Índice**, **Descripción**.
*   Detalles de campos:
    *   `id`: INT (Primary Key, Autoincremental).
    *   `assignid`: INT (Foreign Key apuntando a `assign.id`, con restricción **Única** e índice `assignid_idx`).
    *   `prompt`: TEXT (Texto de instrucciones del docente para la IA).
    *   `prompt_estudiante`: TEXT (Texto de instrucciones o contexto visible para el estudiante).
    *   `timecreated`: INT (Timestamp de creación).
    *   `timemodified`: INT (Timestamp de actualización).

#### 4. Detalle de Implementación Técnica (Hooks de Moodle en `lib.php`)
Explica exhaustivamente cada una de las siguientes funciones, indicando los parámetros que recibe, su lógica interna y los estándares de Moodle que implementa:
*   **`local_prompt_tarea_coursemodule_standard_elements($formwrapper, $mform)`**:
    *   Cómo filtra para actuar únicamente en tareas de tipo `assign`.
    *   Cómo añade las secciones de encabezados (`promptheader`, `promptestudianteheader`) y campos de área de texto (`prompt_tarea_text`, `prompt_estudiante_text`) en el formulario de configuración.
    *   Cómo maneja la obtención segura del valor de instancia existente y cómo comprueba la existencia de la tabla en base de datos antes de cargar por defecto los prompts guardados (incluyendo el manejo silencioso de excepciones mediante `debugging`).
*   **`local_prompt_tarea_coursemodule_edit_post_actions($data, $course)`**:
    *   Cómo intercepta la acción posterior al guardado del formulario de edición.
    *   La lógica para determinar si debe realizar un `insert_record` (nuevo registro) o un `update_record` (actualizar registro existente) mapeando los campos del formulario con la tabla `local_prompt_tarea`.
    *   El registro de marcas temporales (`timecreated`, `timemodified`).
*   **`local_prompt_tarea_pre_course_module_delete($cm)`**:
    *   Cómo funciona el hook que intercepta la eliminación de una actividad en el curso.
    *   La lógica para eliminar en cascada de manera limpia los registros de prompts asociados en `local_prompt_tarea` para evitar datos huérfanos.

#### 5. Ciclo de Vida y Migración de Base de Datos
*   Explicar el archivo `db/upgrade.php` y la función `xmldb_local_prompt_tarea_upgrade($oldversion)`.
*   Detallar la migración a la versión `2025053100`: inserción dinámica del campo `prompt_estudiante` como tipo TEXT en la tabla existente sin perder datos anteriores, utilizando la clase `xmldb_field` y el gestor de base de datos de Moodle `$DB->get_manager()`.

#### 6. Internacionalización e Idioma (i18n)
*   Presentar las cadenas de traducción declaradas en `lang/en/local_prompt_tarea.php` con su descripción de uso en la interfaz de usuario:
    *   `pluginname`
    *   `promptheader`
    *   `promptlabel`
    *   `promptlabel_help`
    *   `promptestudianteheader`
    *   `promptestudiantelabel`
    *   `promptestudiantelabel_help`
    *   `privacy:metadata`

#### 7. Seguridad y Privacidad (GDPR)
*   Explicar la política de cumplimiento de privacidad del plugin.
*   Justificar por qué el plugin cumple por diseño con las directivas de privacidad de datos personales de Moodle (solo almacena prompts escritos por profesores asociados a un ID de tarea, sin capturar datos de estudiantes ni de calificaciones).

#### 8. Guía de Usuario para Docentes
*   Cómo se visualizan los campos nuevos dentro de la configuración de una Tarea.
*   Buenas prácticas para escribir prompts efectivos de evaluación asistida.
*   Diferencia fundamental de alcance entre el prompt de evaluación (orientado a la IA de retroalimentación) y el prompt para el estudiante (orientado a guiar su escritura o comprensión de la actividad).

---

### Instrucciones para la Salida (Output Guidelines)
1.  **Profundidad técnica**: No resumas la lógica de las funciones. Explica con precisión cómo se interactúa con `$mform` de Moodle y los métodos del API `$DB`.
2.  **Calidad del código**: Si haces referencia a fragmentos de código, asegúrate de que estén comentados detalladamente en español.
3.  **Estilo visual**: Usa negrita, tablas limpias de Markdown, listas de definición y diagramas claros.
```
