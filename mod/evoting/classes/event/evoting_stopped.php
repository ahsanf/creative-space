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
// GNU General Public License fo r more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Version information
 *
 * @package    mod_evoting
 * @copyright  2016 Cyberlearn
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_evoting\event;

defined('MOODLE_INTERNAL') || die();

class evoting_stopped extends \core\event\base {
    /**
     * Init method.
     *
     * @return void
     */
    protected function init() {
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
        $this->data['objecttable'] = 'evoting';
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return get_string('eventevotinguserid', 'mod_evoting') .  ' ' . $this->userid . ' ' . get_string('eventevotingobjectidstopped', 'mod_evoting') . ' ' . $this->objectid . ' ' . get_string('eventusercontextid', 'mod_evoting') . ' ' .$this->contextinstanceid;
    }

    /**
     * Return localised event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventevotingstopped', 'mod_evoting');
    }

    /**
     * Get URL related to the action
     *
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url('/mod/evoting/view.php', array('id' => $this->contextinstanceid));
    }

    /**
     * Return the legacy event log data.
     *
     * @return array|null
     */
    protected function get_legacy_logdata() {
        $logurl = substr($this->get_url()->out_as_local_url(), strlen('/mod/evoting/'));

        return array($this->courseid, 'evoting', 'stop evoting', $logurl, $this->contextinstanceid);
    }

    /**
     * Custom validation.
     *
     * @throws \coding_exception
     * @return void
     */
    protected function validate_data() {
        parent::validate_data();
    }

    public static function get_objectid_mapping() {
        return array('db' => 'evoting', 'restore' => 'evoting');
    }

}
