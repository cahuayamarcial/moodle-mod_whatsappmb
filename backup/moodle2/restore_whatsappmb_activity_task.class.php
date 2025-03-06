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
 * Defines the restore task for the whatsappmb activity.
 *
 * @package   mod_whatsappmb
 * @copyright 2025 Marcial Cahuaya
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/whatsappmb/backup/moodle2/restore_whatsappmb_stepslib.php');

/**
 * Defines the restore task for the whatsappmb activity.
 */
class restore_whatsappmb_activity_task extends restore_activity_task {

    /**
     * Defines the settings for the restore task.
     *
     * No specific settings are required for this activity.
     */
    protected function define_my_settings() {
        // No specific settings are required.
    }

    /**
     * Defines the steps for the restore task.
     */
    protected function define_my_steps() {
        // Define the step to restore the activity.
        $this->add_step(new restore_whatsappmb_activity_structure_step('whatsappmb_structure', 'whatsappmb.xml'));
    }

    /**
     * Defines the contents in the activity that must be processed by the link decoder.
     *
     * @return array List of restore_decode_content instances.
     */
    public static function define_decode_contents() {
        $contents = [];
        $contents[] = new restore_decode_content('whatsappmb', ['intro'], 'whatsappmb');
        return $contents;
    }

    /**
     * Defines the decoding rules for links belonging to the activity.
     *
     * @return array List of restore_decode_rule instances.
     */
    public static function define_decode_rules() {
        $rules = [];

        $rules[] = new restore_decode_rule('WHATSAPPMBVIEWBYID', '/mod/whatsappmb/view.php?id=$1', 'course_module');
        $rules[] = new restore_decode_rule('WHATSAPPMBINDEX', '/mod/whatsappmb/index.php?id=$1', 'course');

        return $rules;
    }

    /**
     * Defines the restore log rules that will be applied when restoring logs.
     *
     * @return array List of restore_log_rule instances.
     */
    public static function define_restore_log_rules() {
        $rules = [];

        $rules[] = new restore_log_rule('whatsappmb', 'add', 'view.php?id={course_module}', '{whatsappmb}');
        $rules[] = new restore_log_rule('whatsappmb', 'update', 'view.php?id={course_module}', '{whatsappmb}');
        $rules[] = new restore_log_rule('whatsappmb', 'view', 'view.php?id={course_module}', '{whatsappmb}');

        return $rules;
    }

    /**
     * Defines the restore log rules that will be applied when restoring course logs.
     *
     * @return array List of restore_log_rule instances.
     */
    public static function define_restore_log_rules_for_course() {
        $rules = [];

        $rules[] = new restore_log_rule('whatsappmb', 'view all', 'index.php?id={course}', null);

        return $rules;
    }
}