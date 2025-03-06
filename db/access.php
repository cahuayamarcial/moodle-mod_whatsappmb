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
// along with Moodle. If not, see <https://www.gnu.org/licenses/>.

/**
 * Capabilities definition for the mod_whatsappmb module in Moodle.
 *
 * @package   mod_whatsappmb
 * @copyright 2025 Marcial Cahuaya | Marbot
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Defines the capabilities available for the mod_whatsappmb plugin.
 *
 * The `$capabilities` array specifies different access levels for users
 * based on their roles (e.g., students, teachers, managers).
 */
$capabilities = [

    // Capability to add a new instance of the WhatsAppMB module.
    'mod/whatsappmb:addinstance' => [
        'riskbitmask' => RISK_XSS, // Potential risk of cross-site scripting (XSS).
        'captype' => 'write', // Allows modifications to the course structure.
        'contextlevel' => CONTEXT_COURSE, // Applies at the course level.
        'archetypes' => [
            'editingteacher' => CAP_ALLOW, // Teachers with editing permissions can add the module.
            'manager' => CAP_ALLOW, // Managers can also add the module.
        ],
        'clonepermissionsfrom' => 'moodle/course:manageactivities', // Inherits activity management permissions.
    ],

    // Capability to view the WhatsAppMB module.
    'mod/whatsappmb:view' => [
        'captype' => 'read', // Read-only access.
        'contextlevel' => CONTEXT_MODULE, // Applies at the module level.
        'archetypes' => [
            'guest' => CAP_ALLOW, // Guests can view the module.
            'student' => CAP_ALLOW, // Students can view the module.
            'teacher' => CAP_ALLOW, // Teachers can view the module.
            'editingteacher' => CAP_ALLOW, // Editing teachers can view the module.
            'manager' => CAP_ALLOW, // Managers can view the module.
        ],
    ],
];

