<?php
namespace mod_whatsappmb\event;

defined('MOODLE_INTERNAL') || die();

class course_module_viewed extends \core\event\course_module_viewed {
    protected function init() {
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
        $this->data['objecttable'] = 'whatsappmb';
    }

    public static function get_objectid_mapping() {
        return ['db' => 'whatsappmb', 'restore' => 'whatsappmb'];
    }

    public function get_description() {
        return "The user with id {$this->userid} viewed the WhatsApp activity with id {$this->objectid} in course {$this->courseid}.";
    }

    public function get_url() {
        return new \moodle_url('/mod/whatsappmb/view.php', ['id' => $this->contextinstanceid]);
    }

    protected function get_legacy_logdata() {
        return [$this->courseid, 'whatsappmb', 'view', 'view.php?id=' . $this->contextinstanceid, $this->objectid, $this->contextinstanceid];
    }
}