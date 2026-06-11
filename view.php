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
 * Redirects users to the WhatsApp link configured for this activity.
 *
 * @package   mod_whatsappmb
 * @copyright 2025 Marcial Cahuaya | Marbot
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');

$id = required_param('id', PARAM_INT);

$cm = get_coursemodule_from_id('whatsappmb', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
$whatsappmb = $DB->get_record('whatsappmb', ['id' => $cm->instance], '*', MUST_EXIST);

require_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/whatsappmb:view', $context);

$PAGE->set_url('/mod/whatsappmb/view.php', ['id' => $cm->id]);

// Trigger the viewed event and update completion state.
whatsappmb_view($whatsappmb, $course, $cm, $context);

// Build the target WhatsApp URL based on the configured link type.
if ($whatsappmb->linktype === 'personal') {
    $number = trim($whatsappmb->whatsappnumber);
    $message = urlencode(trim($whatsappmb->message));
    $whatsapplink = "https://wa.me/{$number}?text={$message}";
} else {
    $grouplink = trim($whatsappmb->grouplink);
    if (!preg_match('~^https?://~i', $grouplink)) {
        $grouplink = 'https://' . $grouplink;
    }
    $whatsapplink = $grouplink;
}

redirect($whatsapplink);
