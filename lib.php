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
 * Library functions for the mod_whatsappmb plugin.
 *
 * @package   mod_whatsappmb
 * @copyright 2025 Marcial Cahuaya | Marbot
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Add a new whatsappmb instance.
 *
 * @param stdClass $whatsappmb Submitted data from the form in mod_form.php.
 * @param mod_whatsappmb_mod_form|null $mform The form instance (optional).
 * @return int The id of the newly inserted whatsappmb record.
 */
function whatsappmb_add_instance($whatsappmb, $mform = null) {
    global $DB;

    $whatsappmb->intro = $whatsappmb->intro ?? '';
    $whatsappmb->introformat = $whatsappmb->introformat ?? FORMAT_MOODLE;
    $whatsappmb->timecreated = time();
    $whatsappmb->timemodified = time();
    $whatsappmb->linktype = $whatsappmb->linktype ?? 'personal';
    $whatsappmb->whatsappnumber = $whatsappmb->whatsappnumber ?? '';
    $whatsappmb->message = $whatsappmb->message ?? '';
    $whatsappmb->grouplink = $whatsappmb->grouplink ?? '';

    return $DB->insert_record('whatsappmb', $whatsappmb);
}

/**
 * Update an existing whatsappmb instance.
 *
 * @param stdClass $whatsappmb Submitted data from the form in mod_form.php.
 * @return bool True on success.
 */
function whatsappmb_update_instance($whatsappmb) {
    global $DB;

    $whatsappmb->intro = $whatsappmb->intro ?? '';
    $whatsappmb->introformat = $whatsappmb->introformat ?? FORMAT_MOODLE;
    $whatsappmb->timemodified = time();
    $whatsappmb->id = $whatsappmb->instance;

    return $DB->update_record('whatsappmb', $whatsappmb);
}

/**
 * Delete an existing whatsappmb instance.
 *
 * @param int $id The id of the whatsappmb instance to delete.
 * @return bool True on success.
 */
function whatsappmb_delete_instance($id) {
    global $DB;

    return $DB->delete_records('whatsappmb', ['id' => $id]);
}

/**
 * Add information about the course module to the cached information.
 *
 * The activity will always link to view.php (a standard internal redirect page)
 * so that events, logs and completion tracking are handled server-side, without
 * relying on JavaScript.
 *
 * @param stdClass $coursemodule The course module record.
 * @return cached_cm_info|false Cached course module info or false on error.
 */
function whatsappmb_get_coursemodule_info($coursemodule) {
    global $DB;

    $whatsappmb = $DB->get_record('whatsappmb', ['id' => $coursemodule->instance], 'id, name, intro, introformat');
    if (!$whatsappmb) {
        return false;
    }

    $info = new cached_cm_info();
    $info->name = $whatsappmb->name;

    if ($coursemodule->showdescription) {
        $info->content = format_module_intro('whatsappmb', $whatsappmb, $coursemodule->id, false);
    }

    return $info;
}

/**
 * Mark the activity as viewed: trigger the viewed event and complete it for the user.
 *
 * @param stdClass $whatsappmb The whatsappmb record.
 * @param stdClass $course The course record.
 * @param stdClass $cm The course module record.
 * @param context_module $context The module context.
 * @return void
 */
function whatsappmb_view($whatsappmb, $course, $cm, $context) {
    $event = \mod_whatsappmb\event\course_module_viewed::create([
        'objectid' => $whatsappmb->id,
        'context' => $context,
        'courseid' => $course->id,
    ]);
    $event->add_record_snapshot('course', $course);
    $event->add_record_snapshot('whatsappmb', $whatsappmb);
    $event->trigger();

    $completion = new completion_info($course);
    $completion->set_module_viewed($cm);
}

/**
 * Indicates the features supported by this module.
 *
 * @uses FEATURE_IDNUMBER
 * @uses FEATURE_GROUPS
 * @uses FEATURE_GROUPINGS
 * @uses FEATURE_MOD_INTRO
 * @uses FEATURE_COMPLETION_TRACKS_VIEWS
 * @uses FEATURE_GRADE_HAS_GRADE
 * @uses FEATURE_GRADE_OUTCOMES
 * @uses FEATURE_BACKUP_MOODLE2
 * @uses FEATURE_SHOW_DESCRIPTION
 * @uses FEATURE_MOD_PURPOSE
 * @param string $feature FEATURE_xx constant for requested feature.
 * @return mixed True if the feature is supported, null if unknown.
 */
function whatsappmb_supports($feature) {
    switch ($feature) {
        case FEATURE_IDNUMBER:
            return true;
        case FEATURE_GROUPS:
        case FEATURE_GROUPINGS:
            return false;
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS:
            return true;
        case FEATURE_GRADE_HAS_GRADE:
        case FEATURE_GRADE_OUTCOMES:
            return false;
        case FEATURE_MOD_ARCHETYPE:
            return MOD_ARCHETYPE_OTHER;
        case FEATURE_BACKUP_MOODLE2:
            return true;
        case FEATURE_NO_VIEW_LINK:
            return false;
        case FEATURE_SHOW_DESCRIPTION:
            return true;
        case FEATURE_MOD_PURPOSE:
            return MOD_PURPOSE_COMMUNICATION;
        default:
            return null;
    }
}
