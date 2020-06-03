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

require_once ($CFG -> dirroot . '/course/moodleform_mod.php');
require_once($CFG->dirroot . '/mod/evoting/lib.php');
require_once($CFG->dirroot.'/mod/evoting/locallib.php');

class mod_evoting_mod_form extends moodleform_mod {

	/**
	 * Defines forms elements
	 */
	public function definition() {

		global $CFG, $DB, $PAGE, $USER, $COURSE;

		$mform = &$this -> _form;

		$PAGE->requires->jquery();
		$PAGE->requires->js('/mod/evoting/js/google-jsapi.js');
		$PAGE->requires->strings_for_js(array('noaddChoice',  'confirmDelete'), 'evoting');
		$PAGE->requires->js('/mod/evoting/js/mod_form.js');
		$PAGE->requires->js_init_call('M.mod_evoting.form_init', array(optional_param('update', 0, PARAM_INT)));

		$mform -> addElement('hidden', 'idcreator', $USER -> id);
		$mform -> setType('idcreator', PARAM_TEXT);

		$mform -> addElement('hidden', 'idCourse', $COURSE -> id);
		$mform -> setType('idCourse', PARAM_TEXT);

		// Adding the "general" fieldset, where all the common settings are showed.
		$mform -> addElement('header', 'general', get_string('general', 'form'));

		// Adding the standard "name" field.
		$mform -> addElement('text', 'name', get_string('evotingname', 'evoting'), array('size' => '64'));
		if (!empty($CFG -> formatstringstriptags)) {
			$mform -> setType('name', PARAM_TEXT);
		} else {
			$mform -> setType('name', PARAM_CLEAN);
		}

		// Poll name required
		$mform -> addRule('name', null, 'required', null, 'client');
		$mform -> addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

		// Adding the standard "Description" field.
		$this -> standard_intro_elements(false, get_string('questiondescription', 'evoting'));

		// Adding  "Question" section.
		$mform -> addElement('header', 'evotingfieldset', get_string('question', 'evoting'));

		// Only add one question
		$repeatQuestionArray = array();

		// If Add Question (Default => 1 question is open) if update,  questions from database are open
		if ($this -> _instance) {
			$repeatQuestionNo = $DB -> count_records('evoting_questions', array('evotingid' => $this -> _instance));
		} else {
			$repeatQuestionNo = 1;
		}

		// Create the field Name Question with hidden id
		$repeatQuestionArray[] =  $mform -> createElement('html','<div  class="container_question">');
		$repeatQuestionArray[]= $mform -> createElement('editor', 'questionname', get_string('questionno', 'evoting'), 'class="evotingQuestionTextarea"',  evoting_get_editor_options($this->context));
		$repeatQuestionArray[] = $mform -> createElement('hidden', 'questionnameid', 0);
		$mform -> setType('questionnameid', PARAM_TEXT);
		$mform->setType('questionname', PARAM_RAW);

		// Multiple response voting checkbox
		$repeatQuestionArray[] =  $mform->createElement('advcheckbox', 'checkbox_multiple_answer', get_string('checkbox_multiple_answer', 'evoting'), '','class="evotingCheckBoxMultipleAnswer"', array(0, 1));

		$repeatQuestionArray[] =  $mform -> createElement('html','<div class="container_choices">');

		$repeatQuestionArray[] =  $mform -> createElement('html',
			'<div class="header_choice">
				<div class="header_label">'.
			get_string('choice', 'evoting').'
				</div>
				<div class="header_text">'.
			get_string('text', 'evoting').'
				</div>
				<div class="header_correct">'.
			get_string('selectanswer', 'evoting').'
				</div>
			</div>');

		// Create and set type for the field OptionName and OptionID
		$maxChoice = get_config('evoting', 'evoting_number_choice');
		$mform -> addElement('hidden', 'max_choice', $maxChoice);
		$mform -> setType('max_choice', PARAM_INT);

		for ($i = 0; $i < $maxChoice; $i++) {
			$repeatQuestionArray[] =  $mform -> createElement('html','<div class="container_choice">');
			$repeatQuestionArray[] = $mform -> createElement('text', 'option' . $i, $i + 1, 'class="evotingQuestionText" maxlength="90" ');
			$repeatQuestionArray[] = $mform -> createElement('hidden', 'optionid' . $i, 0);
			$repeatQuestionArray[] = $mform->createElement('advcheckbox', 'right' . $i,'', '',array('group' => 1), array(0, 1));
			$repeatQuestionArray[] =  $mform -> createElement('html','</div>');
			$mform -> setType('option' . $i, PARAM_TEXT);
			$mform -> setType('optionid' . $i, PARAM_INT);
		}

		// Add choice question button
		$repeatQuestionArray[] =  $mform -> createElement('html','<div class="container_add_choice">');
		$repeatQuestionArray[] = $mform -> createElement('link', 'addChoice', get_string('addChoice', 'evoting'), '#', get_string('addChoice', 'evoting'), 'class="addChoice"');
		$repeatQuestionArray[] =  $mform -> createElement('html','</div>');

		$repeatQuestionArray[] =  $mform -> createElement('html','</div>');

		// Add delete question button
		$repeatQuestionArray[] =  $mform -> createElement('html','<div class="container_delete_question">');
		$repeatQuestionArray[] = $mform -> createElement('link', 'deleteQuestion', get_string('deletequestion', 'evoting'), '#', get_string('deletequestion', 'evoting'), 'class="deleteButton"');
		$repeatQuestionArray[] =  $mform -> createElement('html','</div>');

		// Add separator between questions
		$repeatQuestionArray[] =  $mform -> createElement('html','</div>');

		// Array for Add question
		$repeatelquestions = array();

		// Make questions required and the first two options required
		$repeatelquestions['questionname']['rule'] = 'required';
		$repeatelquestions['option0']['rule'] = 'required';
		$repeatelquestions['option1']['rule'] = 'required';

		// Repeat question button
		$this -> repeat_elements($repeatQuestionArray, $repeatQuestionNo, $repeatelquestions, 'question_repeats', 'question_add_fields', 1, get_string('addquestion', 'evoting'), false);

		// Add standard elements, common to all modules.
		$this -> standard_coursemodule_elements();

		// Add standard buttons, common to all modules.
		$this -> add_action_buttons();

	}

	/**
	 * Preprocess form data
	 * @param $default_values
	 */
	function data_preprocessing(&$default_values) {
		global $DB, $USER, $CFG;

		if (!empty($this -> _instance) && ($questions = $DB -> get_records('evoting_questions', array('evotingid' => $this -> _instance), 'id', 'id, name, multipleanswers'))) {

			$default_values['idcreator'] = $USER -> id;

			// Get questions Name and ID when Update
			$questions = array_values($questions);

			$countQuestion = count($questions);

			for ($i = 0; $i < $countQuestion; $i++) {
				$default_values['questionnameid[' . $i . ']'] = $questions[$i] -> id;
				$draftitem = file_get_submitted_draft_itemid('questioneditor' . $i);
				$default_values['questionname[' . $i . ']']['format'] = 1;
				$default_values['questionname[' . $i . ']']['text'] = file_prepare_draft_area($draftitem, $this->context->id, 'mod_evoting', 'questioneditor', $questions[$i] -> id,  evoting_get_editor_options($this->context),$questions[$i] -> name);
				$default_values['questionname[' . $i . ']']['itemid'] = $draftitem;

				// Checkbox multiple answers
				$default_values['checkbox_multiple_answer' . '[' . $i . ']'] =  $questions[$i] -> multipleanswers;

				// Get Option ID and Text when Update
				$options = $DB -> get_records('evoting_options', array('evotingquestionid' => $questions[$i] -> id), 'id', 'id,text,correct');

				$options = array_values($options);
				$countOptions = count($options);

				for ($j = 0; $j < $countOptions; $j++) {
					$default_values['optionid' . ($j) . '[' . $i . ']'] = $options[$j] -> id;
					$default_values['option' . ($j) . '[' . $i . ']'] = $options[$j] -> text;
					$default_values['right' . ($j) . '[' . $i . ']'] = $options[$j] -> correct;
				}
			}
		}
	}

}
