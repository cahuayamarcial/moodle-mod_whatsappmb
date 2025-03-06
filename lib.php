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
 * Library functions for the mod_whatsappmb plugin.
 *
 * @package   mod_whatsappmb
 * @copyright 2025 Marcial Cahuaya | Marbot
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Adds a new instance of the WhatsAppMB activity.
 *
 * @param object $whatsappmb The data object containing instance details.
 * @param object|null $mform Optional form instance.
 * @return int The ID of the newly inserted record.
 */
function whatsappmb_add_instance($whatsappmb, $mform = null) {
    global $DB;

    // Ensure default values
    $whatsappmb->intro = $whatsappmb->intro ?? '';
    $whatsappmb->introformat = $whatsappmb->introformat ?? FORMAT_MOODLE;
    $whatsappmb->timecreated = time();
    $whatsappmb->timemodified = time();
    $whatsappmb->linktype = $whatsappmb->linktype ?? 'personal';
    $whatsappmb->whatsappnumber = $whatsappmb->whatsappnumber ?? '';
    $whatsappmb->message = $whatsappmb->message ?? '';
    $whatsappmb->grouplink = $whatsappmb->grouplink ?? '';

    // Insert the new instance into the database
    return $DB->insert_record('whatsappmb', $whatsappmb);
}

/**
 * Updates an existing instance of the WhatsAppMB activity.
 *
 * @param object $whatsappmb The data object containing updated instance details.
 * @return bool True if the update was successful, false otherwise.
 */
function whatsappmb_update_instance($whatsappmb) {
    global $DB;

    // Ensure default values
    $whatsappmb->intro = $whatsappmb->intro ?? '';
    $whatsappmb->introformat = $whatsappmb->introformat ?? FORMAT_MOODLE;
    $whatsappmb->timemodified = time();
    $whatsappmb->id = $whatsappmb->instance;

    // Update the existing record in the database
    return $DB->update_record('whatsappmb', $whatsappmb);
}

/**
 * Deletes an instance of the WhatsAppMB activity.
 *
 * @param int $id The ID of the instance to delete.
 * @return bool True if the deletion was successful, false otherwise.
 */
function whatsappmb_delete_instance($id) {
    global $DB;

    // Delete the instance from the database
    return $DB->delete_records('whatsappmb', ['id' => $id]);
}

/**
 * Retrieves course module information for display.
 *
 * @param object $coursemodule The course module object.
 * @return cached_cm_info Course module information including dynamic link handling.
 */
function whatsappmb_get_coursemodule_info($coursemodule) {
    global $DB;

    // Retrieve the WhatsAppMB instance from the database
    $whatsappmb = $DB->get_record('whatsappmb', ['id' => $coursemodule->instance], '*', MUST_EXIST);

    // Create a new course module info object
    $info = new cached_cm_info();
    $info->name = $whatsappmb->name;

    // Construct the WhatsApp link based on the type
    if ($whatsappmb->linktype === 'personal') {
        $number = $whatsappmb->whatsappnumber;
        $message = urlencode($whatsappmb->message);
        $link = "https://wa.me/{$number}?text={$message}";
    } else {
        $link = $whatsappmb->grouplink;
    }

    // Set the onclick event to open the WhatsApp link in a new tab
    $info->onclick = "window.open('$link', '_blank'); return false;";

    return $info;
}

/**
 * Indicates that the module supports backup and restore.
 *
 * @param string $feature The feature to check.
 * @return bool|null True if the feature is supported, null otherwise.
 */
function whatsappmb_supports($feature) {
    switch ($feature) {
        case FEATURE_IDNUMBER:
            return true; // Allows ID numbers for course modules.

        case FEATURE_GROUPS:
        case FEATURE_GROUPINGS:
            return false; // This module does not support groups.

        case FEATURE_MOD_INTRO:
            return true; // Supports an introductory text.

        case FEATURE_COMPLETION_TRACKS_VIEWS:
            return false; // No tracking of views for completion.

        case FEATURE_GRADE_HAS_GRADE:
        case FEATURE_GRADE_OUTCOMES:
            return false; // No grading in this module.

        case FEATURE_MOD_ARCHETYPE:
            return MOD_ARCHETYPE_RESOURCE; // This module behaves like a resource.

        case FEATURE_BACKUP_MOODLE2:
            return true; // Supports Moodle backup and restore.

        case FEATURE_NO_VIEW_LINK:
            return true; // This module does not require a separate view page.

        case FEATURE_MOD_PURPOSE:
            return MOD_PURPOSE_COMMUNICATION; // Categorized as a communication module.

        default:
            return null; // Unknown feature.
    }
}