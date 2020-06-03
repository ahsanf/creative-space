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
require_once ("locallib.php");

$PAGE->requires->jquery();
$PAGE->requires->js('/mod/evoting/js/google-jsapi.js');

// Get URL QRCODE (client poll)
$path = dirname($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);

$id = required_param('id', PARAM_INT);

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

// Lang course
$lang = current_language();

// HTTP or HTTPS
$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ||
	$_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

// QR CODE URL
define('QRCODE_URL_LINK', "client_poll.php?id=" . $evoting->idpoll . '&lang=' . $lang);

// Hack Cyberlearn QR Code
if(strpos($path, 'cyberlearn') !== false){
	define('QRCODE_URL', evoting_get_tiny_url($protocol . $path ."/". QRCODE_URL_LINK));
} else {
	define('QRCODE_URL', $protocol . $path ."/". QRCODE_URL_LINK);
}

// Get Contexts
$context = context_module::instance($cm->id);
$context_course = context_course::instance($course -> id);

// If the user is not at least a teacher show the vote page
if(!has_capability('mod/evoting:openevoting', $context_course)){
	 header("Location:". QRCODE_URL_LINK);
	exit();
} else{
	// Print the page header.
	$PAGE -> set_url('/mod/evoting/view.php', array('id' => $id));
	$PAGE -> set_title(format_string($evoting -> name));
	$PAGE -> set_heading(format_string($course -> fullname));

	// Output starts here
	$PAGE->requires->jquery();
	$PAGE->requires->strings_for_js(array('countvote', 'end', 'endvote', 'seconds', 'choice', 'start', 'timechoice', 'msgShowQrCode', 'msgHideQrCode', 'totalvote', 'second', 'goodanswer'), 'evoting');
	$PAGE->requires->js('/mod/evoting/js/mdl_poll.js');

	echo $OUTPUT -> header();

	// Title Poll
	$divTitle  = html_writer::start_tag('div', array('id' => 'divTitle', 'style' => 'float:left; text-align: center;'));
	$divTitle .= html_writer::start_tag('h2', array('id'=>'namePoll'));
	$divTitle .= $evoting -> name;
	$divTitle .= html_writer::end_tag('h2'); 
	$divTitle .= html_writer::empty_tag('span', array('id'=>'introPoll'));
	$divTitle .= file_rewrite_pluginfile_urls($evoting -> intro, 'pluginfile.php', $context->id, 'mod_evoting', "intro", null);
	$divTitle .= html_writer::end_tag('div');
	echo $divTitle;
	
	// QR Code and Link
        $qrCodeS = "https://chart.googleapis.com/chart?chs=120x120&cht=qr&chl=" . urlencode(QRCODE_URL) . "&choe=UTF-8&chld=L|1";
	$imgQrCodeS  = html_writer::empty_tag('img', array('src'=>$qrCodeS, 'alt'=>'QrCode', 'style' => 'border: 2px solid #007cb7'));
	$votelinkText =  html_writer::start_tag('h5', array('style' => 'text-align: center; margin:0 auto')).get_string('vote', 'evoting') . html_writer::end_tag('h5');

	$divButtonQRCode  = html_writer::start_div('div', array('id' => 'divQrCode', 'style' => 'float:right'));
	$divButtonQRCode  .= html_writer::link('#', $imgQrCodeS,  array('onclick' => 'M.mod_evoting.poll_init.showQrCode()', 'title' => get_string('msgShowQrCode', 'evoting')));
	$divButtonQRCode  .= html_writer::link(QRCODE_URL_LINK, $votelinkText, array('target'=>'_blank'));

	$divButtonQRCode .= html_writer::end_div();
	echo $divButtonQRCode;

	echo html_writer::start_tag('div', array('style' => 'clear: both;'));
	echo html_writer::end_tag('div');

        $qrcode = "https://chart.googleapis.com/chart?chs=400x400&cht=qr&chl=" . urlencode(QRCODE_URL) . "&choe=UTF-8&chld=L|1";
        $imgqrcode  = html_writer::empty_tag('img',
                array('src' => $qrcode, 'alt' => 'QrCode', 'style' => 'border: 5px solid #007cb7;'));
        echo html_writer::start_tag('div',
                array('id' => 'QrCodeBig', 'style' => 'width: 100%; display: none;text-align: center; padding:25px;'));
        $tagsevoting = html_writer::start_tag('h4', array('id' => 'inigma')) . get_string('scan', 'evoting');
        $tagsevoting .= html_writer::start_tag('span', array('style' => 'font-weight:bold;')) . ' i-nigma';
        $tagsevoting .= html_writer::end_tag('span');
        $tagsevoting .= html_writer::end_tag('h4');
        echo $tagsevoting;
        $inigmagoogleplay  = html_writer::empty_tag('img',
                array('src' => './pix/googleplay.jpg', 'alt' => 'Google Play', 'height' => '40', 'style' => 'margin:10px'));
        $urlgoogle = 'https://play.google.com/store/apps/details?id=com.threegvision.products.inigma.Android';
        echo html_writer::start_tag('a',
                array( 'href' => $urlgoogle, 'style' => 'cursor:pointer;')) . $inigmagoogleplay . html_writer::end_tag('a');

        $inigmaapplestore  = html_writer::empty_tag('img',
                array('src' => './pix/applestore.png', 'alt' => 'Apple Store', 'height' => '40', 'style' => 'margin:10px'));
        $urlapple = 'https://itunes.apple.com/fr/app/i-nigma-qr-code-data-matrix/id388923203?mt=8';
        echo html_writer::start_tag('a',
                array( 'href' => $urlapple, 'style' => 'cursor:pointer;')) . $inigmaapplestore . html_writer::end_tag('a');

        $inigmawindowstore  = html_writer::empty_tag('img',
                array('src' => './pix/windowstore.png', 'alt' => 'Windows Store', 'height' => '40', 'style' => 'margin:10px'));
        $urlwindows = 'http://www.windowsphone.com/fr-ch/store/app/i-nigma/828c4e78-1d2b-4fd2-ad22-fde9c553e263';
        $linkwindows = html_writer::start_tag('a', array( 'href' => $urlwindows, 'style' => 'cursor:pointer;'));
        $linkwindows .= $inigmawindowstore;
        $linkwindows .= html_writer::end_tag('a');
        echo $linkwindows;
        echo html_writer::link('#',
                $imgqrcode,
                array('onclick' => 'M.mod_evoting.poll_init.hideQrCode()',
                    'title' => get_string('msgHideQrCode', 'evoting'),
                    'target' => "_top",
                    'style' => 'display:block;text-align: center;margin-top:20px;'
                    ));
        echo html_writer::link(QRCODE_URL,
                '<h1 style="text-transform:none">'. QRCODE_URL .'</h1>',
                array('target' => '_blank', 'style' => 'margin:15px auto;'));
        echo html_writer::end_tag('div');

	echo $OUTPUT -> heading("<span style='display:none' id='numberQuestion'>" . $evoting -> questions -> number. "</span>", 5);

	echo html_writer::start_tag('div', array('id' => 'questionName'));
	echo file_rewrite_pluginfile_urls($evoting->questions->name, 'pluginfile.php', $context->id, 'mod_evoting', "questioneditor", $evoting->questions->id);
	echo html_writer::end_tag('div');

	// Count the options of the current question
	$countOptions = count($evoting -> questions-> options);
	$sumOptions = 0;
	
	for ($i = 0; $i < $countOptions; $i++) {
		$idOption = $evoting -> questions -> options[$i] -> id;
		$obj = evoting_get_count_answer($idOption);
		$sumOptions += intval($obj->{'count'}) ;
	}
	 
	// Set to null the html futures options
	$options = '';
	$divOptionListContext = '';
	 // Array color
	$arrayColor = array("#007cb7");

	// Array from intitiate graphic
	$arrayDataGraphic = array();
	
	// Loop to create dynamic options
	for ($i = 0; $i < $countOptions; $i++) {
		
		$idOption = $evoting -> questions -> options[$i] -> id;
		$nameOption = $evoting -> questions -> options[$i] -> text;
		
		$obj = evoting_get_count_answer($idOption);
		$currentOption = $obj->{'count'} ;

		$options .= html_writer::empty_tag('p', array('class' => 'answerName', 'style' => 'display:inline-block'));
		$options .= ($i+1) . ") " . $nameOption . ' ';
		$options .= html_writer::start_span('answerCount', array('id' => $idOption, 'style' => 'display:none')) . $currentOption .  html_writer::end_span();
		$options .='</p>';
		
		// Create html option list
		$divOptionListContext .= html_writer::start_tag('div', array('id' => 'evotingOption', 'class'=>'evotingOption'));
		$divOptionListContext .= html_writer::start_tag('div', array('id' => 'optionNo', 'class'=>'optionNo'));
		$divOptionListContext .= $i+1;
		$divOptionListContext .= html_writer::end_tag('div');
		
		$divOptionListContext .= html_writer::start_tag('div', array('id' => 'optionText', 'class'=>'optionText'));
		$divOptionListContext .= html_writer::start_tag('span');
		$divOptionListContext .= $nameOption;
		$divOptionListContext .= html_writer::end_tag('span');
		$divOptionListContext .= html_writer::end_tag('div');
		$divOptionListContext .= html_writer::end_tag('div');
		
		// Create array for Graphic chart
		$currentOption = intval($currentOption);

		if($sumOptions > 0){
			 $percent = round($currentOption/$sumOptions*100);
		} else {
			$percent = 0;
		}

		// Set the true answer
		$correctAnswer = $evoting -> questions -> options[$i] -> correct;

			if($correctAnswer == 1 ){
				$trueAnswer = true;
			} else {
				$trueAnswer = false;
			}

		if($sumOptions > 29){
			$nameOption = $nameOption . " - " . $percent . " %" ;
		} else {
			if($currentOption == 0 && $sumOptions == 0){
				$nameOption = $nameOption;
			} else {
				$nameOption = $nameOption . " - " . $currentOption . "/" . $sumOptions;
			}
		}
		$arrayOptions =  array(" " . ($i+1) . "  ", $currentOption, $arrayColor[0], $nameOption  , $idOption, '&nbsp;<h5>&nbsp;'.get_string('countvote','evoting').' <b>&nbsp; '.$currentOption.'</b>&nbsp;&nbsp;&nbsp;</h5>', $trueAnswer);
		array_push($arrayDataGraphic,$arrayOptions);

	}

	if($countOptions% 2 != 0)
	{
		$divOptionListContext .= html_writer::start_tag('div', array('id' => 'evotingOptionEmpty', 'class'=>'evotingOption'));
		$divOptionListContext .= html_writer::start_tag('div', array('id' => 'optionNo', 'class'=>'optionNo')).' ';
		$divOptionListContext .= html_writer::end_tag('div');
		
		$divOptionListContext .= html_writer::start_tag('div', array('id' => 'optionText', 'class'=>'optionText'));
			$divOptionListContext .= html_writer::start_tag('span').' ';
				
			$divOptionListContext .= html_writer::end_tag('span');
		$divOptionListContext .= html_writer::end_tag('div');
		$divOptionListContext .= html_writer::end_tag('div');
	}

	// Div Chart
	$divChart  = html_writer::start_div('div', array('id' => 'chartContainer', 'style' => 'text-align:center')) . html_writer::end_div();
	
	// Countdown Div
	$divCountDown = html_writer::start_div('div', array('id' => 'divCountDown',  'class' => 'pie degree'));
	$divCountDown .= html_writer::empty_tag('span', array('class'=>'block_time'));
	$divCountDown .= html_writer::empty_tag('span', array('id'=>'time'));
	$divCountDown .= html_writer::start_div('div', array('id' => 'divCountText'));
	$divCountDown .= html_writer::end_div();
	$divCountDown .= html_writer::end_div();

	// Init option list
	$divOptionList = html_writer::start_tag('div', array('id' => 'divOptions'));
	$divOptionList .= $divOptionListContext;
	$divOptionList .= html_writer::start_tag('div', array('style' => 'clear: both;'));
	$divOptionList .= html_writer::end_tag('div');
	$divOptionList .= html_writer::end_tag('div');
	
	// Container Chart and countdown div
	$divContainerChartCountDown = html_writer::start_div('div', array('id' => 'divEndText'));
	$divContainerChartCountDown .= html_writer::end_div();
	$divContainerChartCountDown .= html_writer::start_div('div', array('id' => 'divContainerChartCountDown', "style" => "height:450px ; position:relative")); 
	$divContainerChartCountDown .= $divCountDown;
	$divContainerChartCountDown .= $divChart;
	$divContainerChartCountDown .= $divOptionList;
	
	$divContainerChartCountDown .= html_writer::end_div();
	echo $divContainerChartCountDown;

	// Historique
	$historyList = evoting_get_history_list($evoting -> questions -> id);
	$countHistory = count($historyList);
	
	if($countHistory > 0) {
		$refHistorique = html_writer::start_tag('div', array('id' => 'divHistorique', 'style' => 'width: 100%; text-align: right;'));
	} else {
		$refHistorique = html_writer::start_tag('div', array('id' => 'divHistorique', 'style' => 'width: 100%; text-align: right; visibility:hidden'));
	}
	 
	 $refHistorique .= html_writer::start_tag('a', array( 'href' => 'history.php?id='.$id. '&idQ='.$evoting -> questions -> id.'&ts=0','class' => 'refHistoric', 'style'=> 'cursor:pointer;')) . get_string('history', 'evoting');
	 $refHistorique .= html_writer::end_tag('a');
	 $refHistorique .= html_writer::end_tag('div');
	 echo $refHistorique;

	// Number Question
	$questionslist = evoting_get_questions($evoting -> idpoll);
	$questionslistlength = count($questionslist);

	echo html_writer::start_tag('ul', array('class' => 'pagination'));
        $disabled = 'disabled';
        $html = html_writer::span('');
	if($evoting -> questions -> number > 1 )
	{
            $disabled = '';
            $html = html_writer::start_tag('a', array('href' => '#',
                'title' => get_string('previous', 'evoting'), 'data-value' => -2, 'class' => 'pagenum'));
            $html .= html_writer::end_tag('a');
	}
        echo html_writer::start_tag('li', array('class' => $disabled));
        echo $html;
        echo html_writer::end_tag('li');

	foreach ($questionslist as $question) {
                $current = '';
                $htmlpage = html_writer::start_tag('a', array('href' => '#',
                    'class' => 'pagenum', 'data-value' => $question->number));
                $htmlpage .= $question->number;
                $htmlpage .= html_writer::end_tag('a');
		if($question->number == $evoting -> questions -> number)
		{
                    $current = "active";
                    $htmlpage = html_writer::span($question->number);
		}
                echo html_writer::start_tag('li', array('class' => $current));
                echo $htmlpage;
                echo html_writer::end_tag('li');
	}
        $disabled = 'disabled';
        $html = html_writer::span('');
	if($evoting -> questions -> number < $questionslistlength )
	{
            $disabled = '';
            $html = html_writer::start_tag('a', array('href' => '#',
                'title' => get_string('next', 'evoting'), 'data-value' => -1, 'class' => 'pagenum'));
            $html .= html_writer::end_tag('a');
	}
        echo html_writer::start_tag('li', array('class' => $disabled));
        echo $html;
        echo html_writer::end_tag('li');

	echo html_writer::end_tag('ul');

	// Button and statut Start / Stop Poll
	$divButtonActivPoll  = html_writer::start_div('div', array('id' => 'divButtonActivPoll'));
	$divButtonActivPoll  .= html_writer::empty_tag('input', array('type'=>'button', 'class' => 'btn btn-lg btn-success', 'id'=>'buttonStatutPoll', 'value'=>get_string('start', 'evoting')));
	$divButtonActivPoll .= html_writer::end_div();

	// Button Reset
	$divButtonResetQuestion  = html_writer::start_div('', array('id' => 'divButtonNavigate'));
	$divButtonResetQuestion .= html_writer::empty_tag('input', array('type'=>'button', 'id'=>'buttonResetQuestion', 'value'=>get_string('reset')));
	$divButtonResetQuestion .= html_writer::end_div();

	// Options
	$divOptions  = html_writer::start_div('', array('id' => 'divOptions', 'style' => 'display: none;'));
	$divOptions .= $options;;
	$divOptions .= html_writer::end_div();
	echo $divOptions;

	// Dropdown list Time voting
	$divDropDownListQuestion  = html_writer::start_div('', array('id' => 'divDropDownList'));
	
	$options = array(
		'1' => get_string('disable'),
		'10' => ' 10 sec',
		'20' => ' 20 sec',
		'30' => ' 30 sec',
		'60' => ' 1 min'
	);
	
	$selectTime = html_writer::start_div('', array('id' => 'selectZone'));
	$selectTime .= html_writer::select($options, 'selectTime', 1);
	$selectTime .= html_writer::end_div();

	$divDropDownListQuestion .= html_writer::start_div('', array('id' => 'textSelect')).get_string('timevote', 'evoting'); 
	$divDropDownListQuestion .= html_writer::end_div('');
	$divDropDownListQuestion .= $selectTime;
	$divDropDownListQuestion .= html_writer::end_div();

	// Command panel
	$divRowControl  = html_writer::start_div('div', array('id' => 'divTableControl'));
	$divRowControl .= $divButtonResetQuestion;
	$divRowControl .= $divDropDownListQuestion;
	$divRowControl .= $divButtonActivPoll;
	$divRowControl .= html_writer::end_div();

	$divTableControl  = html_writer::start_div('div', array('id' => 'divTableControl', 'style' => 'display: table; width: 100%;text-align:center;'));
	$divTableControl .= $divRowControl;
	$divTableControl .= html_writer::end_div();

	// Display Button
	echo $divRowControl;

	// Field ID Poll and ID Question next
	echo $inputIdPoll = html_writer::empty_tag('input', array('type'=>'hidden', 'id'=>'inputIdPoll', 'value'=> $evoting->idpoll));
	echo $inputIdCourse = html_writer::empty_tag('input', array('type'=>'hidden', 'id'=>'inputIdCourse', 'value'=> $course->id));
    echo $inputIdQuestion = html_writer::empty_tag('input', array('type'=>'hidden', 'id'=>'inputIdQuestion', 'value'=> $evoting -> questions -> id));
	echo $inputIdQuestionNext = html_writer::empty_tag('input', array('type'=>'hidden', 'id'=>'inputIdQuestionNext', 'value'=> $evoting->idquestionnext));

	echo $div  = html_writer::start_div('div', array('id' => 'affichage', 'style' => 'text-align:center')) . html_writer::end_div();

	// Finish the page.
	$PAGE->requires->js_init_call('M.mod_evoting.poll_init', array(json_encode($arrayDataGraphic)));
	echo $OUTPUT -> footer();

}

