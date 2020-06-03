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
 * Define all the backup steps that will be used by the backup_evoting_activity_task
 */

/**
 * Define the complete evoting structure for backup, with file and id annotations
 */
class backup_evoting_activity_structure_step extends backup_activity_structure_step {

    protected function define_structure() {

        // To know if we are including userinfo
        $userinfo = $this->get_setting_value('userinfo');

        // Define each element separated
        $evoting = new backup_nested_element('evoting', array('id'), array(
            'name', 'intro', 'introformat', 'publish',
            'showresults', 'display', 'allowupdate', 'showunanswered',
            'limitanswers', 'timeopen', 'timeclose', 'timemodified',
            'completionsubmit'));

        $questions = new backup_nested_element('questions');

        $question = new backup_nested_element('question', array('id'), array(
            'evotingid', 'evotinggraphicid', 'number', 'name', 'activ', 'multipleanswers'));
			
        $options = new backup_nested_element('options');

        $option = new backup_nested_element('option', array('id'), array('evotingquestionid',
            'text', 'maxanswers', 'timemodified', 'correct'));

      
        // Build the tree
        $evoting->add_child($questions);
        $questions->add_child($question);
		
        $evoting->add_child($options);
        $options->add_child($option);

        // Define sources
        $evoting->set_source_table('evoting', array('id' => backup::VAR_ACTIVITYID));
		
		$question->set_source_sql('
            SELECT *
            FROM {evoting_questions}
            WHERE evotingid = ?
            ORDER BY id',
            array(backup::VAR_PARENTID));
			
        $option->set_source_sql('
            SELECT o.* 
            FROM {evoting_options} o, {evoting_questions} q, {evoting} p
            WHERE p.id = q.evotingid 
            AND o.evotingquestionid = q.id 
            AND p.id = ?
            ORDER BY p.id',
            array(backup::VAR_PARENTID));

        // Define file annotations
        $evoting->annotate_files('mod_evoting', 'intro', null); // This file area hasn't itemid
        $question->annotate_files('mod_evoting', 'questioneditor', "id");

        // Return the root element (evoting), wrapped into standard activity structure
        return $this->prepare_activity_structure($evoting);
    }
}
