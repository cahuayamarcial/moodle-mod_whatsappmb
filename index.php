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
 * Index file for mod_whatsappmb.
 *
 * This file is responsible for handling direct access to the module's index page.
 * If accessed without a valid course ID, it displays an error message.
 *
 * @package   mod_whatsappmb
 * @copyright 2025 Marcial Cahuaya | Marbot
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');

$id = optional_param('id', 0, PARAM_INT); // Safely retrieve the course ID from request parameters.

require_login();

// Set up the page configuration.
$PAGE->set_url('/mod/whatsappmb/index.php', ['id' => $id]);
$PAGE->set_context(context_system::instance());
$PAGE->set_heading(get_string('pluginname', 'mod_whatsappmb'));

if ($id > 0) {
    // Redirect to the activity view page if a valid ID is provided.
    redirect(new moodle_url('/mod/whatsappmb/view.php', ['id' => $id]));
} else {
    // Display an error message if no valid ID is found.
    echo $OUTPUT->header();
    echo $OUTPUT->notification(get_string('invalidwhatsappid', 'mod_whatsappmb'), 'notifyproblem');
    echo $OUTPUT->footer();
    die();
}
