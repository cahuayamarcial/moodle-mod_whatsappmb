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
// along with Moodle. If not, see <https://www.gnu.org/licenses/>.

/**
 * Defines the steps to backup the whatsappmb activity.
 *
 * @package   mod_whatsappmb
 * @copyright 2025 Marcial Cahuaya | Marbot
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Defines the structure for backing up the whatsappmb activity.
 */
class backup_whatsappmb_activity_structure_step extends backup_activity_structure_step {

    /**
     * Defines the backup structure.
     *
     * @return backup_nested_element The backup structure.
     */
    protected function define_structure() {
        // Define the root element describing the activity.
        $whatsappmb = new backup_nested_element('whatsappmb', ['id'], [
            'course', 'name', 'intro', 'introformat', 'linktype',
            'whatsappnumber', 'message', 'grouplink', 'timecreated', 'timemodified'
        ]);

        // Set the source table for the backup.
        $whatsappmb->set_source_table('whatsappmb', ['id' => backup::VAR_ACTIVITYID]);

        // Annotate files related to the activity.
        $whatsappmb->annotate_files('mod_whatsappmb', 'intro', null);

        // Return the structure wrapped in the activity element.
        return $this->prepare_activity_structure($whatsappmb);
    }
}

