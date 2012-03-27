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
 * Defines, displays and processes the settings form
 *
 * Since normal settings.php behaviour doesn't appear to apply to admin tools,
 * this page creates, displays and processess a form for configuring the tool.
 *
 * @package tool
 * @subpackage targetgrades
 * @author      Mark Johnson <mark.johnson@tauntons.ac.uk>
 * @copyright   2011 Tauntons College, UK
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../../config.php');
require_once($CFG->dirroot.'/lib/formslib.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/'.$CFG->admin.'/tool/targetgrades/lib.php');

use tool\targetgrades as tg;

require_login($SITE);
admin_externalpage_setup('tooltargetgrades', null, null, '/'.$CFG->admin.'/tool/targetgrades/settingsform.php');
$PAGE->navbar->add(get_string('settings'));

/**
 * Defines the class for the settings form
 *
 * Defines the form elements for editing settings, validates and processes the input.
 *
 */
class tool_targetgrades_settingsform extends moodleform {

    /**
     * Defines fields for editing the settings
     *
     * Defines fiels for editing the following settings for tool_targetgrade:
     * group_length
     * group_field
     * gcse_field
     * roles
     * categories
     * exclude_field
     * exclude_regex
     *
     * @see lib/moodleform#definition()
     */
    public function definition() {
        $mform = $this->_form;

        $fields = $this->_customdata['fields'];
        $userfields = $this->_customdata['userfields'];
        $categories = $this->_customdata['categories'];
        $roles = $this->_customdata['roles'];

        /**
         * Defines the number of characters to use for the pattern that matches courses
         * to a set of ALIS statistics
         */
        $mform->addElement('text', 'group_length', get_string('group_length', 'tool_targetgrades'));
        $mform->setType('group_length', PARAM_NUMBER);

        /**
         * Defines the field to use for the pattern that matches courses against a set
         * of ALIS statistics
         */
        $mform->addElement('select', 'group_field', get_string('group_field', 'tool_targetgrades'), $fields);
        $mform->addHelpButton('group_field', 'group_fielddesc', 'tool_targetgrades');

        /**
         * Defines the user field where the average GCSE scrore is stored
         */
        $mform->addElement('select', 'gcse_field', get_string('gcse_field', 'tool_targetgrades'), array_merge(array('0'=>''), $userfields));
        $mform->addHelpButton('gcse_field', 'gcse_field', 'tool_targetgrades');
        $mform->addRule('gcse_field', get_string('err_gcsefield', 'tool_targetgrades'), 'nonzero', null, 'client');
        $mform->addRule('gcse_field', null, 'required');

        /**
         * Defines the roles to which target grades should be distributed
         */
        $roleselect = &$mform->addElement('select', 'roles', get_string('roles', 'tool_targetgrades'), $roles);
        $roleselect->setMultiple(true);
        $mform->setDefault('roles', array_keys($roles));
        $mform->addHelpButton('roles', 'roles', 'tool_targetgrades');
        $mform->addRule('roles', null, 'required', null, 'client');

        /**
         * Defines the course categories that should be made available for distribution
         */
        $catselect = &$mform->addElement('select', 'categories', get_string('categories', 'tool_targetgrades'), $categories);
        $catselect->setMultiple(true);
        $mform->setDefault('categories', array_keys($categories));
        $mform->addHelpButton('categories', 'categories', 'tool_targetgrades');
        $mform->addRule('categories', null, 'required', null, 'client');

        /**
         * Defines the field that should be matched to the regex to find which courses
         * should NOT be available for distribution
         */
        $mform->addElement('select', 'exclude_field', get_string('exclude_field', 'tool_targetgrades'), $fields);

        /**
         * Defines the regex that the above field should be matched against to find
         * courses which should NOT be available for distribution
         */
        $mform->addElement('text', 'exclude_regex', get_string('exclude_regex', 'tool_targetgrades'));
        $mform->addHelpButton('exclude_regex', 'exclude_regexdesc', 'tool_targetgrades');

        $this->add_action_buttons();
    }

    /**
     * Validates input
     *
     * Validates the exclude regex to ensure that it doesn't pose a risk
     * of ReDOS, and checks that settings used together
     * (group_length/groupfield and exclude_regex/exlude_field) are both
     * defined if either are.
     *
     * @param $data The data submitted to the form
     * @see lib/moodleform#validation($data, $files)
     */
    public function validation($data) {
        $errors = array();
		if(preg_match('/(.+?\.?[*+].*?)[*+]/', $data['exclude_regex'])) {
		    $errors['exclude_regex'] = get_string('unsaferegex', 'tool_targetgrades');
		}

		if(!empty($data['group_length']) && empty($data['group_field'])) {
		    $errors['group_field'] = get_string('err_group_field', 'tool_targetgrades');
		}
		if(empty($data['group_length']) && !empty($data['group_field'])) {
		    $errors['group_length'] = get_string('err_group_length', 'tool_targetgrades');
		}
		if(!empty($data['exclude_regex']) && empty($data['exclude_field'])) {
		    $errors['exclude_field'] = get_string('err_exclude_field', 'tool_targetgrades');
		}

        return $errors;
    }

    /**
     * Processes the settings and saves them
     *
     * Takes the valid settings from the form, serialises the arrays and
     * saves them in the tool's plugin config.
     *
     * @param $data
     */
    public function process($data) {
        $data->roles = serialize($data->roles);
        $data->categories = serialize($data->categories);
        foreach ($data as $field => $value) {
            set_config($field, $value, 'tool_targetgrades');
        }
    }

}

list($in_sql, $params) = $DB->get_in_or_equal(explode(',', $CFG->gradebookroles));
$customdata = array(
	'fields' => array('',
	    'shortname' => get_string('shortname'),
	    'fullname' => get_string('fullname'),
	    'idnumber' => get_string('idnumber')),
	'roles' => $DB->get_records_select_menu('role', 'id '.$in_sql, $params),
	'categories' => $DB->get_records_menu('course_categories'),
	'userfields' => $DB->get_records_menu('user_info_field', null, '', 'id, name')
);

if (empty($customdata['userfields'])) {
    throw new moodle_exception('err_nouserfields', 'tool_targetgrades', new moodle_url('/user/profile/index.php'));
}

$form = new tool_targetgrades_settingsform(null, $customdata);
if ($config = tg\get_config()) {
    $form->set_data($config);
}
if ($data = $form->get_data()) {
   $form->process($data);
   redirect(new moodle_url('/'.$CFG->admin.'/tool/targetgrades'), get_string('settingssaved', 'tool_targetgrades'), 3);
   exit();
}

echo $OUTPUT->header();
echo tg\print_tabs(3);
$form->display();
echo $OUTPUT->footer();

?>
