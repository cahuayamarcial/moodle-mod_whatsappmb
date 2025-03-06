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
 * Defines the backup task for the whatsappmb activity.
 *
 * @package   mod_whatsappmb
 * @copyright 2025 Marcial Cahuaya | Marbot
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/whatsappmb/backup/moodle2/backup_whatsappmb_stepslib.php');

/**
 * Class to define the backup task for the whatsappmb activity.
 */
class backup_whatsappmb_activity_task extends backup_activity_task {

    /**
     * Defines the settings for the backup task.
     */
    protected function define_my_settings() {
        // No specific settings are required for this activity.
    }

    /**
     * Defines the steps for the backup task.
     */
    protected function define_my_steps() {
        // Define the step to backup the activity.
        $this->add_step(new backup_whatsappmb_activity_structure_step('whatsappmb_structure', 'whatsappmb.xml'));
    }

    /**
     * Encodes content links in the activity.
     *
     * @param string $content The content to encode.
     * @return string The encoded content.
     */
    static public function encode_content_links($content) {
        global $CFG;

        // Define the base URL for the activity.
        $base = preg_quote($CFG->wwwroot . '/mod/whatsappmb', '#');

        // Encode links to the view.php page.
        $pattern = "#(" . $base . "/view\.php\?id=)([0-9]+)#";
        $content = preg_replace($pattern, '$@WHATSMAPPVIEWBYID*$2@$', $content);

        // Encode links to the index.php page.
        $pattern = "#(" . $base . "/index\.php\?id=)([0-9]+)#";
        $content = preg_replace($pattern, '$@WHATSMAPPINDEX*$2@$', $content);

        return $content;
    }
}