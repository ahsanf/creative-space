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

require_once("../../../config.php");
require_once("../lib.php");
require_once($CFG->libdir . '/completionlib.php');

require_login();
require_sesskey();

/*
 *  Variables
 */
$idcourse = optional_param('idCourse', 0, PARAM_INT);
$idpoll = optional_param('idPoll', 0, PARAM_INT);
$action = optional_param('action', '', PARAM_TEXT);
$idclient = optional_param('idClient', 0, PARAM_INT);
$choice = optional_param('choice', '', PARAM_TEXT);
$lang = optional_param('lang', '', PARAM_TEXT);
$number = optional_param('number', 0, PARAM_INT);
$idquestion = optional_param('idQuestion', 0, PARAM_INT);
$idoption = optional_param('idOption', 0, PARAM_INT);
$idoptioncurrent = optional_param('idOptionCurrent', 0, PARAM_INT);
$nbrvoteoptioncurrent = optional_param('nbrVoteOptionCurrent', 0, PARAM_INT);
$time = optional_param('time', 0, PARAM_INT);
$statut = optional_param('statut', '', PARAM_TEXT);


// Validate params request.

if (!$idpoll) {
    if ($idoption) {
        $option = $DB->get_record('evoting_options', array('id' => $idoption), 'evotingquestionid', MUST_EXIST);
        $idquestion = $option->evotingquestionid;
    }
    if ($idoptioncurrent) {
        $option = $DB->get_record('evoting_options', array('id' => $idoptioncurrent), 'evotingquestionid', MUST_EXIST);
        $idquestion = $option->evotingquestionid;
    }
    if ($idquestion) {
        $question = $DB->get_record('evoting_questions', array('id' => $idquestion), 'evotingid', MUST_EXIST);
        $idpoll = $question->evotingid;
    }
}
if ($idpoll) {
    $cm = get_coursemodule_from_instance('evoting', $idpoll, $idcourse, false);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    require_login($course, true, $cm);
}
/*
 * Get Context
 */
$contextcourse = context_course::instance($idcourse);

/*
 * Check capability (Only teacher, Manager, admin can use these services)
 */
if (has_capability('mod/evoting:openevoting', $contextcourse)) {
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        if (isloggedin() && confirm_sesskey()) {
            switch ($action) {
                case 'mdl_delete_history':
                    //Delete history
                    echo evoting_delete_history($time, $idquestion);
                    break;
                case 'mdl_get_history':
                    //Get history
                    $result = evoting_get_history_list($idquestion);
                    echo json_encode($result);
                    break;
                case 'mdl_save_history':
                    //Add history
                    echo evoting_save_history($idoptioncurrent, $nbrvoteoptioncurrent, $time);
                    break;
                case 'mdl_changeQuestion':
                    // Go to the next / previous activ question of the current poll in Moodle
                    echo evoting_change_question($idpoll, $number, $contextcourse);
                    break;
                case 'mdl_deleteQuestion':
                    // Delete a question
                    $result = evoting_delete_question($idquestion);
                    echo json_encode($result);
                    break;
                case 'mdl_refreshOption':
                    // Get the count of answer of an option to display in Moodle
                    $result = evoting_get_count_answer($idoption);
                    echo json_encode($result);
                    break;
                case 'mdl_resetQuestion':
                    // Reset the answer of the current question
                    $result = evoting_reset_poll($idpoll, $contextcourse);
                    echo json_encode($result);
                    break;
                case 'mdl_setStatutPoll':
                    // Set the statut of the current poll (Activ / Inactive)
                    $result = evoting_set_statut_poll($idpoll, $statut, $contextcourse);
                    echo json_encode($result);
                    break;
            }
        }
    }
}
?>