# local_prompt_tarea

Plugin local para Moodle que permite a los profesores asociar un **prompt de inteligencia artificial** a cada tarea (`assign`). Este prompt es utilizado por el plugin [`assignfeedback_aiprompt`](../assignfeedback_aiprompt/) para generar retroalimentación automática sobre las entregas de los estudiantes.

---

## 📋 Descripción

`local_prompt_tarea` extiende el formulario estándar de configuración de tareas en Moodle añadiendo una nueva sección llamada **"Configuración de Prompt"**. En ella, el profesor puede redactar las instrucciones que la IA utilizará al evaluar las entregas de los alumnos.

### ¿Qué hace exactamente?

1. **Agrega un campo de texto** en el formulario de edición de cualquier actividad de tipo Tarea.
2. **Guarda el prompt** en la tabla `local_prompt_tarea` de la base de datos cuando se guarda la tarea.
3. **Carga el prompt existente** cuando el profesor vuelve a editar la tarea.
4. **Elimina el prompt** automáticamente cuando la tarea es borrada.

---

## 🗂️ Estructura del proyecto

```
local/prompt_tarea/
├── lib.php           # Hooks de Moodle: añade campo, guarda y elimina el prompt
├── version.php       # Metadatos del plugin (versión, dependencias)
├── db/
│   └── install.xml   # Esquema de base de datos
└── lang/
    └── en/
        └── local_prompt_tarea.php  # Cadenas de idioma (en inglés/español)
```

---

## ✅ Requisitos

| Requisito | Versión mínima |
|-----------|----------------|
| Moodle    | 5.0 (build 2024042200) |
| PHP       | 7.4+ |

---

## 🚀 Instalación

### 1. Copiar el plugin

Coloca la carpeta del plugin en el directorio de plugins locales de Moodle:

```
{moodle_root}/local/prompt_tarea/
```

### 2. Instalar en Moodle

Accede como administrador y ve a:

```
Administración del sitio → Notificaciones
```

Moodle detectará el plugin y creará la tabla `local_prompt_tarea` automáticamente.

---

## 🗃️ Esquema de base de datos

### Tabla: `local_prompt_tarea`

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | INT (PK) | Clave primaria autoincremental |
| `assignid` | INT (FK) | ID de la tarea (`assign.id`) — único por tarea |
| `prompt` | TEXT | Texto del prompt configurado por el profesor |
| `timecreated` | INT | Timestamp Unix de creación |
| `timemodified` | INT | Timestamp Unix de última modificación |

> **Restricción única:** el campo `assignid` tiene un índice único, por lo que cada tarea solo puede tener un prompt.

---

## 🔗 Integración con `assignfeedback_aiprompt`

Este plugin actúa como **proveedor de datos** para el plugin de feedback con IA. Cuando un profesor califica una tarea, `assignfeedback_aiprompt` consulta la tabla `local_prompt_tarea` buscando el prompt asociado al `assignid` correspondiente.

```
Profesor edita tarea → local_prompt_tarea guarda el prompt
                              ↓
Profesor califica entrega → assignfeedback_aiprompt lee el prompt
                              ↓
                     IA genera feedback personalizado
```

---

## 🧩 Arquitectura: `lib.php`

Este archivo implementa tres hooks estándar de Moodle:

| Función | Hook de Moodle | Descripción |
|---------|----------------|-------------|
| `local_prompt_tarea_coursemodule_standard_elements()` | `coursemodule_standard_elements` | Añade el campo de prompt al formulario de edición de la tarea |
| `local_prompt_tarea_coursemodule_edit_post_actions()` | `coursemodule_edit_post_actions` | Guarda (o actualiza) el prompt en la BD al guardar la tarea |
| `local_prompt_tarea_pre_course_module_delete()` | `pre_course_module_delete` | Elimina el prompt de la BD cuando se borra la tarea |

---

## 🔐 Privacidad

Este plugin **no almacena datos personales** de los estudiantes. Solo guarda los prompts configurados por los profesores, asociados a los IDs de tareas.

```php
$string['privacy:metadata'] = 'El plugin Prompt para Tareas no almacena datos personales.';
```

---

## 📄 Versión

| Atributo | Valor |
|----------|-------|
| Componente | `local_prompt_tarea` |
| Versión | `2025011100` |
| Madurez | Estable (`MATURITY_STABLE`) |
| Release | `1.0` |
