<?php
defined('MOODLE_INTERNAL') || die();

function xmldb_local_prompt_tarea_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager();

    if ($oldversion < 2025053100) {
        // Add prompt_estudiante field to local_prompt_tarea table.
        $table = new xmldb_table('local_prompt_tarea');
        $field = new xmldb_field('prompt_estudiante', XMLDB_TYPE_TEXT, null, null, null, null, null, 'prompt');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_plugin_savepoint(true, 2025053100, 'local', 'prompt_tarea');
    }

    return true;
}
