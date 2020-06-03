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

require_sesskey();

/*
 * Variable to recieve
 */
$action = optional_param('action', '', PARAM_TEXT);
$idClient = optional_param('idClient', '', PARAM_TEXT);
$choice = optional_param('choice', '', PARAM_TEXT);
$idPoll = optional_param('idPoll', '', PARAM_INT);
$lang = optional_param('lang', '', PARAM_TEXT);
$validclientid = true;

// Validate client id.
if (!isset($SESSION->evotingclientid) || empty($idClient) || $idClient != $SESSION->evotingclientid) {
    $validclientid = false;
}

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
	if(confirm_sesskey()){
		switch ($action) {
			case 'client_vote':
                                if (!$validclientid) {
                                    echo "Invalid session";
                                    break;
                                }
				//Add vote
				echo json_encode(evoting_vote($idClient, $choice, $idPoll, $lang));
				break;
			case 'client_get_options_poll':
				//Get options
				echo json_encode(evoting_get_options_poll($idPoll, $idClient));
				break;
			/*case 'client_cancel_vote':
				if (!$validclientid) {
					echo "Invalid session";
					break;
				}
				echo evoting_cancel_vote($idPoll, $idClient, $lang);
				break;*/
		}
	} 
}
?>