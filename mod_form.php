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
 * Form definition for the mod_whatsappmb activity.
 *
 * @package   mod_whatsappmb
 * @copyright 2025 Marcial Cahuaya | Marbot
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/moodleform_mod.php');

/**
 * Form class for adding and editing whatsappmb activity instances.
 */
class mod_whatsappmb_mod_form extends moodleform_mod {
    /**
     * Defines the form fields.
     */
    public function definition() {
        $mform = $this->_form;

        // General section.
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Activity name.
        $mform->addElement('text', 'name', get_string('name'), ['size' => '64']);
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');

        // Standard description field.
        $this->standard_intro_elements();

        // Select type of link (personal number or group link).
        $mform->addElement('select', 'linktype', get_string('linktype', 'mod_whatsappmb'), [
            'personal' => get_string('personalnumber', 'mod_whatsappmb'),
            'group' => get_string('grouplink', 'mod_whatsappmb'),
        ]);
        $mform->setDefault('linktype', 'personal');

        // WhatsApp number field (only for personal links).
        $mform->addElement('text', 'whatsappnumber', get_string('whatsappnumber', 'mod_whatsappmb'), [
            'size' => '15',
            'placeholder' => '+1234567890',
        ]);
        $mform->setType('whatsappnumber', PARAM_TEXT);
        $mform->hideIf('whatsappnumber', 'linktype', 'eq', 'group');

        // Message field (only for personal links, optional).
        $mform->addElement('textarea', 'message', get_string('message', 'mod_whatsappmb'));
        $mform->setType('message', PARAM_TEXT);
        $mform->hideIf('message', 'linktype', 'eq', 'group');

        // Group link field (only for group links).
        $mform->addElement('text', 'grouplink', get_string('grouplink', 'mod_whatsappmb'), [
            'size' => '60',
            'placeholder' => 'https://chat.whatsapp.com/XXXXXXX',
        ]);
        $mform->setType('grouplink', PARAM_URL);
        $mform->hideIf('grouplink', 'linktype', 'eq', 'personal');

        // Standard course module elements.
        $this->standard_coursemodule_elements();

        // Save and cancel buttons.
        $this->add_action_buttons(true, false);
    }

    /**
     * Custom validation for the activity form.
     *
     * @param array $data Form data.
     * @param array $files Uploaded files.
     * @return array Array of validation errors.
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if ($data['linktype'] === 'personal') {
            if (empty(trim($data['whatsappnumber']))) {
                $errors['whatsappnumber'] = get_string('required');
            } else if (!preg_match('/^\+?[1-9]\d{1,14}$/', $data['whatsappnumber'])) {
                $errors['whatsappnumber'] = get_string('invalidnumber', 'mod_whatsappmb');
            }
        }

        if ($data['linktype'] === 'group') {
            if (empty(trim($data['grouplink']))) {
                $errors['grouplink'] = get_string('required');
            } else if (
                !filter_var($data['grouplink'], FILTER_VALIDATE_URL) ||
                    !preg_match('/^https:\/\/chat\.whatsapp\.com\/[A-Za-z0-9]+$/', $data['grouplink'])
            ) {
                $errors['grouplink'] = get_string('invalidgrouplink', 'mod_whatsappmb');
            }
        }

        return $errors;
    }
}
