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
 * Lists all whatsappmb instances in a given course.
 *
 * @package   mod_whatsappmb
 * @copyright 2025 Marcial Cahuaya | Marbot
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

$id = required_param('id', PARAM_INT);

$course = $DB->get_record('course', ['id' => $id], '*', MUST_EXIST);
require_course_login($course, true);

$coursecontext = context_course::instance($course->id);

$PAGE->set_url('/mod/whatsappmb/index.php', ['id' => $course->id]);
$PAGE->set_context($coursecontext);
$PAGE->set_pagelayout('incourse');
$PAGE->set_title($course->shortname . ': ' . get_string('pluginname', 'mod_whatsappmb'));
$PAGE->set_heading($course->fullname);

// Log the course module instance list viewed event.
$event = \mod_whatsappmb\event\course_module_instance_list_viewed::create([
    'context' => $coursecontext,
    'courseid' => $course->id,
]);
$event->trigger();

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('modulenameplural', 'mod_whatsappmb'));

// Retrieve all whatsappmb instances available in this course.
$instances = get_all_instances_in_course('whatsappmb', $course);

if (empty($instances)) {
    notice(get_string('noinstances', 'mod_whatsappmb'), new moodle_url('/course/view.php', ['id' => $course->id]));
} else {
    $table = new html_table();
    $table->head = [
        get_string('name'),
        get_string('description'),
        get_string('lastmodified', 'mod_whatsappmb'),
    ];
    $table->data = [];

    foreach ($instances as $instance) {
        $url = new moodle_url('/mod/whatsappmb/view.php', ['id' => $instance->coursemodule]);
        $name = html_writer::link($url, format_string($instance->name));
        $description = format_text($instance->intro, $instance->introformat);
        $lastmodified = userdate($instance->timemodified, get_string('strftimedatetime', 'langconfig'));
        $table->data[] = [$name, $description, $lastmodified];
    }

    echo html_writer::table($table);
}

echo $OUTPUT->footer();
