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
 * Displays a list of WhatsAppMB instances, either for a specific course or across all courses.
 *
 * @package   mod_whatsappmb
 * @copyright 2025 Marcial Cahuaya | Marbot
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');

$id = optional_param('id', 0, PARAM_INT); // Course ID is optional.

require_login();

$systemcontext = context_system::instance();

// Set up the page
$PAGE->set_url('/mod/whatsappmb/index.php', ['id' => $id]);
$PAGE->set_context($systemcontext);
$PAGE->set_pagelayout('incourse');
$PAGE->set_title(get_string('pluginname', 'mod_whatsappmb'));
$PAGE->set_heading(get_string('pluginname', 'mod_whatsappmb'));

echo $OUTPUT->header();

if ($id) {
    // Case 1: A specific course ID is provided
    $course = $DB->get_record('course', ['id' => $id], '*', MUST_EXIST);
    $coursecontext = context_course::instance($course->id);

    // Log the course module instance list viewed event
    $event = \mod_whatsappmb\event\course_module_instance_list_viewed::create([
        'context' => $coursecontext,
        'courseid' => $course->id
    ]);
    $event->trigger();

    // Update page settings for the specific course
    $PAGE->set_context($coursecontext);
    $PAGE->set_title($course->shortname . ': ' . get_string('pluginname', 'mod_whatsappmb'));
    $PAGE->set_heading($course->fullname);

    // Get all instances of WhatsAppMB in this course
    $instances = get_all_instances_in_course('whatsappmb', $course);

    if (!$instances) {
        echo $OUTPUT->notification(get_string('noinstances', 'mod_whatsappmb'), 'notifyproblem');
    } else {
        $table = new html_table();
        $table->head = [
            get_string('name'),
            get_string('description'),
            get_string('lastmodified', 'mod_whatsappmb') // Nueva columna para la fecha
        ];
        $table->data = [];

        foreach ($instances as $instance) {
            $url = new moodle_url('/mod/whatsappmb/view.php', ['id' => $instance->coursemodule]);
            $name = html_writer::link($url, format_string($instance->name));
            $description = format_text($instance->intro, $instance->introformat);
            // Formatear la fecha de última modificación
            $lastmodified = userdate($instance->timemodified, get_string('strftimedatetime', 'langconfig'));
            $table->data[] = [$name, $description, $lastmodified];
        }

        echo html_writer::table($table);
    }
} else {
    // Case 2: No course ID provided, show all courses with WhatsAppMB instances
    require_capability('moodle/site:viewparticipants', $systemcontext);

    $sql = "SELECT c.id, c.fullname, c.shortname
            FROM {course} c
            JOIN {whatsappmb} w ON w.course = c.id
            GROUP BY c.id, c.fullname, c.shortname
            ORDER BY c.fullname";

    $courses = $DB->get_records_sql($sql);

    if (!$courses) {
        echo $OUTPUT->notification(get_string('nocourses', 'mod_whatsappmb'), 'notifyproblem');
    } else {
        echo $OUTPUT->heading(get_string('courseswithwhatsappmb', 'mod_whatsappmb'), 2);
        $table = new html_table();
        $table->head = [get_string('coursename', 'mod_whatsappmb')];
        $table->data = [];

        foreach ($courses as $course) {
            $url = new moodle_url('/mod/whatsappmb/index.php', ['id' => $course->id]);
            $courselink = html_writer::link($url, format_string($course->fullname));
            $table->data[] = [$courselink];
        }

        echo html_writer::table($table);
    }
}

echo $OUTPUT->footer();