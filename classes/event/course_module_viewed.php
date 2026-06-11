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
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * The mod_whatsappmb course module viewed event.
 *
 * @package   mod_whatsappmb
 * @copyright 2025 Marcial Cahuaya | Marbot
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_whatsappmb\event;

/**
 * Event triggered when a user views a whatsappmb activity instance.
 */
class course_module_viewed extends \core\event\course_module_viewed {
    /**
     * Initialise the event data.
     */
    protected function init() {
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
        $this->data['objecttable'] = 'whatsappmb';
    }

    /**
     * Returns the mapping used by the backup/restore subsystem.
     *
     * @return array
     */
    public static function get_objectid_mapping() {
        return ['db' => 'whatsappmb', 'restore' => 'whatsappmb'];
    }

    /**
     * Returns a human readable description of the event.
     *
     * @return string
     */
    public function get_description() {
        return "The user with id '{$this->userid}' viewed the whatsappmb activity with id '{$this->objectid}' " .
            "in course with id '{$this->courseid}'.";
    }

    /**
     * Returns the URL that points to the resource related to the event.
     *
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url('/mod/whatsappmb/view.php', ['id' => $this->contextinstanceid]);
    }

    /**
     * Returns legacy log data for backwards compatibility.
     *
     * @return array
     */
    protected function get_legacy_logdata() {
        return [
            $this->courseid,
            'whatsappmb',
            'view',
            'view.php?id=' . $this->contextinstanceid,
            $this->objectid,
            $this->contextinstanceid,
        ];
    }
}
