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
 * Form definition for the mod_whatsappmb activity in Moodle.
 *
 * @package   mod_whatsappmb
 * @copyright 2025 Marcial Cahuaya | Marbot
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/moodleform_mod.php');

class mod_whatsappmb_mod_form extends moodleform_mod {

    public function definition() {
        $mform = $this->_form;

        // General section
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Activity name
        $mform->addElement('text', 'name', get_string('name'), ['size' => '64']);
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');

        // Standard description field
        $this->standard_intro_elements();

        // Select type of link (Personal Number or Group Link)
        $mform->addElement('select', 'linktype', get_string('linktype', 'whatsappmb'), [
            'personal' => get_string('personalnumber', 'whatsappmb'),
            'group' => get_string('grouplink', 'whatsappmb')
        ]);
        $mform->setDefault('linktype', 'personal');

        // WhatsApp number field (only for personal links)
        $mform->addElement('text', 'whatsappnumber', get_string('whatsappnumber', 'whatsappmb'), [
            'size' => '15',
            'placeholder' => '+1234567890' // Example format for the user
        ]);
        $mform->setType('whatsappnumber', PARAM_TEXT);
        $mform->hideIf('whatsappnumber', 'linktype', 'eq', 'group'); // Hide if "group" is selected

        // Message field (only for personal links, optional)
        $mform->addElement('textarea', 'message', get_string('message', 'whatsappmb'));
        $mform->setType('message', PARAM_TEXT);
        $mform->hideIf('message', 'linktype', 'eq', 'group');

        // Group link field (only for group links)
        $mform->addElement('text', 'grouplink', get_string('grouplink', 'whatsappmb'), [
            'size' => '60',
            'placeholder' => 'https://chat.whatsapp.com/XXXXXXX' // Example format for WhatsApp group links
        ]);
        $mform->setType('grouplink', PARAM_URL);
        $mform->hideIf('grouplink', 'linktype', 'eq', 'personal'); // Hide if "personal" is selected

        // Standard course module elements
        $this->standard_coursemodule_elements();

        // Add save and cancel buttons
        $this->add_action_buttons(true, false);
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if ($data['linktype'] === 'personal') {
            // Validate phone number format
            if (empty(trim($data['whatsappnumber']))) {
                $errors['whatsappnumber'] = get_string('required');
            } elseif (!preg_match('/^\+?[1-9]\d{1,14}$/', $data['whatsappnumber'])) {
                $errors['whatsappnumber'] = get_string('invalidnumber', 'whatsappmb');
            }
        }

        if ($data['linktype'] === 'group') {
            // Validate group link format
            if (empty(trim($data['grouplink']))) {
                $errors['grouplink'] = get_string('required');
            } elseif (!filter_var($data['grouplink'], FILTER_VALIDATE_URL) || !preg_match('/^https:\/\/chat\.whatsapp\.com\/[A-Za-z0-9]+$/', $data['grouplink'])) {
                $errors['grouplink'] = get_string('invalidgrouplink', 'whatsappmb');
            }
        }

        return $errors;
    }
}
