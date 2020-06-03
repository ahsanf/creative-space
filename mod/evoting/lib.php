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
define('MAXOPTIONS', 16);
require_once($CFG->dirroot . '/mod/evoting/locallib.php');

/**
 * Return the tiny url
 * @param $url
 * @return mixed
 */
function evoting_get_tiny_url($url)
{
    $ch = curl_init();
    $timeout = 5;
    curl_setopt($ch, CURLOPT_URL, 'https://tinyurl.com/api-create.php?url=' . $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

/**
 *  Get curl result
 * @param $url
 * @return mixed
 */
function evoting_curl_get_result($url)
{
    $ch = curl_init();
    $timeout = 5;
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

/**
 * Get the history list
 * @param $idQuestion
 * @return array
 */
function evoting_get_history_list($idQuestion)
{
    global $DB;

    $history = $DB->get_records_sql("
	SELECT h.timestamp FROM {evoting_options} o, {evoting_questions} q , {evoting_history} h
	WHERE q.id = o.evotingquestionid
	AND o.id = h.optionid
	AND q.id = ?
	GROUP BY h.timestamp", array($idQuestion));
    $history = array_values($history);

    return $history;
}

/**
 * Get history
 * @param $idQuestion
 * @param $timestamp
 * @return array
 */
function evoting_get_history($idQuestion, $timestamp)
{
    global $DB;

    $history = $DB->get_records_sql("
	SELECT h.*, o.id, o.text as optionname, correct FROM {evoting_options} o, {evoting_questions} q , {evoting_history} h
	WHERE q.id = o.evotingquestionid
	AND q.id = ?
	AND o.id = h.optionid
	AND h.timestamp = ?
	ORDER BY o.id", array($idQuestion, $timestamp));
    $history = array_values($history);

    return $history;
}

/**
 * Save history
 * @param $idOptionCurrent
 * @param $nbrVoteOptionCurrent
 * @param $time
 * @return mixed
 */
function evoting_save_history($idOptionCurrent, $nbrVoteOptionCurrent, $time)
{
    global $DB;

    $history = new stdClass();
    $history->optionid = $idOptionCurrent;
    $history->countvote = $nbrVoteOptionCurrent;
    $history->timestamp = $time;

    return $DB->insert_record("evoting_history", $history);
}

/**
 * Delete history.
 *
 * @param $time
 * @param $questionid The question ID
 * @return mixed
 */
function evoting_delete_history($time, $questionid)
{
    global $DB;
    
    // Select options.
    $sql = "SELECT o.id
              FROM {evoting_options} o
             WHERE o.evotingquestionid = ? ORDER BY o.id ASC";
    $options = $DB->get_records_sql($sql, array('id' => $questionid));
    if (count(array_keys($options))) {
        list($insql, $params) = $DB->get_in_or_equal(array_keys($options));
        $params[] = $time;
        // Delete history.
        return $DB->delete_records_select('evoting_history', "optionid $insql AND timestamp = ?", $params);
    }
    return false;
}

/**
 * Gets options for the poll
 * @param $idpoll
 * @return count
 */
function evoting_get_options_poll($idPoll, $idUser) {
    global $DB;
    $sql1 = "SELECT op.id
              FROM {evoting} e, {evoting_questions} q, {evoting_options} op
         WHERE  e.id = q.evotingid
         AND op.evotingquestionid = q.id
 	     AND q.activ = 1 AND e.id = ? ORDER BY op.id ASC";

    $sql2 = "SELECT op.id
              FROM {evoting} e, {evoting_questions} q, {evoting_options} op, {evoting_answers} a
         WHERE  e.id = q.evotingid
         AND op.evotingquestionid = q.id
         AND op.id = a.optionid
 	     AND q.activ = 1 AND e.id = ? AND a.uservoteid = ? ORDER BY op.id ASC";

    $result = $DB->get_records_sql($sql1, array($idPoll));
    $result2 = $DB->get_records_sql($sql2, array( $idPoll, $idUser));

    $result = array_values($result);
    $result2 = array_values($result2);

    $return_array = array();
    array_push($return_array, $result);
    array_push($return_array, $result2);

    return $return_array;
}


/**
 * Function that delete the question in moodle form admin
 * @param $idquestion
 * @return mixed
 */
function evoting_delete_question($idquestion) {
    global $DB;

    // Select and Delete answer.
    $sql = "SELECT a.id
              FROM {evoting_answers} a, {evoting_options} o
             WHERE a.optionid = o.id AND o.evotingquestionid = ?";
    $answers = $DB->get_records_sql($sql, array('id' => $idquestion));
    if (count(array_keys($answers))) {
        list($insql, $params) = $DB->get_in_or_equal(array_keys($answers));
        $DB->delete_records_select('evoting_answers', "id $insql", $params);
    }

    // Select and Delete options.
    $sql = "SELECT o.id
              FROM {evoting_options} o
             WHERE o.evotingquestionid = ? ORDER BY o.id ASC";
    $options = $DB->get_records_sql($sql, array('id' => $idquestion));
    if (count(array_keys($options))) {
        list($insql, $params) = $DB->get_in_or_equal(array_keys($options));
        // Delete history.
        $DB->delete_records_select('evoting_history', "optionid $insql", $params);
        // Delete options.
        $DB->delete_records_select('evoting_options', "id $insql", $params);
    }

    return $DB->delete_records('evoting_questions', array('id' => $idquestion));
}

/**
 * Reset the user data
 * @param $course
 * @return array status
 */
function evoting_reset_userdata($data)
{
    global $DB;

    $componentstr = get_string('modulenameplural', 'evoting');
    $status = array();
    $course = $data->courseid;

    if (isset($data->reset_evoting_all)) {
        $current = $DB->get_records_sql("SELECT a.id
	FROM {evoting_answers} a, {evoting_options} o, {evoting_questions} q, {evoting} p
	WHERE p.id = q.evotingid
	AND o.evotingquestionid = q.id
	AND a.optionid = o.id
	AND q.evotingid = p.id
	AND p.course = ?", array($course));

        // Get array values form object [0,1,2,...]
        $current = array_keys($current);

        // Delete in DB Moodle
        if (count($current) > 0) {
            list($insql, $params) = $DB->get_in_or_equal($current);
            $DB->delete_records_select('evoting_answers', "id $insql", $params);
        }
    }

    $status[] = array('component' => $componentstr, 'item' => get_string('resetpoll', 'evoting'), 'error' => false);

    return $status;
}

/**
 * Used for set default values to form's elements displayed by mymodule_reset_course_form_definition
 * @param $course
 * @return array
 */
function evoting_reset_course_form_defaults($course)
{
    return array('reset_evoting_all' => 'checked');
}

/**
 * Called by course/reset.php
 */
function evoting_reset_course_form_definition(&$mform)
{
    $mform->addElement('header', 'evotingheader', get_string('modulenameplural', 'evoting'));
    $mform->addElement('checkbox', 'reset_evoting_all', get_string('resetpoll', 'evoting'));
}

/**
 * Function to reset answers of the current question
 *
 * @param int $idQuestion
 * @param context_course $context_course
 * @return true
 */
function evoting_reset_poll($idPoll, $context_course)
{
    global $DB;
    // Select the Poll with id
    $current = $DB->get_records_sql("
SELECT a.id
FROM {evoting_answers} a, {evoting_options} o, {evoting_questions} q, {evoting} p
WHERE p.id = q.evotingid
AND o.evotingquestionid = q.id
AND a.optionid = o.id
AND q.evotingid = ?", array($idPoll));

    // Get array values form object [0,1,2,...]
    $current = array_values($current);

    // Create a copy object of the poll
    $answer = $current;

    // Count answers to delete
    $countAnswer = count($answer);

    // Delete in DB Moodle
    for ($i = 0; $i < $countAnswer; $i++) {
        $DB->delete_records('evoting_answers', array('id' => $answer[$i]->id));
    }

    // Log.
    $params = array(
        'context' => $context_course,
        'objectid' => $idPoll
    );

    // Log reset poll.
    $event = \mod_evoting\event\evoting_reset::create($params);
    $event->trigger();

    return true;
}

/**
 * Function that change the statut of the current poll (Start / Stop)
 * @param int $idPoll
 * @return the evoting poll
 */
function evoting_set_statut_poll($idPoll, $statut, $context_course)
{
    global $DB;

    // Select the Poll with id
    $current = $DB->get_records_sql("
SELECT
*
FROM
{evoting}
WHERE
id=?", array($idPoll));

    // Get array values form object [0,1,2,...]
    $current = array_values($current);

    // Create a copy object of the poll
    $poll = new stdClass();
    $poll = $current[0];

    // Log
    $params = array(
        'context' => $context_course,
        'objectid' => $idPoll,
    );

    // Change the statut "publish" of the poll (Start / Stop)
    if ($statut == 'true') {
        $statut = 1;
    } else {
        $statut = 0;
    }

    if ($poll->publish == 0 && $statut == 1){
        // Log start poll
        $event = \mod_evoting\event\evoting_started::create($params);
        $event->trigger();
    } else if ($poll->publish == 1 && $statut == 0){
        // Log stop poll
        $event = \mod_evoting\event\evoting_stopped::create($params);
        $event->trigger();
    }

    $poll->publish = $statut;


    // Update in DB Moodle
    $DB->update_record("evoting", $poll);

    // Return the poll
    return $poll;
}

/**
 * Function that change tu current question (Set to inactive and activ the next) / (Set to inactive and activ the previous)
 * @param int $idPoll
 * @param int $number (+1 => next question) / (-1 => previous question)
 * @return true
 */
function evoting_change_question($idPoll, $numb, $context_course)
{
    global $DB;

    // set variable to activ
    $activ = 1;

    // Select the activ question from poll
    $current = $DB->get_records_sql("
SELECT
*
FROM
{evoting_questions}
WHERE
evotingid=?
AND
activ=?", array($idPoll, $activ));

    // Get array values form object [0,1,2,...]
    $current = array_values($current);

    // Create a copy object of the question
    $ques = new stdClass();
    $ques = $current[0];

    // Inactive the current question
    $ques->activ = 0;

    // Update in DB Moodle
    $DB->update_record("evoting_questions", $ques);

    // Get the number of the current question
    $number = $ques->number;

    // Next question or Back question (Check $numb in @param)
    if ($numb > 0) {
        $number = $numb;
    } else if ($numb == -1) {
        $number++;
    } else if ($numb == -2) {
        $number--;
    }

    // Select the next / previous question
    $next = $DB->get_records_sql("
SELECT
*
FROM
{evoting_questions}
WHERE
evotingid=?
AND
number=?", array($idPoll, $number));

    // Get array values form object [0,1,2,...]
    $next = array_values($next);

    // Create a copy object of the question
    $quesNext = new stdClass();
    $quesNext = $next[0];

    // Activ the next / previous question
    $quesNext->activ = 1;

    // Update in DB Moodle
    $DB->update_record("evoting_questions", $quesNext);

    // Log
    $params = array(
        'context' => $context_course,
        'objectid' => $idPoll
    );

    // Log reset poll
    $event = \mod_evoting\event\evoting_change_question::create($params);
    $event->trigger();

    return true;
}

/**
 * Gets a full evoting record in Moodle
 * @param int $idPoll
 * @return object The evoting
 */
function evoting_get_evoting($idPoll)
{
    global $DB;

// set variable to activ
    $activ = 1;

// Select the poll by id
    $poll = $DB->get_records_sql("
SELECT *
FROM
{evoting}
WHERE
id=?", array($idPoll));

// Create a result class with field we want to set to the object
    $result = new stdClass();
    $result->idpoll = $poll[$idPoll]->{'id'};
    $result->idcreator = $poll[$idPoll]->{'idcreator'};
    $result->course = $poll[$idPoll]->{'course'};
    $result->name = $poll[$idPoll]->{'name'};
    $result->intro = $poll[$idPoll]->{'intro'};
    $result->publish = $poll[$idPoll]->{'publish'};

// Select the activ question from poll
    $questions = $DB->get_records_sql("
SELECT
id as idQuestion, number, name, evotinggraphicid
FROM
{evoting_questions}
WHERE
evotingid=?
AND
activ=? ORDER BY number ASC", array($idPoll, $activ));

// Get array values form object [0,1,2,...]
    $questions = array_values($questions);

// Create a  class question with field we want to set to the object
    $ques = new stdClass();
    $ques->id = $questions[0]->{'idquestion'};
    $ques->number = $questions[0]->{'number'};
    $ques->name = $questions[0]->{'name'};
    $ques->evotinggraphicid = $questions[0]->{'evotinggraphicid'};

// Select options form question
    $options = $DB->get_records_sql("
SELECT
id, text, correct
FROM
{evoting_options}
WHERE
evotingquestionid=?", array($ques->id));

// Get array values form object [0,1,2,...]
    $options = array_values($options);

// Length of the object
    $cptOptions = count($options);

// Loop foreach options
    for ($j = 0; $j < $cptOptions; $j++) {
        $opt[$j] = new stdClass();
        $opt[$j]->id = $options[$j]->{'id'};
        $opt[$j]->text = $options[$j]->{'text'};
        $opt[$j]->correct = $options[$j]->{'correct'};

    }

// Add object option to the object question
    $ques->options = $opt;

// Set the number of the next question
    $numberCurrentQuestion = $ques->number;
    $numberNextQuestion = $numberCurrentQuestion + 1;

// Select the next question
    $questionNext = $DB->get_records_sql("
SELECT
id as idQuestion
FROM
{evoting_questions}
WHERE
number=?
AND evotingid=?", array($numberNextQuestion, $idPoll));

// Is there a next question, set it
    if (!empty($questionNext)) {
        $questionNext = array_values($questionNext);
        $idQuestionNext = $questionNext[0]->{'idquestion'};
// Otherweise next question is 0
    } else {
        $idQuestionNext = 0;
    }

// Add the id question next to the result
    $result->idquestionnext = $idQuestionNext;

// Add the object question to the result
    $result->questions = $ques;

// Return the result
    return $result;

}

/**
 * Gets a full evoting graphic record in Moodle
 * @param $idPoll
 * @return llist question
 */
function evoting_get_questions($idPoll)
{
    global $DB;
    $questionList = $DB->get_records_sql("
	SELECT
	id , number, name 
	FROM
	{evoting_questions}
	WHERE
	evotingid=? ORDER BY number ASC", array($idPoll));

    return $questionList;
}


/**
 * Gets a full evoting graphic record in Moodle
 * @return object The graphic
 */
function evoting_get_graphic()
{
    global $DB;
    $graphicList = $DB->get_records_sql("
	SELECT
	id , name, picture 
	FROM
	{evoting_graphic}");

    return $graphicList;
}


/**
 * Function that count the sum of answer for an option
 * @param  $idOption
 * @return object count
 */
function evoting_get_count_answer($idOption)
{
    global $DB;

    $text = $DB->get_records_sql("SELECT  o.text AS text
FROM {evoting_options} o
WHERE o.id =?", array($idOption));

    // Count the sum of answer for an option
    $count = $DB->get_records_sql("
SELECT count(*) as count
FROM {evoting_options} o, {evoting_answers} a, {evoting_questions} q, {evoting} p
WHERE o.id = a.optionid
AND o.evotingquestionid = q.id
AND q.evotingid = p.id
AND a.optionid=?", array('optionid' => $idOption));

    // Get array values form object [0,1,2,...]
    $text = array_values($text);
    $count = array_values($count);

    // Construct a new array
    $obj = new stdClass();
    $obj->text = $text[0]->text;
    $obj->count = $count[0]->count;

    // Return the count
    return $obj;
}

/**
 * Function to cancel vote of user
 */
function evoting_cancel_vote($idPoll, $idUser, $lang){
    global $DB;

    $pollNotStart = get_string_manager()->get_string('pollnotstart', 'evoting', null, $lang);
    $delete_vote = get_string_manager()->get_string('votedeleted', 'evoting', null, $lang);

    // Question activ
    $activ = 1;

    // Check if the poll is started and get the ID of the current question
    $poll = $DB->get_records_sql("
	SELECT
	p.publish, q.id, q.multipleanswers
	FROM {evoting_questions} q, {evoting} p
	WHERE q.evotingid = p.id
	AND activ = ?
	AND p.id =?", array($activ, $idPoll));

    // Get array values form object [0,1,2,...]
    $poll = array_values($poll);

    // If the poll is started
    if ($poll[0]->publish == true) {

        // ID of the activ question
        $idQuestion = $poll[0]->id;

       //$result = $DB->delete_records_select("DELETE FROM {evoting_answers} a, {evoting_options} o WHERE o.id = a.optionid AND uservoteid = ? AND o.evotingquestionid = ?", array($idUser,$idQuestion));

        $sql_delete = $DB->get_records_sql("
	SELECT a.id
	FROM {evoting_answers} a, {evoting_options} o
	WHERE o.id = a.optionid 
	AND uservoteid = ? 
	AND o.evotingquestionid = ?", array($idUser,$idQuestion));

        $result = array_values($sql_delete);
        $count_result = count($result);

        for($i = 0 ; $i < $count_result ; $i++){
            $DB->delete_records("evoting_answers", array('id'=>$result[$i]->id));
        }

        return $delete_vote;

    } else {
        return $pollNotStart;
    }
}
/**
 * Function that insert into DB Moodle the vote of the end user
 * @param $userVoteId
 * @param $optionId
 * @param $idPoll
 * @param $lang
 * @return mixed
 */
function evoting_vote($userVoteId, $optionId, $idPoll, $lang)
{
    global $DB;

    // Question activ
    $activ = 1;

    $sameVote = get_string_manager()->get_string('samevote', 'evoting', null, $lang);
    $choiceNotValid = get_string_manager()->get_string('choicenotvalid', 'evoting', null, $lang);
    $voteOk = get_string_manager()->get_string('voteok', 'evoting', null, $lang);
    $pollNotStart = get_string_manager()->get_string('pollnotstart', 'evoting', null, $lang);
    $delete_vote = get_string_manager()->get_string('votesingledeleted', 'evoting', null, $lang);
    $return_array = array();

    // Check if the poll is started and get the ID of the current question
    $poll = $DB->get_records_sql("
	SELECT
	p.publish, q.id, q.multipleanswers
	FROM {evoting_questions} q, {evoting} p
	WHERE q.evotingid = p.id
	AND activ = ?
	AND p.id =?", array($activ, $idPoll));

    // Get array values form object [0,1,2,...]
    $poll = array_values($poll);

    // If the poll is started
    if ($poll[0]->publish == true) {

        // ID of the activ question
        $idQuestion = $poll[0]->id;

        // Get ID Options from the activ Question
        $optionsId = $DB->get_records_sql("
		SELECT o.id FROM {evoting_options} o, {evoting_questions} q
		WHERE o.evotingquestionid = q.id
		AND q.id = ? ORDER BY o.id ASC", array($idQuestion));

        // Get array values form object [0,1,2,...]
        $optionsId = array_values($optionsId);

        // Get the ID Option of the choice of the current user
        if (isset($optionsId[$optionId - 1]->id)) {
            $optionId = $optionsId[$optionId - 1]->id;
        } else {
            $optionId = NULL;
        }

        // Multiple answers
        $multiple_answers = $poll[0]->multipleanswers;

        array_push($return_array, $multiple_answers);

        // If choice not valid
        if ($optionId == NULL) {
            array_push($return_array, $choiceNotValid);
            return $return_array;
        }

        // Check if the user has already voted the same option
        $option = $DB->get_record_sql("
		SELECT o.id FROM {evoting_options} o, {evoting_answers} a, {evoting_questions} q
		WHERE o.id = a.optionid
		AND o.evotingquestionid = q.id
		AND a.optionid = ?
		AND a.uservoteid = ? ORDER BY o.id ASC", array('optionid' => $optionId, 'uservoteid' => $userVoteId));

        if(!empty($option->id)){
            $DB->delete_records("evoting_answers", array('optionid'=>$option->id, 'uservoteid' =>$userVoteId));
            array_push($return_array, $delete_vote);
            return $return_array;
        }

        if(!$multiple_answers){

                // Count if the user has already voted
                $count = $DB->count_records_sql("
		SELECT count(*) as count FROM {evoting_options} o, {evoting_answers} a, {evoting_questions} q
		WHERE o.id = a.optionid
		AND o.evotingquestionid = q.id
		AND o.evotingquestionid = ?
		AND a.uservoteid = ?", array('evotingquestionid' => $idQuestion, 'uservoteid' => $userVoteId));

                // If Yes -> Update the answers
                if ($count !== 0) {

                    $current = $DB->get_records_sql("
		SELECT a.uservoteid, a.timemodified, a.id, a.optionid FROM {evoting_options} o, {evoting_answers} a, {evoting_questions} q
		WHERE o.id = a.optionid
		AND o.evotingquestionid = q.id
		AND o.evotingquestionid = ?
		AND a.uservoteid = ?", array('evotingquestionid' => $idQuestion, 'uservoteid' => $userVoteId));

                    $newanswer = new stdClass();
                    $newanswer->id = $current[$userVoteId]->{'id'};
                    $newanswer->uservoteid = $userVoteId;
                    $newanswer->optionid = $optionId;
                    $newanswer->timemodified = time();
                    $DB->update_record("evoting_answers", $newanswer);

                    // if No -> Insert the answers
                } else {
                    $newanswer = new stdClass();
                    $newanswer->uservoteid = $userVoteId;
                    $newanswer->optionid = $optionId;
                    $newanswer->timemodified = time();
                    $DB->insert_record("evoting_answers", $newanswer);
                }
                // All is ok return ok
            array_push($return_array, $voteOk);
            return $return_array;

        } else {
            // Count if the user has already voted the same option
            $count = $DB->count_records_sql("
		SELECT count(*) as count FROM {evoting_options} o, {evoting_answers} a, {evoting_questions} q
		WHERE o.id = a.optionid
		AND o.evotingquestionid = q.id
		AND o.id = ?
		AND a.uservoteid = ?", array('id' => $optionId, 'uservoteid' => $userVoteId));

            // If Yes -> Update the answers
            if ($count !== 0) {
                array_push($return_array, $sameVote);
                return $return_array;
                // if No -> Insert the answers
            } else {
                $newanswer = new stdClass();
                $newanswer->uservoteid = $userVoteId;
                $newanswer->optionid = $optionId;
                $newanswer->timemodified = time();
                $DB->insert_record("evoting_answers", $newanswer);
                // All is ok return ok
                array_push($return_array, $voteOk);
                return $return_array;
            }
        }
    } // If the poll is not started return not activ
    else {
        array_push($return_array, 0);
        array_push($return_array, $pollNotStart);
        return $return_array;
    }
}

/* Moodle core API */

/**
 * Returns the information on whether the module supports a feature
 *
 * See {@link plugin_supports()} for more info.
 *
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed true if the feature is supported, null if unknown
 */
function evoting_supports($feature)
{

    switch ($feature) {
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_SHOW_DESCRIPTION:
            return true;
        case FEATURE_GRADE_HAS_GRADE:
            return false;
        case FEATURE_BACKUP_MOODLE2:
            return true;
        case FEATURE_GROUPS:
            return false;

        default:
            return null;
    }
}

/**
 * Saves a new instance of the evoting into the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param stdClass $evoting Submitted data from the form in mod_form.php
 * @param mod_evoting_mod_form $mform The form instance itself (if needed)
 * @return int The id of the newly inserted evoting record
 */
function evoting_add_instance($evoting)
{
    global $DB, $CFG;
    $cmid = $evoting->coursemodule;
    $context = context_module::instance($cmid);

//!!!!!!!To see Get the ID CREATOR!!!!!!!!!

// Insert Poll
    $evoting->timemodified = time();
    $evoting->id = $DB->insert_record('evoting', $evoting);

    // Insert Questions
    $cptQuestion = 0;

    if (isset($evoting->questionname[$cptQuestion]['text'])) {

        while (trim($evoting->questionname[$cptQuestion]['text']) != "") {

            $question = new stdClass();
            $question->name = "";
            $question->evotingid = $evoting->id;
            $question->evotinggraphicid = 1;
            $question->number = $cptQuestion + 1;
            if ($cptQuestion == 0) {
                $question->activ = 1;
            } else {
                $question->activ = 0;
            }
            $question->multipleanswers = trim($evoting->{'checkbox_multiple_answer'}[$cptQuestion]);
            $question->id = $DB->insert_record("evoting_questions", $question);
            $draftitemid = $evoting->questionname[$cptQuestion]['itemid'];
            $question->name = file_save_draft_area_files($draftitemid, $context->id, 'mod_evoting', 'questioneditor', $question->id, evoting_get_editor_options($context), trim($evoting->questionname[$cptQuestion]['text']));
            $DB->update_record('evoting_questions', $question);

            // Insert Options
            for ($i = 0; $i < MAXOPTIONS; $i++) {
                $optionText = 'option' . $i;
                $rightText = 'right' . $i;
                $option = new stdClass();
                $option->evotingquestionid = $question->id;
                $option->text = trim($evoting->{$optionText}[$cptQuestion]);
                $option->correct = trim($evoting->{$rightText}[$cptQuestion]);
                $option->timemodified = time();

                if ($option->text != "") {
                    $DB->insert_record("evoting_options", $option);
                } else {
                    continue;
                }
                
            }
            $cptQuestion++;

            if (!isset($evoting->questionname[$cptQuestion]['text'])) {
                break;
            }

        }

    }

    if (class_exists('\core_completion\api')) {
        $completiontimeexpected = !empty($evoting->completionexpected) ? $evoting->completionexpected : null;
        \core_completion\api::update_completion_date_event($evoting->coursemodule, 'evoting', $evoting->id, $completiontimeexpected);
    }

    return $evoting->id;
}

/**
 * Updates an instance of the evoting in the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param stdClass $evoting An object from the form in mod_form.php
 * @param mod_evoting_mod_form $mform The form instance itself (if needed)
 * @return boolean Success/Fail
 */
function evoting_update_instance($evoting)
{
    global $DB, $CFG;
    $cmid = $evoting->coursemodule;
    $context = context_module::instance($cmid);

    $evoting->id = $evoting->instance;
    $result = $DB->update_record('evoting', $evoting);

    // Update Questions
    $cptQuestion = 0;

    if (isset($evoting->questionname[$cptQuestion]['text'])) {

        while (trim($evoting->questionname[$cptQuestion]['text']) != "") {

            $question = new stdClass();
            $question->id = $evoting->questionnameid[$cptQuestion];
            $question->evotingid = $evoting->id;
            $question->evotinggraphicid = 1;
            $question->number = $cptQuestion + 1;
            $question->name = "";

            if ($cptQuestion == 0) {
                $question->activ = 1;
            } else {
                $question->activ = 0;
            }

            $question->multipleanswers = trim($evoting->{'checkbox_multiple_answer'}[$cptQuestion]);

            // Count if question exist to update or add
            $count = $DB->count_records("evoting_questions", array('id' => $question->id));
            if ($count > 0) {
                $result = $DB->update_record("evoting_questions", $question);
            } else {
                $question->id = $DB->insert_record("evoting_questions", $question);
            }

            $draftitemid = $evoting->questionname[$cptQuestion]['itemid'];
            $question->name = file_save_draft_area_files($draftitemid, $context->id, 'mod_evoting', 'questioneditor', $question->id, evoting_get_editor_options($context), trim($evoting->questionname[$cptQuestion]['text']));
            $DB->update_record('evoting_questions', $question);
            // Update Options
            for ($i = 0; $i < MAXOPTIONS; $i++) {
                $optionText = 'option' . $i;
                $optionId = 'optionid' . $i;
                $rightText = 'right' . $i;
                $option = new stdClass();
                $option->evotingquestionid = $question->id;
                $option->text = trim($evoting->{$optionText}[$cptQuestion]);
                $option->correct = trim($evoting->{$rightText}[$cptQuestion]);
                $option->timemodified = time();
                $option->id = $evoting->{$optionId}[$cptQuestion];

                if ($option->text != "") {
                    // Count if options exist to update or add
                    $count = $DB->count_records("evoting_options", array('id' => $option->id));
                    if ($count > 0) {
                        $result = $DB->update_record("evoting_options", $option);
                    } else {
                        $result = $DB->insert_record("evoting_options", $option);
                    }
                } else {
                    // Count if options exist to delete
                    $count = $DB->count_records("evoting_options", array('id' => $option->id));

                    if ($count > 0) {
                        $result = $DB->delete_records("evoting_options", array('id' => $option->id));
                    } else {
                        continue;
                    }
                }
            }
            $cptQuestion++;

            if (!isset($evoting->questionname[$cptQuestion]['text'])) {
                break;
            }
        }
    }

    if (class_exists('\core_completion\api')) {
        $completiontimeexpected = !empty($evoting->completionexpected) ? $evoting->completionexpected : null;
        \core_completion\api::update_completion_date_event($evoting->coursemodule, 'evoting', $evoting->id, $completiontimeexpected);
    }

    return $result;
}

/**
 * Removes an instance of the evoting from the database
 *
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 */
function evoting_delete_instance($id)
{
    global $DB;
    if (!$evoting = $DB->get_record('evoting', array('id' => $id))) {
        return false;
    }

    // Select and Delete answer.
    $answers = $DB->get_records_sql("SELECT a.id FROM {evoting_answers} a, {evoting_options} o, {evoting_questions} q, {evoting} p WHERE a.optionid = o.id AND o.evotingquestionid = q.id AND p.id = q.evotingid AND q.evotingid = ?", array('id' => $evoting->id));
    if (count(array_keys($answers))) {
        list($insql, $params) = $DB->get_in_or_equal(array_keys($answers));
        $DB->delete_records_select('evoting_answers', "id $insql", $params);
    }

    // Select and Delete options.
    $sql = "SELECT o.id
              FROM {evoting_options} o, {evoting_questions} q, {evoting} p
             WHERE o.evotingquestionid = q.id AND p.id = q.evotingid AND q.evotingid = ? ORDER BY o.id ASC";
    $options = $DB->get_records_sql($sql, array('id' => $evoting->id));
    if (count(array_keys($options))) {
        list($insql, $params) = $DB->get_in_or_equal(array_keys($options));
        // Delete history.
        $DB->delete_records_select('evoting_history', "optionid $insql", $params);
        // Delete options.
        $DB->delete_records_select('evoting_options', "id $insql", $params);
    }

    // Delete questions
    $DB->delete_records('evoting_questions', array('evotingid' => $evoting->id));

    // Delete Poll
    $DB->delete_records('evoting', array('id' => $evoting->id));

    return true;
}

/**
 * Returns a small object with summary information about what a
 * user has done with a given particular instance of this module
 * Used for user activity reports.
 *
 * $return->time = the time they did it
 * $return->info = a short text description
 *
 * @param stdClass $course The course record
 * @param stdClass $user The user record
 * @param cm_info|stdClass $mod The course module info object or record
 * @param stdClass $evoting The evoting instance record
 * @return stdClass|null
 */
function evoting_user_outline($course, $user, $mod, $evoting)
{
    $return = new stdClass();
    $return->time = 0;
    $return->info = '';
    return $return;
}

/**
 * Prints a detailed representation of what a user has done with
 * a given particular instance of this module, for user activity reports.
 *
 * It is supposed to echo directly without returning a value.
 *
 * @param stdClass $course the current course record
 * @param stdClass $user the record of the user we are generating report for
 * @param cm_info $mod course module info
 * @param stdClass $evoting the module instance record
 */
function evoting_user_complete($course, $user, $mod, $evoting)
{
}

/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in evoting activities and print it out.
 *
 * @param stdClass $course The course record
 * @param bool $viewfullnames Should we display full names
 * @param int $timestart Print activity since this timestamp
 * @return boolean True if anything was printed, otherwise false
 */
function evoting_print_recent_activity($course, $viewfullnames, $timestart)
{
    return false;
}

/**
 * Prepares the recent activity data
 *
 * This callback function is supposed to populate the passed array with
 * custom activity records. These records are then rendered into HTML via
 * {@link evoting_print_recent_mod_activity()}.
 *
 * Returns void, it adds items into $activities and increases $index.
 *
 * @param array $activities sequentially indexed array of objects with added 'cmid' property
 * @param int $index the index in the $activities to use for the next record
 * @param int $timestart append activity since this time
 * @param int $courseid the id of the course we produce the report for
 * @param int $cmid course module id
 * @param int $userid check for a particular user's activity only, defaults to 0 (all users)
 * @param int $groupid check for a particular group's activity only, defaults to 0 (all groups)
 */
function evoting_get_recent_mod_activity(&$activities, &$index, $timestart, $courseid, $cmid, $userid = 0, $groupid = 0)
{
}

/**
 * Prints single activity item prepared by {@link evoting_get_recent_mod_activity()}
 *
 * @param stdClass $activity activity record with added 'cmid' property
 * @param int $courseid the id of the course we produce the report for
 * @param bool $detail print detailed report
 * @param array $modnames as returned by {@link get_module_types_names()}
 * @param bool $viewfullnames display users' full names
 */
function evoting_print_recent_mod_activity($activity, $courseid, $detail, $modnames, $viewfullnames)
{
}

/**
 * Function to be run periodically according to the moodle cron
 *
 * This function searches for things that need to be done, such
 * as sending out mail, toggling flags etc ...
 *
 * Note that this has been deprecated in favour of scheduled task API.
 *
 * @return boolean
 */
function evoting_cron()
{
    return true;
}

/**
 * Returns all other caps used in the module
 *
 * For example, this could be array('moodle/site:accessallgroups') if the
 * module uses that capability.
 *
 * @return array
 */
function evoting_get_extra_capabilities()
{
    return array();
}

/* Gradebook API */

/**
 * Is a given scale used by the instance of evoting?
 *
 * This function returns if a scale is being used by one evoting
 * if it has support for grading and scales.
 *
 * @param int $evotingid ID of an instance of this module
 * @param int $scaleid ID of the scale
 * @return bool true if the scale is used by the given evoting instance
 */
function evoting_scale_used($evotingid, $scaleid)
{
    return false;
}

/**
 * Checks if scale is being used by any instance of evoting.
 *
 * This is used to find out if scale used anywhere.
 *
 * @param int $scaleid ID of the scale
 * @return boolean true if the scale is used by any evoting instance
 */
function evoting_scale_used_anywhere($scaleid)
{
    return false;
}

/**
 * Creates or updates grade item for the given evoting instance
 *
 * Needed by {@link grade_update_mod_grades()}.
 *
 * @param stdClass $evoting instance object with extra cmidnumber and modname property
 * @param bool $reset reset grades in the gradebook
 * @return void
 */
function evoting_grade_item_update(stdClass $evoting, $reset = false)
{
    return false;
}

/**
 * Delete grade item for given evoting instance
 *
 * @param stdClass $evoting instance object
 * @return grade_item
 */
function evoting_grade_item_delete($evoting)
{
    return false;
}

/**
 * Update evoting grades in the gradebook
 *
 * Needed by {@link grade_update_mod_grades()}.
 *
 * @param stdClass $evoting instance object with extra cmidnumber and modname property
 * @param int $userid update grade of specific user only, 0 means all participants
 */
function evoting_update_grades(stdClass $evoting, $userid = 0)
{
    return false;
}

/* File API */

/**
 * Returns the lists of all browsable file areas within the given module context
 *
 * The file area 'intro' for the activity introduction field is added automatically
 * by {@link file_browser::get_file_info_context_module()}
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @return array of [(string)filearea] => (string)description
 */
function evoting_get_file_areas($course, $cm, $context)
{
    return array();
}

/**
 * File browsing support for evoting file areas
 *
 * @package mod_evoting
 * @category files
 *
 * @param file_browser $browser
 * @param array $areas
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @param string $filearea
 * @param int $itemid
 * @param string $filepath
 * @param string $filename
 * @return file_info instance or null if not found
 */
function evoting_get_file_info($browser, $areas, $course, $cm, $context, $filearea, $itemid, $filepath, $filename)
{
    return null;
}

/**
 * Serves the files from the evoting file areas
 *
 * @package mod_evoting
 * @category files
 *
 * @param stdClass $course the course object
 * @param stdClass $cm the course module object
 * @param stdClass $context the evoting's context
 * @param string $filearea the name of the file area
 * @param array $args extra arguments (itemid, path)
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 */
function evoting_pluginfile($course, $cm, $context, $filearea, array $args, $forcedownload, array $options = array())
{
    global $DB, $CFG;

// Check the contextlevel is as expected - if your plugin is a block, this becomes CONTEXT_BLOCK, etc.
    if ($context->contextlevel != CONTEXT_MODULE) {
        return false;
    }

    // Make sure the user is logged in and has access to the module (plugins that are not course modules should leave out the 'cm' part).
    require_login($course, true, $cm);

    $context_course = context_course::instance($course -> id);
    // Check the relevant capabilities - these may vary depending on the filearea being accessed.
    if (!has_capability('mod/evoting:openevoting', $context_course)) {
        return false;
    }
    // Leave this line out if you set the itemid to null in make_pluginfile_url (set $itemid to 0 instead).
    $itemid = array_shift($args); // The first item in the $args array.

    // Use the itemid to retrieve any relevant data records and perform any security checks to see if the
    // user really does have access to the file in question.

    // Extract the filename / filepath from the $args array.
    $filename = array_pop($args); // The last item in the $args array.
    if (!$args) {
        $filepath = '/'; // $args is empty => the path is '/'
    } else {
        $filepath = '/' . implode('/', $args) . '/'; // $args contains elements of the filepath
    }


    // Retrieve the file from the Files API.
    $fs = get_file_storage();
    $file = $fs->get_file($context->id, 'mod_evoting', $filearea, $itemid, $filepath, $filename);

    if (!$file) {
        return false; // The file does not exist.
    }

    // We can now send the file back to the browser - in this case with a cache lifetime of 1 day and no filtering.
    // From Moodle 2.3, use send_stored_file instead.
    send_stored_file($file, 86400, 0, $forcedownload, $options);
}

/**
 * This function receives a calendar event and returns the action associated with it, or null if there is none.
 *
 * This is used by block_myoverview in order to display the event appropriately. If null is returned then the event
 * is not displayed on the block.
 *
 * @param calendar_event $event
 * @param \core_calendar\action_factory $factory
 * @return \core_calendar\local\event\entities\action_interface|null
 */
function mod_evoting_core_calendar_provide_event_action(calendar_event $event,
                                                            \core_calendar\action_factory $factory) {
    $cm = get_fast_modinfo($event->courseid)->instances['evoting'][$event->instance];

    $completion = new \completion_info($cm->get_course());

    $completiondata = $completion->get_data($cm, false);

    if ($completiondata->completionstate != COMPLETION_INCOMPLETE) {
        return null;
    }

    return $factory->create_instance(
            get_string('view'),
            new \moodle_url('/mod/evoting/view.php', ['id' => $cm->id]),
            1,
            true
    );
}

