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

require_once ("../../config.php");
require_once ("lib.php");

$id = required_param('id', PARAM_INT);
$timestamp = required_param('ts', PARAM_INT);
$idQuestion = required_param('idQ', PARAM_INT);

if (!$cm = get_coursemodule_from_id('evoting', $id)) {
	print_error('invalidcoursemodule');
}

if (!$course = $DB -> get_record("course", array("id" => $cm -> course))) {
	print_error('coursemisconf');
}

if (!$evoting = evoting_get_evoting($cm -> instance)) {
	print_error('invalidcoursemodule');
}

require_course_login($course, false, $cm);

// Get Contexts
$context = context_module::instance($cm -> id);
$context_course = context_course::instance($course -> id);

$PAGE->requires->jquery();
$PAGE->requires->js('/mod/evoting/js/google-jsapi.js');

// If the user is not at least a teacher show the vote page
if (!has_capability('mod/evoting:openevoting', $context_course)) {
	exit();
} else {
	$PAGE -> set_title(format_string($evoting -> name));
	$PAGE -> set_heading($course -> fullname);
	$PAGE->requires->jquery();
	$PAGE->requires->strings_for_js(array('countvote', 'confirmDelete', 'choice', 'totalvote', 'goodanswer'), 'evoting');
	$PAGE->requires->js('/mod/evoting/js/history.js');

	// Print the page header.
	$PAGE -> set_url('/mod/evoting/view.php', array('id' => $id));
	$PAGE -> set_title(format_string($evoting -> name));
	$PAGE -> set_heading(format_string($course -> fullname));

	// Output starts here
	echo $OUTPUT -> header();

	// Title Poll
	$divTitle = html_writer::start_tag('div', array('id' => 'divTitle', 'style' => 'float:left; text-align: center;'));
	$divTitle .= html_writer::start_tag('h2', array('id' => 'namePoll'));
	$divTitle .= $evoting -> name;
	$divTitle .= html_writer::end_tag('h2');
	$divTitle .= html_writer::empty_tag('span', array('id' => 'introPoll'));
	$divTitle .= file_rewrite_pluginfile_urls($evoting -> intro, 'pluginfile.php', $context->id, 'mod_evoting', "intro", null);
	$divTitle .= html_writer::end_tag('div');
	echo $divTitle;

	echo html_writer::start_tag('div', array('style' => 'clear: both;'));
	echo html_writer::end_tag('div');

	echo $OUTPUT -> heading("<span style='display:none' id='numberQuestion'>" . $evoting -> questions -> number . "</span>", 5);

	echo html_writer::start_tag('div', array('id' => 'questionName'));
	echo file_rewrite_pluginfile_urls($evoting->questions->name, 'pluginfile.php', $context->id, 'mod_evoting', "questioneditor", $evoting->questions->id);
	echo html_writer::end_tag('div');

        // Array from intitiate graphic.
        $arrayDataGraphic = array();

	// History list
	$historyList = evoting_get_history_list($idQuestion);
	$countHistory = sizeof($historyList);

	if ($countHistory > 0) {
		$optionsSelect = array();

		for ($i = 0; $i < $countHistory; $i++) {
			$date = date("d/m/y - G\hi", $historyList[$i] -> timestamp);
			$optionsSelect[$historyList[$i] -> timestamp] = $date;
		}

		if (empty($timestamp)) {
			$timestamp = key($optionsSelect);
		}

		$selectHistory = html_writer::start_div('', array('id' => 'selectZone'));
		$selectHistory .= html_writer::select($optionsSelect, 'selectHistory', $timestamp);
		$selectHistory .= html_writer::empty_tag('a', array( 'id'=>'btnDeleteHistory'));
		$selectHistory .= get_string('delete');
		$selectHistory .= "</a>";
		$selectHistory .= html_writer::end_div();
		echo $selectHistory;

		$history = evoting_get_history($idQuestion, $timestamp);

		//Count the options of the current question
		$countOptions = count($history);
		$sumOptions = 0;
		
		for ($i = 0; $i < $countOptions; $i++) {
			$countVote = $history[$i] -> countvote;
			$sumOptions += intval($countVote);
		}

		// Set to null the html futures options
		$options = '';

		// array color
		$arrayColor = array("#007cb7");

		//Loop to create dynamic options
		for ($i = 0; $i < $countOptions; $i++) {

			$idOption = $history[$i] -> optionid;
			$nameOption = $history[$i] -> optionname;
			$currentOption = $history[$i] -> countvote;

			$options .= html_writer::empty_tag('p', array('class' => 'answerName', 'style' => 'display:inline-block'));
			$options .= ($i + 1) . ") " . $nameOption . ' ';
			$options .= html_writer::start_span('answerCount', array('id' => $idOption, 'style' => 'display:none')) . $currentOption . html_writer::end_span();
			$options .= '</p>';

			// Create array for Graphic chart
			$currentOption = intval($currentOption);
			$percent = round($currentOption / $sumOptions * 100);

			//Set the true answer
			$correctAnswer = $history[$i] -> correct;

			if($correctAnswer == 1 ){
				$trueAnswer = true;
			} else {
				$trueAnswer = false;
			}

			if($sumOptions > 29){
				$nameOption = $nameOption ;
			} else {
				if($currentOption == 0 && $sumOptions == 0){
					$nameOption = $nameOption;
				} else {
					$nameOption = $nameOption . " - " . $currentOption . "/" . $sumOptions;
				}
			}
                        $countvote = get_string('countvote', 'evoting');
                        $arrayDataGraphic[] = array(
                            " " . ($i + 1) . "  ",
                            $currentOption,
                            $arrayColor[0],
                            $nameOption." ",
                            $idOption,
                            "&nbsp;<h5>&nbsp;$countvote<b>&nbsp;$currentOption</b>&nbsp;&nbsp;&nbsp;</h5>",
                            $trueAnswer);
		}

		// Div Chart
		$divChart = html_writer::start_div('div', array('id' => 'chartContainer', 'style' => 'text-align:center')) . html_writer::end_div();

		// Container Chart and countdown div
		$divContainerChartCountDown = html_writer::start_div('div', array('id' => 'divContainerChartCountDown', "style" => "height:450px ; position:relative"));
		$divContainerChartCountDown .= $divChart;

		$divContainerChartCountDown .= html_writer::end_div();
		echo $divContainerChartCountDown;

		//  options
		$divOptions = html_writer::start_div('', array('id' => 'divOptions', 'style' => 'display: none;'));
		$divOptions .= $options; ;
		$divOptions .= html_writer::end_div();
		echo $divOptions;

		// Field ID Module / ID Poll and ID Question next
		echo $inputIdModule = html_writer::empty_tag('input', array('type' => 'hidden', 'id' => 'inputIdModule', 'value' => $id));
		echo $inputIdQuestion = html_writer::empty_tag('input', array('type' => 'hidden', 'id' => 'inputIdQuestion', 'value' => $idQuestion));
		echo $inputIdPoll = html_writer::empty_tag('input', array('type' => 'hidden', 'id' => 'inputIdPoll', 'value' => $evoting -> idpoll));
		echo $inputIdCourse = html_writer::empty_tag('input', array('type'=>'hidden', 'id'=>'inputIdCourse', 'value'=> $course->id));


	} else {
		echo html_writer::start_div('div', array('id' => 'affichage', 'style' => 'text-align:center')) . get_string('noHistory', 'evoting') . html_writer::end_div();
	}

	// Historic button
	echo $div = html_writer::start_div('div', array('id' => 'affichage', 'style' => 'text-align:center'));
	echo html_writer::link('view.php?id=' . $id, get_string('back'), array('id' => 'btnHistoryBack'));
	echo html_writer::end_div();

	// Finish the page.
	$PAGE->requires->js_init_call('M.mod_evoting.history_init', array(json_encode($arrayDataGraphic)));
	echo $OUTPUT -> footer();

}
