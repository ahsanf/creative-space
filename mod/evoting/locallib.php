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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Version information
 *
 * @package    mod_evoting
 * @copyright  2016 Cyberlearn
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Get the options editor
 * @param $context
 * @return array
 */
function evoting_get_editor_options($context) {
    global $CFG;
    return array('subdirs'=>1, 'maxbytes'=>$CFG->maxbytes, 'maxfiles'=>-1, 'changeformat'=>1, 'context'=>$context, 'noclean'=>1, 'trusttext'=>0);
}

/**
 * Check instance
 * @return id
 */
function evoting_check_instance() {
    global $DB, $COURSE, $USER ;

    $query = "SELECT id from {modules} WHERE name = ?";
    $moduleid = $DB->get_fieldset_sql($query,$param=array('evoting'));
    $moduleid = $moduleid[0];

    $query = "SELECT id from {course_modules} WHERE course = ? and module=?";

    $param = array($COURSE->id, $moduleid);
    $editid = $DB->get_fieldset_sql($query, $param);
    $editid = $editid[0];

    return $editid ;

}
