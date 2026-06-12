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
 * Mobile output handler for mod_whatsappmb.
 *
 * @package   mod_whatsappmb
 * @copyright 2025 Marcial Cahuaya | Marbot
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_whatsappmb\output;

/**
 * Returns the template rendered inside the Moodle Mobile App when the user
 * taps a WhatsApp activity. The template shows the activity description and
 * an "Open WhatsApp" button that hands off to the system browser, which then
 * deep-links to the installed WhatsApp app (wa.me / chat.whatsapp.com).
 */
class mobile {
    /**
     * Build the activity view template for the Moodle Mobile App.
     *
     * @param array $args Arguments from the app — at least 'cmid'.
     * @return array
     */
    public static function mobile_course_view(array $args): array {
        global $DB, $CFG;
        require_once($CFG->dirroot . '/mod/whatsappmb/lib.php');

        $args = (object)$args;
        $cmid = (int)$args->cmid;

        $cm = get_coursemodule_from_id('whatsappmb', $cmid, 0, false, MUST_EXIST);
        $course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
        $whatsappmb = $DB->get_record('whatsappmb', ['id' => $cm->instance], '*', MUST_EXIST);
        $context = \context_module::instance($cm->id);

        require_capability('mod/whatsappmb:view', $context);

        // Trigger the viewed event and update completion just like view.php does.
        whatsappmb_view($whatsappmb, $course, $cm, $context);

        if ($whatsappmb->linktype === 'personal') {
            $number = trim($whatsappmb->whatsappnumber);
            $message = urlencode(trim($whatsappmb->message));
            $url = "https://wa.me/{$number}?text={$message}";
        } else {
            $url = trim($whatsappmb->grouplink);
            if (!preg_match('~^https?://~i', $url)) {
                $url = 'https://' . $url;
            }
        }

        $description = '';
        if (!empty($whatsappmb->intro)) {
            $description = format_module_intro('whatsappmb', $whatsappmb, $cm->id, false);
        }

        $template = '
<div class="ion-padding">
    <div *ngIf="description" [innerHTML]="description"></div>
    <ion-button expand="block" class="ion-margin-top" core-link [href]="url" [inApp]="false" capture="false" autoLogin="no">
        <ion-icon name="logo-whatsapp" slot="start"></ion-icon>
        {{ \'plugin.mod_whatsappmb.openwhatsapp\' | translate }}
    </ion-button>
</div>';

        return [
            'templates' => [
                ['id' => 'main', 'html' => $template],
            ],
            'javascript' => '',
            'otherdata' => [
                'description' => $description,
                'url' => $url,
            ],
            'files' => [],
        ];
    }
}
