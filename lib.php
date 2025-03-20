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

function whatsappmb_update_instance($whatsappmb) {
    global $DB;

    $whatsappmb->intro = $whatsappmb->intro ?? '';
    $whatsappmb->introformat = $whatsappmb->introformat ?? FORMAT_MOODLE;
    $whatsappmb->timemodified = time();
    $whatsappmb->id = $whatsappmb->instance;

    return $DB->update_record('whatsappmb', $whatsappmb);
}

function whatsappmb_delete_instance($id) {
    global $DB;

    return $DB->delete_records('whatsappmb', ['id' => $id]);
}

function whatsappmb_get_coursemodule_info($coursemodule) {
    global $DB;

    $whatsappmb = $DB->get_record('whatsappmb', ['id' => $coursemodule->instance], '*', MUST_EXIST);

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

    // URL para registrar el evento usando view.php
    $logurl = new moodle_url('/mod/whatsappmb/view.php', [
        'id' => $coursemodule->id,
        'logonly' => 1
    ]);

    // Usar fetch para registrar el evento en segundo plano y abrir WhatsApp
    $info->onclick = "fetch('$logurl', { method: 'GET' }).catch(err => console.error('Error logging view: ', err)); window.open('$link', '_blank'); return false;";

    return $info;
}

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
            return false;
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