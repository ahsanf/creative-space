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
 * Upgrade evoting
 * @param $oldversion
 * @return bool
 */
function xmldb_evoting_upgrade($oldversion) {
    global  $DB;

    $dbman = $DB->get_manager();

    $tableOptions = new xmldb_table('evoting_options');
    $fieldCorrect = new xmldb_field('correct', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');

    if (!$dbman->field_exists($tableOptions, $fieldCorrect)) {
        $dbman->add_field($tableOptions, $fieldCorrect);
    }

    $tableQuestions = new xmldb_table('evoting_questions');
    $multipleAnswers = new xmldb_field('multipleanswers', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');

    if (!$dbman->field_exists($tableQuestions, $multipleAnswers)) {
        $dbman->add_field($tableQuestions, $multipleAnswers);
    }
    
    return true;
}


