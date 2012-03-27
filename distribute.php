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
 * Distribute and Calulate Target Grades
 *
 * Displays a Big Select List to allow selection of courses for grades to be
 * distributed to.
 * When the form Calculate button is pressed, each selected course has
 * the 4 required grade items created if necessary, and the grades for each student are
 * calculated and entered. The gradebook is then re-sorted to move the target
 * grade items to the front.
 * If the Recalculate button is pressed, the grades on the pages which already
 * have the grade items on are recalculated.
 *
 * @package tool
 * @subpackage targetgrades
 * @author      Mark Johnson <mark.johnson@tauntons.ac.uk>
 * @copyright   2011 Tauntons College, UK
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../../config.php');
require_once($CFG->libdir.'/gradelib.php');
require_once($CFG->dirroot.'/user/selector/lib.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/'.$CFG->admin.'/tool/targetgrades/lib.php');

use tool\targetgrades as tg;

require_login($SITE);
admin_externalpage_setup('tooltargetgrades', null, null, '/'.$CFG->admin.'/tool/targetgrades/distribute.php');
$PAGE->navbar->add(get_string('mtgdistribute', 'tool_targetgrades'));


$context = get_context_instance(CONTEXT_SYSTEM);
if (!has_capability('tool/targetgrades:distribute', $context)){
    print_error('noperms', 'tool_targetgrades');
}

$defaultscale = optional_param('defaultscale', null, PARAM_INT);
if (!empty($defaultscale)) {
    set_config('defaultscale', $defaultscale, 'tool_targetgrades');
}

$config = tg\get_config(); // Get the raw config data for the block

if(preg_match('/(.+?\.?[*+].*?)[*+]/', $config->exclude_regex)) {
    print_error('unsaferegex', 'tool_targetgrades');
}

$potential_selector = new tg\potential_course_selector('potentialcourses');
$distributed_selector = new tg\distributed_course_selector('distributedcourses');

$potential_selector->exclude(array_keys(current($distributed_selector->find_users())));

$courses = array();
if (optional_param('calculate', false, PARAM_TEXT)) {
    $courses = $potential_selector->get_selected_users();
} else if (optional_param('recalculate', false, PARAM_TEXT)) {
    $courses = current($distributed_selector->find_users());
}


