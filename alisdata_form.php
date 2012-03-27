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
 * Defines the form for uploading ALIS data
 *
 * @package tool
 * @subpackage targetgrades
 * @author      Mark Johnson <mark.johnson@tauntons.ac.uk>
 * @copyright   2011 Tauntons College, UK
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->libdir.'/formslib.php');

/**
 * The upload form's class for uploading ALIS data
 */
### @export "alisdata_form"
class alisdata_upload_form extends moodleform {

    /**
     * Define the form for uploading the ALIS data as a CSV file.
     */
    public function definition() {
        $mform    =& $this->_form;
        $mform->addElement('hidden', 'blah', 'blah');
        $mform->addElement('filepicker', 'equationsfile', get_string('equationsfile', 'tool_targetgrades'));
        $mform->addElement('static', 'filedesc', '', get_string('equationsfiledesc', 'tool_targetgrades'));
        $mform->addElement('submit', 'upload', get_string('upload'));
    }
}
### @end

?>
