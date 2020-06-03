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
 * evoting conversion handler
 */
class moodle1_mod_evoting_handler extends moodle1_mod_handler {

    /** @var moodle1_file_manager */
    protected $fileman = null;

    /** @var int cmid */
    protected $moduleid = null;

    /**
     * Declare the paths in moodle.xml we are able to convert
     *
     * The method returns list of {@link convert_path} instances.
     * For each path returned, the corresponding conversion method must be
     * defined.
     *
     * Note that the path /MOODLE_BACKUP/COURSE/MODULES/MOD/evoting does not
     * actually exist in the file. The last element with the module name was
     * appended by the moodle1_converter class.
     *
     * @return array of {@link convert_path} instances
     */
    public function get_paths() {
        return array(
            new convert_path(
                'evoting', '/MOODLE_BACKUP/COURSE/MODULES/MOD/evoting',
                array(
                    'renamefields' => array(
                        'text' => 'intro',
                        'format' => 'introformat',
                    ),
                    'newfields' => array(
                        'completionsubmit' => 0,
                    ),
                    'dropfields' => array(
                        'modtype'
                    ),
                )
            ),
            new convert_path('evoting_options', '/MOODLE_BACKUP/COURSE/MODULES/MOD/evoting/OPTIONS'),
            new convert_path('evoting_option', '/MOODLE_BACKUP/COURSE/MODULES/MOD/evoting/OPTIONS/OPTION'),
        );
    }

    /**
     * This is executed every time we have one /MOODLE_BACKUP/COURSE/MODULES/MOD/evoting
     * data available
     */
    public function process_evoting($data) {

        // get the course module id and context id
        $instanceid     = $data['id'];
        $cminfo         = $this->get_cminfo($instanceid);
        $this->moduleid = $cminfo['id'];
        $contextid      = $this->converter->get_contextid(CONTEXT_MODULE, $this->moduleid);

        // get a fresh new file manager for this instance
        $this->fileman = $this->converter->get_file_manager($contextid, 'mod_evoting');

        // convert course files embedded into the intro
        $this->fileman->filearea = 'intro';
        $this->fileman->itemid   = 0;
        $data['intro'] = moodle1_converter::migrate_referenced_files($data['intro'], $this->fileman);

        // start writing evoting.xml
        $this->open_xml_writer("activities/evoting_{$this->moduleid}/evoting.xml");
        $this->xmlwriter->begin_tag('activity', array('id' => $instanceid, 'moduleid' => $this->moduleid,
            'modulename' => 'evoting', 'contextid' => $contextid));
        $this->xmlwriter->begin_tag('evoting', array('id' => $instanceid));

        foreach ($data as $field => $value) {
            if ($field <> 'id') {
                $this->xmlwriter->full_tag($field, $value);
            }
        }

        return $data;
    }

    /**
     * This is executed when the parser reaches the <OPTIONS> opening element
     */
    public function on_evoting_options_start() {
        $this->xmlwriter->begin_tag('options');
    }

    /**
     * This is executed every time we have one /MOODLE_BACKUP/COURSE/MODULES/MOD/evoting/OPTIONS/OPTION
     * data available
     */
    public function process_evoting_option($data) {
        $this->write_xml('option', $data, array('/option/id'));
    }

    /**
     * This is executed when the parser reaches the closing </OPTIONS> element
     */
    public function on_evoting_options_end() {
        $this->xmlwriter->end_tag('options');
    }

    /**
     * This is executed when we reach the closing </MOD> tag of our 'evoting' path
     */
    public function on_evoting_end() {
        // finalize evoting.xml
        $this->xmlwriter->end_tag('evoting');
        $this->xmlwriter->end_tag('activity');
        $this->close_xml_writer();

        // write inforef.xml
        $this->open_xml_writer("activities/evoting_{$this->moduleid}/inforef.xml");
        $this->xmlwriter->begin_tag('inforef');
        $this->xmlwriter->begin_tag('fileref');
        foreach ($this->fileman->get_fileids() as $fileid) {
            $this->write_xml('file', array('id' => $fileid));
        }
        $this->xmlwriter->end_tag('fileref');
        $this->xmlwriter->end_tag('inforef');
        $this->close_xml_writer();
    }
}
