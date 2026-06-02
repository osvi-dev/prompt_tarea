<?php
defined('MOODLE_INTERNAL') || die();

/**
 * Añade el campo prompt al formulario de configuración de la tarea
 */
function local_prompt_tarea_coursemodule_standard_elements($formwrapper, $mform) {
    global $DB;
    
    // Solo aplicar a módulos de tipo 'assign'
    if ($formwrapper->get_current()->modulename != 'assign') {
        return;
    }
    
    $mform->addElement('header', 'promptheader', get_string('promptheader', 'local_prompt_tarea'));
    
    $mform->addElement('textarea', 'prompt_tarea_text', 
                       get_string('promptlabel', 'local_prompt_tarea'),
                       'wrap="virtual" rows="10" cols="50"');
    
    $mform->setType('prompt_tarea_text', PARAM_TEXT);
    $mform->addHelpButton('prompt_tarea_text', 'promptlabel', 'local_prompt_tarea');
    
    // Sección: Prompt para el Estudiante
    $mform->addElement('header', 'promptestudianteheader', get_string('promptestudianteheader', 'local_prompt_tarea'));
    
    $mform->addElement('textarea', 'prompt_estudiante_text', 
                       get_string('promptestudiantelabel', 'local_prompt_tarea'),
                       'wrap="virtual" rows="10" cols="50"');
    
    $mform->setType('prompt_estudiante_text', PARAM_TEXT);
    $mform->addHelpButton('prompt_estudiante_text', 'promptestudiantelabel', 'local_prompt_tarea');
    
    // Cargar datos existentes si estamos editando
    if (isset($formwrapper->get_current()->instance) && $formwrapper->get_current()->instance) {
        try {
            $assignid = $formwrapper->get_current()->instance;
            
            // Verificar que la tabla exista
            $dbman = $DB->get_manager();
            $table = new xmldb_table('local_prompt_tarea');
            
            if ($dbman->table_exists($table)) {
                $record = $DB->get_record('local_prompt_tarea', ['assignid' => $assignid]);
                if ($record) {
                    $mform->setDefault('prompt_tarea_text', $record->prompt);
                    $mform->setDefault('prompt_estudiante_text', $record->prompt_estudiante);
                }
            }
        } catch (Exception $e) {
            // Silenciosamente ignorar errores al cargar datos
            debugging('Error loading prompt data: ' . $e->getMessage(), DEBUG_DEVELOPER);
        }
    }
}

/**
 * Guarda el prompt cuando se guarda la tarea
 */
function local_prompt_tarea_coursemodule_edit_post_actions($data, $course) {
    global $DB;
    
    // Solo aplicar a módulos de tipo 'assign'
    if ($data->modulename != 'assign') {
        return $data;
    }
    
    // Verificar que la tabla exista
    $dbman = $DB->get_manager();
    $table = new xmldb_table('local_prompt_tarea');
    
    if (!$dbman->table_exists($table)) {
        debugging('Table local_prompt_tarea does not exist', DEBUG_DEVELOPER);
        return $data;
    }
    
    if (isset($data->prompt_tarea_text) || isset($data->prompt_estudiante_text)) {
        $time = time();
        
        try {
            // Verificar si ya existe un registro
            $record = $DB->get_record('local_prompt_tarea', ['assignid' => $data->instance]);
            
            if ($record) {
                // Actualizar
                $record->prompt = $data->prompt_tarea_text ?? $record->prompt;
                $record->prompt_estudiante = $data->prompt_estudiante_text ?? $record->prompt_estudiante;
                $record->timemodified = $time;
                $DB->update_record('local_prompt_tarea', $record);
            } else {
                // Crear nuevo
                $record = new stdClass();
                $record->assignid = $data->instance;
                $record->prompt = $data->prompt_tarea_text ?? '';
                $record->prompt_estudiante = $data->prompt_estudiante_text ?? '';
                $record->timecreated = $time;
                $record->timemodified = $time;
                $DB->insert_record('local_prompt_tarea', $record);
            }
        } catch (Exception $e) {
            debugging('Error saving prompt: ' . $e->getMessage(), DEBUG_DEVELOPER);
        }
    }
    
    return $data;
}

/**
 * Elimina el prompt cuando se elimina la tarea
 */
function local_prompt_tarea_pre_course_module_delete($cm) {
    global $DB;
    
    if ($cm->modname == 'assign') {
        // Verificar que la tabla exista
        $dbman = $DB->get_manager();
        $table = new xmldb_table('local_prompt_tarea');
        
        if ($dbman->table_exists($table)) {
            try {
                $DB->delete_records('local_prompt_tarea', ['assignid' => $cm->instance]);
            } catch (Exception $e) {
                debugging('Error deleting prompt: ' . $e->getMessage(), DEBUG_DEVELOPER);
            }
        }
    }
}