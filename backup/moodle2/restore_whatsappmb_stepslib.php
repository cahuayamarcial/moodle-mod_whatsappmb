<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.

/**
 * Defines the steps to restore the whatsappmb activity.
 *
 * @package   mod_whatsappmb
 * @copyright 2025 Marcial Cahuaya
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * Class to define the steps for restoring the whatsappmb activity.
 */
class restore_whatsappmb_activity_structure_step extends restore_activity_structure_step {

    /**
     * Define the structure for restoring the whatsappmb activity.
     */
    protected function define_structure() {
        $paths = [];
        $paths[] = new restore_path_element('whatsappmb', '/activity/whatsappmb');

        // Agrega más elementos si tu actividad tiene subdatos, como configuraciones adicionales o archivos.

        return $this->prepare_activity_structure($paths);
    }

    /**
     * Procesa la restauración de la actividad whatsappmb.
     *
     * @param stdClass $data Datos de la actividad extraídos del respaldo.
     */
    protected function process_whatsappmb($data) {
        global $DB;

        // Convertir a objeto
        $data = (object)$data;
        $oldid = $data->id; // ID antiguo del backup
        $data->course = $this->get_courseid();

        // Manejo de fechas (si tu tabla tiene estos campos)
        if (isset($data->timecreated)) {
            $data->timecreated = $this->apply_date_offset($data->timecreated);
        }
        if (isset($data->timemodified)) {
            $data->timemodified = $this->apply_date_offset($data->timemodified);
        }
        
        // Insertar en la base de datos
        $newitemid = $DB->insert_record('whatsappmb', $data);

        // Aplicar el mapeo de actividad
        $this->apply_activity_instance($newitemid);

        // Guardar la relación entre el ID del backup y el nuevo ID
        $this->set_mapping('whatsappmb', $oldid, $newitemid, true);
    }

    /**
     * Agregar archivos relacionados después de la restauración.
     */
    protected function after_execute() {
        $this->add_related_files('mod_whatsappmb', 'intro', null);
    }
}