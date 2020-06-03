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

/**
 * Define all the restore steps that will be used by the restore_evoting_activity_task
 */

/**
 * Structure step to restore one evoting activity
 */
class restore_evoting_activity_structure_step extends restore_activity_structure_step {

    protected function define_structure() {

        $paths = array();
        $userinfo = $this->get_setting_value('userinfo');

        $paths[] = new restore_path_element('evoting', '/activity/evoting');
        $paths[] = new restore_path_element('evoting_questions', '/activity/evoting/questions/question');
		$paths[] = new restore_path_element('evoting_options', '/activity/evoting/options/option');

        // Return the paths wrapped into standard activity structure
        return $this->prepare_activity_structure($paths);
    }

    protected function process_evoting($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();

        $data->timeopen = $this->apply_date_offset($data->timeopen);
        $data->timeclose = $this->apply_date_offset($data->timeclose);
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        // insert the evoting record
        $newitemid = $DB->insert_record('evoting', $data);
        // immediately after inserting "activity" record, call this
        $this->apply_activity_instance($newitemid);
    }

	 protected function process_evoting_questions($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->evotingid = $this->get_new_parentid('evoting');
        $newitemid = $DB->insert_record('evoting_questions', $data);

        $this->set_mapping('evoting_questions', $oldid, $newitemid, true);
     }

    protected function process_evoting_options($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->evotingquestionid = $this->get_mappingid('evoting_questions', $data->evotingquestionid);
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        $newitemid = $DB->insert_record('evoting_options', $data);
        $this->set_mapping('evoting_options', $oldid, $newitemid);
    }

    

    protected function after_execute() {
        // Add evoting related files, no need to match by itemname (just internally handled context)
        $this->add_related_files('mod_evoting', 'intro', null);
        $this->add_related_files('mod_evoting', 'questioneditor', 'evoting_questions');
    }
}