if (!empty($courses)) {

	set_time_limit(0); // This could take a while, so disable max execution time
    $output = '';

    $itemnames = array('target'   =>  get_string('item_mtg', 'tool_targetgrades'),
                        'min'  =>  get_string('item_alis', 'tool_targetgrades'),
                        'alisnum' =>    get_string('item_alisnum', 'tool_targetgrades'),
                        'avgcse' => get_string('item_avgcse', 'tool_targetgrades'),
                        'cpg' => get_string('item_cpg', 'tool_targetgrades'));
    $empty_courses = array();
    $unconfigured_courses = array();
    $empty_students = array();
    $failed_grade_calcs = array();
    $errors = '';
    $infofield = $DB->get_record('user_info_field', array('id' => $config->gcse_field));

    foreach($courses as $course) {
        $category = grade_category::fetch_course_category($course->id);

        $records = null;

        $regrade = false;
        foreach ($itemnames as $item => $itemname) {
            try {
                if($grade_item = grade_item::fetch(array('idnumber'=>'targetgrades_'.$item, 'courseid'=>$course->id))) {
                    $itemdata = new stdClass();
                    if(empty($grade_item->timecreated)) {
                        $itemdata->timecreated = time();
                    }
                    if(empty($grade_item->itemnumber)) {
                        $itemdata->itemnumber = 0;
                    }
                    grade_item::set_properties($grade_item, $itemdata);
                    $grade_item->update();
                    unset($itemdata);
                    throw new tg\grade_item_exists_exception($item, $grade_item->id);
                }

                $itemclass = 'tool\targetgrades\item_'.$item;
                $itemdata = new $itemclass($course->id, $category->id);
                if (in_array($item, array('target', 'min', 'cpg'))) {
                    try {
                        $itemdata->set_scale($course->qualtype, $defaultscale);
                    } catch (Exception $e) {
                        $failed_grade_calcs++;
                        $errors .= get_string('nogradescale', 'tool_targetgrades', $e->getMessage()).'<br />';
                    }
                }

                $grade_item = new grade_item(array('courseid'=>$course->id, 'itemtype'=>'manual'), false);
                grade_item::set_properties($grade_item, $itemdata);
                $itemids[$item] = $grade_item->insert();
                $regrade = true;

            } catch (tg\grade_item_exists_exception $e) {
                $itemids[$e->getMessage()] = $e->getId();
            }

        }

        if($course->qualtype) {
            // Get the ID and average GCSE score of all students in the course

            $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);

            $select = 'SELECT u.id AS id ';

            $from = 'FROM {user} u
                    JOIN {role_assignments} ra
                        ON u.id = ra.userid ';

			list($in_sql, $in_params) = $DB->get_in_or_equal($config->roles);
            $params = array_merge($in_params, array($coursecontext->id));
            $where = 'WHERE u.deleted = 0
                        AND ra.roleid '.$in_sql.'
                        AND ra.contextid = ?';

            try {
                $students = $DB->get_records_sql($select.$from.$where, $params);

                if(!$students) {
                    throw new tg\no_students_exception($course->id);
                }

                if (!isset($course->pattern)) {
                    throw new tg\no_config_for_course_exception($course->id);
                }

                // If there are some students in the class
                foreach($students as $student) {
                    if($data = $DB->get_record('user_info_data', array('fieldid' => $infofield->id, 'userid' => $student->id))) {
                        $student->avgcse = $data->data;
                    } else {
                        $student->avgcse = '';
                    }

                    try {
                        if (empty($student->avgcse)) {
                            throw new tg\no_data_for_student_exception($student->id);
                        }

                        if($avgcse = grade_grade::fetch(array('itemid' => $itemids['avgcse'], 'userid' => $student->id))) {
                            // If they've already got an average gcse grade, update it
                            $avgcse->rawgrade = $student->avgcse;
                            $avgcse->finalgrade = $student->avgcse;
                            $avgcse->timemodified = time();
                            $avgcse->update('tool_targetgrades');
                        } else {
                            $avgcse = new grade_grade();
                            $avgcse->itemid = $itemids['avgcse'];
                            $avgcse->userid = $student->id;
                            $avgcse->rawgrade = $student->avgcse;
                            $avgcse->finalgrade = $student->avgcse;
                            $avgcse->timecreated = time();
                            $avgcse->timemodified = time();
                            $avgcse->insert('tool_targetgrades');
                        }

                        $mtg = tg\calculate_mtg($student, $course);

                        if($alis = grade_grade::fetch(array('itemid' => $itemids['min'], 'userid' => $student->id))) {
                            $alis->rawgrade = $mtg['grade'];
                            $alis->finalgrade = $mtg['grade'];
                            $alis->timemodified = time();
                            $alis->update('tool_targetgrades');
                        } else {
                            $alis = new grade_grade();
                            $alis->itemid = $itemids['min'];
                            $alis->userid = $student->id;
                            $alis->rawgrade = $mtg['grade'];
                            $alis->finalgrade = $mtg['grade'];
                            $alis->timecreated = time();
                            $alis->timemodified = time();
                            $alis->insert('tool_targetgrades');
                        }

                        if($alis_num = grade_grade::fetch(array('itemid' => $itemids['alisnum'], 'userid' => $student->id))){
                            $alis_num->rawgrade = $mtg['number'];
                            $alis_num->finalgrade = $mtg['number'];
                            $alis_num->timemodified = time();
                            $alis_num->update('tool_targetgrades');
                        } else {
                            $alis_num = new grade_grade();
                            $alis_num->itemid = $itemids['alisnum'];
                            $alis_num->userid = $student->id;
                            $alis_num->rawgrade = $mtg['number'];
                            $alis_num->finalgrade = $mtg['number'];
                            $alis_num->timecreated = time();
                            $alis_num->timemodified = time();
                            $alis_num->insert('tool_targetgrades');
                        }

                    } catch (tg\no_data_for_student_exception $e) {
                        $empty_students[] = $e->getMessage();
                    } catch (tg\no_mtg_for_student_exception $e) {
                        $failed_grade_calcs[] = $e->getMessage();
                    }

                }

            } catch (tg\no_students_exception $e) {
                $empty_courses[] = $e->getMessage();
            } catch (tg\no_config_for_course_exception $e) {
                $unconfigured_courses[] = $e->getMessage();
            }
            if($regrade) {
                grade_regrade_final_grades($course->id);
                tg\sort_gradebook($course);
            }
        }
    }

    $output = html_writer::start_tag('p').
    get_string('distribute_success', 'tool_targetgrades', count($distributed_selector->find_users())-count($empty_courses)-count($unconfigured_courses)).
    html_writer::empty_tag('br').
    get_string('distribute_empty', 'tool_targetgrades', count($empty_courses)).
    html_writer::empty_tag('br').
    get_string('distribute_unconfigured', 'tool_targetgrades', count($unconfigured_courses)).
    html_writer::empty_tag('br').
    get_string('distribute_noavgcse', 'tool_targetgrades', count(array_unique($empty_students))).
    html_writer::empty_tag('br').
    get_string('distribute_failedcalc', 'tool_targetgrades', count($failed_grade_calcs)).
    html_writer::empty_tag('br').
    $errors.
    html_writer::end_tag('p');

}


$table = new html_table('course_selector');
$row = new html_table_row();
$row->cells[] = $distributed_selector->display(true);
$cell = html_writer::empty_tag('input', array('id' => 'tool_targetgrades_calcbutton', 'name' => 'calculate', 'type' => 'submit', 'value' => $OUTPUT->larrow().' '.get_string('calculategrades', 'tool_targetgrades')));
$cell .= $OUTPUT->help_icon('calculategrades', 'tool_targetgrades');
$row->cells[] = $cell;
$row->cells[] = $potential_selector->display(true);
$table->data[] = $row;

$scales = $DB->get_records_menu('scale', null, null, 'id, name');

$defaultscale = (isset($config->defaultscale)) ? $config->defaultscale : null;

echo $OUTPUT->header();
tg\print_tabs(2);

if (isset($output)) {
    echo $output;
}

echo html_writer::start_tag('form', array('action' => $PAGE->url->out(), 'method' => 'post'));
echo html_writer::tag('label', get_string('defaultscale', 'tool_targetgrades'), array('for' => 'defaultscale'));
echo html_writer::select($scales, 'tool_targetgrades', $defaultscale);
echo $OUTPUT->help_icon('defaultscale', 'tool_targetgrades');
echo html_writer::table($table);
echo html_writer::empty_tag('input', array('type' => 'submit', 'name' => 'recalculate', 'value' => get_string('recalculate', 'tool_targetgrades')));
echo $OUTPUT->help_icon('recalculate', 'tool_targetgrades');
echo html_writer::end_tag('form');

echo $OUTPUT->footer();

?>
