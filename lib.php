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
 * Library of functions, classes and constants for Target Grade distribution
 *
 * @package tool
 * @subpackage targetgrades
 * @author      Mark Johnson <mark.johnson@tauntons.ac.uk>
 * @copyright   2011 Tauntons College, UK
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

### @export "namespace"
namespace tool\targetgrades;
### @end
/**
 * These constants store the strings used by ALIS to describe each type of
 * qualification that statistics are available for.
 */

### @export "alisconstants"
const ALIS_GCSE = 'GCSE';
const ALIS_ADVANCED_GCE = 'Advanced GCE';
const ALIS_ADVANCED_GCE_DOUBLE = 'Advanced GCE (Double Award)';
const ALIS_ADVANCED_SUBSIDIARY_GCE = 'Advanced Subsidiary GCE';
const ALIS_ADVANCED_SUBSIDIARY_GCE_DOUBLE = 'Advanced Subsidiary GCE (Double Award)';
const ALIS_INTERMEDIATE_GNVQ = 'Intermediate GNVQ';
const ALIS_IB_STANDARD = 'IB Standard';
const ALIS_IB_HIGHER = 'IB Higher';
const ALIS_CACHE_L3_DIPLOMA = 'CACHE Level 3 Diploma';
const ALIS_OCR_NATIONAL_CERTIFICATE = 'OCR National Certificate';
const ALIS_OCR_NATIONAL_DIPLOMA = 'OCR National Diploma';
const ALIS_BTEC_NATIONAL_AWARD = 'BTEC National Award';
const ALIS_BTEC_NATIONAL_CERTIFICATE = 'BTEC National Certificate';
const ALIS_BTEC_NATIONAL_DIPLOMA = 'BTEC National Diploma';
const ALIS_BTEC_FIRST_DIPLOMA = 'BTEC First Diploma';
### @end

/**
 * These constants define the various grade scales required for the above
 * qualifications. Only unique scales are stored here, e.g. the CACHE L3 diploma
 * can use MTG_SCALE_ADVANCED_SUBSIDIARY_GCE as they are the same.
 */
### @export "scaleconstants"
const MTG_SCALE_GCSE = 'U,G,F,E,D,C,B,A,A*';
const MTG_SCALE_ADVANCED_GCE = 'U,E,D,C,B,A,A*';
const MTG_SCALE_ADVANCED_SUBSIDIARY_GCE = 'U,E,D,C,B,A,A*';
const MTG_SCALE_BTEC_AWARD = 'Fail,Pass,Merit,Distinction';
const MTG_SCALE_BTEC_CERTIFICATE = 'Fail,PP,MP,MM,DM,DD';
const MTG_SCALE_BTEC_DIPLOMA = 'Fail,PPP,MPP,MMP,MMM,DMM,DDM,DDD';
const MTG_SCALE_ADVANCED_GCE_DOUBLE = 'U,EE,DE,DD,CD,CC,BC,BB,AB,AA,A*A,A*A*';
const MTG_SCALE_ADVANCED_SUBSIDIARY_GCE_DOUBLE = 'U,EE,DE,DD,CD,CC,BC,BB,AB,AA,A*A,A*A*';
const MTG_SCALE_IB = '1,2,3,4,5,6,7';
### @end

/**
 * The threshold under which to mark stats has having low correlations
 * @var float
 */
### @export "correlationthreshold"
const CORRELATION_THRESHOLD = 0.3;
### @end

### @export "itemprefix"
const GRADE_ITEM_PREFIX = 'targetgrades_';
### @end

/**
 * Returns the grade scale for the provided qualification type.
 *
 * @param string $qualtype
 */
### @export "get_scale"
function get_scale($qualtype) {
    switch ($qualtype) {
        case ALIS_GCSE:
            return MTG_SCALE_GCSE;
            break;

        case ALIS_ADVANCED_GCE:
            return MTG_SCALE_ADVANCED_GCE;
            break;

        case ALIS_ADVANCED_GCE_DOUBLE:
            return MTG_SCALE_ADVANCED_GCE_DOUBLE;
            break;

        case ALIS_ADVANCED_SUBSIDIARY_GCE:
            return MTG_SCALE_ADVANCED_SUBSIDIARY_GCE;
            break;

        case ALIS_ADVANCED_SUBSIDIARY_GCE_DOUBLE:
            return MTG_SCALE_ADVANCED_SUBSIDIARY_GCE_DOUBLE;
            break;

        case ALIS_INTERMEDIATE_GNVQ:
            // TODO: Work out what this should be
            return false;
            break;

        case ALIS_IB_STANDARD:
            return MTG_SCALE_IB;
            break;

        case ALIS_IB_HIGHER:
            return MTG_SCALE_IB;
            break;

        case ALIS_CACHE_L3_DIPLOMA:
            return MTG_SCALE_ADVANCED_SUBSIDIARY_GCE;
            break;

        case ALIS_OCR_NATIONAL_CERTIFICATE:
            return MTG_SCALE_BTEC_AWARD;
            break;

        case ALIS_OCR_NATIONAL_DIPLOMA:
            return MTG_SCALE_BTEC_CERTIFICATE;
            break;

        case ALIS_BTEC_NATIONAL_AWARD:
            return MTG_SCALE_BTEC_AWARD;
            break;

        case ALIS_BTEC_NATIONAL_CERTIFICATE:
            return MTG_SCALE_BTEC_CERTIFICATE;
            break;

        case ALIS_BTEC_NATIONAL_DIPLOMA:
            return MTG_SCALE_BTEC_DIPLOMA;
            break;

        case ALIS_BTEC_FIRST_DIPLOMA:
            return MTG_SCALE_BTEC_AWARD;
            break;
    }
}
### @end


/**
 * Gets the config data for the plugin with arrays unserialized.
 *
 * Will normally only query the database the first time it's run, subsequent requests
 * will just return the static $config. However, this behaviour can be overridden using
 * the $force parameter, to ensure the data is fresh.
 *
 * @param $force bool Force a fresh query on the database (defaults to false)
 * @return object The config data.
 */
### @export "get_config"
function get_config($force = false) {
    static $config;
    if (empty($config) || $force) {
        $config = \get_config('tool_targetgrades');
        $config->categories = isset($config->categories) ? unserialize($config->categories) : array();
        $config->roles = isset($config->roles) ? unserialize($config->roles) : array();
    }
    return $config;
}
### @end

/**
 * Append an asterisk to the course's name if it doesn't have ALIS data
 *
 * @global object $DB Global database object
 * @param array $options the courses being used as options for the select list
 * @return array The options with asterisks added where appropriate
 */
### @export "hasconfig"
function hasconfig($options){
     array_walk($options, function ($option){ // Mark all courses that don't have any ALIS data
        global $DB;
        $select = 'SELECT * ';
        $from = 'FROM {tool_targetgrades_patterns} p
            JOIN {tool_targetgrades_alisdata} d ON p.alisdataid = d.id ';
        $where = 'WHERE p.pattern = ?';
        $params = array($option->pattern);
        if($DB->get_record_sql($select.$from.$where, $params)) {
            return true;
        } else {
            $option->firstname = '*';
        }
    });
    return $options;
}
### @end

/**
 * Calculates a minimum target grade for a particular course based on
 * an average GCSE score
 *
 * @param $course text either a full classcode or just the first 5 characters (will take the first 5 characters anyway)
 * @param $avgcse int student's average gcse score
 *
 * @return integer relating to the grade on a scale or false if fails
 */
### @export "calculate_mtg"
function calculate_mtg($student, $course){
    global $DB;
    $select = 'SELECT name, gradient, intercept ';
    $from = 'FROM {tool_targetgrades_patterns} AS p
        JOIN {tool_targetgrades_alisdata} AS d ON p.alisdataid = d.id ';
    $where = 'WHERE p.pattern = ?';
    $params = array($course->pattern);

    if($alis = $DB->get_record_sql($select.$from.$where, $params)) {
        $points = ($student->avgcse * $alis->gradient)+$alis->intercept;

        // Calculate UCAS points using ALIS formula

        switch($course->qualtype) {
            // Calculate grade value based on course type

            case ALIS_ADVANCED_GCE: // A2
                // Divide points score by 2
                // Divide by 10
                // Round Up
                $mtg = \ceil((($points/2))/10);
                break;

            case ALIS_ADVANCED_GCE_DOUBLE: // A2 Double Award
                // Divide points score by 2
                // Minus 20
                // Divide by 10
                // Round Up
                $mtg = \ceil((($points/2)-20)/10);
                break;

            case ALIS_ADVANCED_SUBSIDIARY_GCE: // AS
                // Minus 20
                // Divide by 10
                // Round Up
                $mtg = \ceil($points/10);

                break;

            case ALIS_ADVANCED_SUBSIDIARY_GCE_DOUBLE: // AS Double Award
                // Divide by 10
                // Round Up
                $mtg = \ceil(($points-20)/10);
                break;

            case ALIS_BTEC_NATIONAL_DIPLOMA: // BTEC Diploma
                // Add 30
                // Minus 100
                // Divide by 40
                // Round up
                // Add 1 (scale includes Fail)
                $mtg = \ceil((($points+20)-100)/40)+1;
                break;

            case ALIS_BTEC_NATIONAL_CERTIFICATE: // BTEC Certificate
                $mtg = \ceil($points/40);
                break;

            case ALIS_BTEC_NATIONAL_AWARD: // BTEC Award
                // Add 10
                // Divide by 40
                // Round up
                // Add 1 (scale includes Fail)
                $mtg = \ceil($points/40)+1;
                if($mtg > 4) {
                   $mtg = 4;
                }
                break;

            case ALIS_CACHE_L3_DIPLOMA: // CACHE L3 Diploma
                $mtg = \ceil($points/60);
                break;

             // These all provide a raw number on the scale, rather than UCAS
            // tariff points.
            case ALIS_IB_STANDARD: // IB
            case ALIS_IB_HIGHER: // IB
            case ALIS_BTEC_FIRST_DIPLOMA: // BTEC 1st
            case ALIS_OCR_NATIONAL_DIPLOMA: // OCR Diploma
            case ALIS_OCR_NATIONAL_CERTIFICATE: // OCR Certificate
                // Add 0.5
                // Round up
                $mtg = \ceil($points+0.5);
                if ($course->qualtype == ALIS_BTEC_FIRST_DIPLOMA
                        || $course->qualtype == ALIS_OCR_NATIONAL_DIPLOMA
                        ||  $course->qualtype == ALIS_OCR_NATIONAL_CERTIFICATE) {
                    // For these 3, add an extra one as the scale includes Fail
                    $mtg += 1;
                }
                break;

            case ALIS_GCSE: // GCSE
                // Always a C, since if they're in FE and taking a GCSE it's becuase
                // they failed and are trying to pass.
                $mtg = 6;
                break;

            default:
                $mtg = '';

        }

        if ($mtg !== '' && $mtg <= 1) {
            $mtg = 2;
        }
        $alis = array();
        $alis['number'] = $points;
        $alis['grade'] = $mtg;
        return $alis;

    } else {
        throw new no_mtg_for_student_exception($student->id);
    }

}
### @end

/**
 * Builds a set of options to select pattens to apply ALIS stats to
 *
 * @global object $DB The global config object
 * @return array The array of options for the menu
 * @throws Exception if the exclusion regex shows a risk of ReDOS
 */
### @export "build_pattern_options"
function build_pattern_options() {

    global $DB;
    $config = get_config();
    if(\preg_match('/(.+?\.?[*+].*?)[*+]/', $config->exclude_regex)) {
        throw new unsafe_regex_exception();
    }

    list($in_sql, $params) = $DB->get_in_or_equal($config->categories);

    $select = 'SELECT DISTINCT ';
    if(!empty($config->group_field) && !empty($config->group_length)) {
        $select .= 'LEFT('.$config->group_field.', '.$config->group_length.'),
                        LEFT('.$config->group_field.', '.$config->group_length.') as pattern ';
    } else {
        $select .= 'shortname, shortname AS pattern ';
    }

    $from = 'FROM {course} ';

    $order = 'ORDER BY pattern';

    if ($DB->sql_regex_supported()) {
        $where = 'WHERE category '.$in_sql.' AND '.$config->exclude_field.' '.$DB->sql_regex(false).' ? ';
        $params = array_merge($params, array($config->exclude_regex));
    } else {
        $courses = $DB->get_records_select('course', 'category '.$in_sql, $params);

        \array_filter($courses, function($course, $config) {
            $field = $config->exclude_field;
            return !\preg_match('/'.$config->exclude_regex.'/', $course->$field);
        });

        list($in_sql, $params) = $DB->get_in_or_equal(array_keys($courses));
        $where = 'WHERE id '.$in_sql.' ';
    }

    $where .= 'AND LEFT('.$config->group_field.', '.$config->group_length.') NOT IN
                (SELECT pattern FROM {tool_targetgrades_patterns} tp) ';

    $options = $DB->get_records_sql_menu($select.$from.$where.$order, $params);

    return $options;
}
### @end

/**
 * Re-sorts the gradebook to put all MTG grade items first.
 *
 * @param object $course Database record for course, containing the id.
 */
### @export "sort_gradebook"
function sort_gradebook($course) {

    global $CFG, $DB;
    require_once $CFG->dirroot.'/grade/lib.php';
    require_once $CFG->dirroot.'/grade/edit/tree/lib.php';
    $gtree = new \grade_tree($course->id, false, false);

    $fields = array(
        GRADE_ITEM_PREFIX.'avgcse',
        GRADE_ITEM_PREFIX.'alisnum',
        GRADE_ITEM_PREFIX.'min',
        GRADE_ITEM_PREFIX.'target',
        GRADE_ITEM_PREFIX.'cpg'
    );
    $params = array($course->id);
    list($in_sql, $in_params) = $DB->get_in_or_equal($fields);
    $params = \array_merge($params, $in_params);
    $where = 'courseid = ? AND idnumber '.$in_sql;
    $gradeitems = $DB->get_records_select('grade_items', $where, $params, 'itemnumber DESC');
    $courseitem = $DB->get_record('grade_items', array('courseid' => $course->id, 'itemtype' => 'course'));

    foreach($gradeitems as $item) {

        if (!$element = $gtree->locate_element('i'.$item->id)) {
            \print_error('invalidelementid');
        }
        $object = $element['object'];

        $moveafter = 'c'.$courseitem->iteminstance;

        if(!$after_el = $gtree->locate_element($moveafter)) {
            \print_error('invalidelementid');
        }

        $after = $after_el['object'];
        $sortorder = $after->get_sortorder();

        $object->set_parent($after->id);

        $object->move_after_sortorder($sortorder);

    }


}
### @end

/**
 * Prints tabs for navigating the block's pages
 *
 * Prints a tab linking to alisdata.php, and one linking to distribute.php.
 * If ALIS data hasn't been uploaded yet, the link to distribute.php will not
 * be displayed.
 *
 * @global $DB The global Database object
 * @global $OUTPUT the output renderer.
 * @param int $selected The ID of the tab to select
 */
### @export "print_tabs"
function print_tabs($selected) {
    global $DB, $OUTPUT, $CFG;

    $tabs = array();
    $tabs[] = new \tabobject(1,
        new \moodle_url('/'.$CFG->admin.'/tool/targetgrades/alisdata.php'),
            \get_string('alisdata', 'tool_targetgrades'));
    if($DB->get_records('tool_targetgrades_alisdata')) {
        $tabs[] = new \tabobject(2,
            new \moodle_url('/'.$CFG->admin.'/tool/targetgrades/distribute.php'),
                            \get_string('mtgdistribute', 'tool_targetgrades'));
    }
    $tabs[] = new \tabobject(3,
            new \moodle_url('/'.$CFG->admin.'/tool/targetgrades/settingsform.php'),
                            \get_string('settings'));
    echo $OUTPUT->heading(get_string('mtgdistribute', 'tool_targetgrades'));
    \print_tabs(array($tabs), $selected);
}
### @end

/**
 * A skeleton grade record
 */
### @export "item_grade"
abstract class item_grade {

    public   $courseid;
    public   $categoryid;
    public   $gradetype;
    public   $grademax;
    public   $grademin;
    public   $hidden;
    public   $itemnumber;
    public   $itemname;
    public   $locked;
    public   $idnumber;

    /**
     * Define common attributes for the subclasses
     *
     * @param int $courseid The course the grade item belongs to
     * @param int $categoryid The category the grade item belongs to
     */
    protected function  __construct($courseid, $categoryid) {
        $this->courseid = $courseid;
        $this->categoryid = $categoryid;
        $this->hidden = 0;
        $this->grademin = 0;
        $this->idnumber = '';
        $this->timecreated = \time();
        $this->timemodified = \time();
    }
}
### @end

/**
 * A course gradeitem
 */
class item_course extends item_grade {
    public $timecreated;
    public $timemodified;
    public $sortorder;

    /**
     * Sets all the attributes to those of a course grade item
     *
     * @param int $courseid The course the grade item belongs to
     * @param int $categoryid The category the grade item belongs to
     */
    public function  __construct($courseid, $categoryid) {
        $this->courseid = $courseid;
        $this->instanceid = $categoryid;
        $this->itemtype = 'course';
        $this->locked = 0;
        $this->hidden = 0;
        $this->gradetype = GRADE_TYPE_SCALE;
        $this->grademax = 0;
        $this->grademin = 0;
        $this->sortorder = 1;
    }
}

/**
 * An average GCSE grade item
 */
class item_avgcse extends item_grade {
    public $decimals = 2;

    /**
     * Sets all the attributes to those of an Average GCSE item
     *
     * @param int $courseid The course the grade item belongs to
     * @param int $categoryid The category the grade item belongs to
     */
    public function __construct($courseid, $categoryid) {
        parent::__construct($courseid, $categoryid);
        $this->itemname = \get_string('item_avgcse', 'tool_targetgrades');
        $this->gradetype = GRADE_TYPE_VALUE;
        $this->grademax = 10;
        $this->grademin = 0;
        $this->locked = \time();
        $this->idnumber = GRADE_ITEM_PREFIX.'avgcse';
        $this->itemnumber = 1;

    }

}

/**
 * An ALIS number grade item
 */
class item_alisnum extends item_grade {

    public $decimals;

    /**
     * Sets all the attributes to those of an ALIS number item
     *
     * @param int $courseid The course the grade item belongs to
     * @param int $categoryid The category the grade item belongs to
     */
    public function __construct($courseid, $categoryid) {
        parent::__construct($courseid, $categoryid);
        $this->itemname = \get_string('item_alisnum', 'tool_targetgrades');
        $this->gradetype = GRADE_TYPE_VALUE;
        $this->grademax = 360;
        $this->grademin = 0;
        $this->decimals = 0;
        $this->hidden = 1;
        $this->locked = \time();
        $this->idnumber = GRADE_ITEM_PREFIX.'alisnum';
        $this->itemnumber = 2;
    }

}

/**
 * An ALIS grade item
 */
class item_min extends item_grade {

    public   $scaleid;

    /**
     * Sets the attributes to those of an ALIS grade item
     *
     * @param int $courseid The course the grade item belongs to
     * @param int $categoryid The category the grade item belongs to
     */
    public function __construct($courseid, $categoryid) {
        parent::__construct($courseid, $categoryid);
        $this->itemname = \get_string('item_alis', 'tool_targetgrades');
        $this->hidden = 1;
        $this->locked = \time();
        $this->gradetype = GRADE_TYPE_SCALE;
        $this->grademax = 0;
        $this->scaleid = 0;
        $this->idnumber = GRADE_ITEM_PREFIX.'min';
        $this->itemnumber = 3;
    }

    /**
     * Sets the grade scale based on the qualtype, or the default if none is set
     *
     * @param string $qualtype Must == one of the qualtype constants
     * @param int $default The ID of the default scale to use
     */
    public function set_scale($qualtype, $default = null) {
        global $DB;
        $scale = $DB->get_record('scale', array('name' => $qualtype.' MTG'));
        if(!$scale) {
            if(!empty($default)) {
                $scale = $DB->get_record('scale', array('id' => $default));
            } else {
                throw new \Exception($qualtype);
            }
        }
        $this->gradetype = 2;
        $this->grademax = \count($scale->scale);
        $this->scaleid = $scale->id;
    }
}

/**
 * A Minimum Target Grade item
 */
class item_target extends item_min {

    /**
     * Sets the attributes to those of an MTG grade item
     *
     * @param int $courseid The course the grade item belongs to
     * @param int $categoryid The category the grade item belongs to
     */
    public function __construct($courseid, $categoryid) {
        parent::__construct($courseid, $categoryid);
        $this->itemname = \get_string('item_mtg', 'tool_targetgrades');
        $this->hidden = 0;
        $this->locked = 0;
        $this->idnumber = GRADE_ITEM_PREFIX.'target';
        $this->itemnumber = 4;
    }
}

/**
 * A Minimum Target Grade item
 */
class item_cpg extends item_min {

    /**
     * Sets the attributes to those of an MTG grade item
     *
     * @param int $courseid The course the grade item belongs to
     * @param int $categoryid The category the grade item belongs to
     */
    public function __construct($courseid, $categoryid) {
        parent::__construct($courseid, $categoryid);
        $this->itemname = \get_string('item_cpg', 'tool_targetgrades');
        $this->hidden = 0;
        $this->locked = 0;
        $this->idnumber = GRADE_ITEM_PREFIX.'cpg';
        $this->itemnumber = 5;
    }
}

/**
 * Validates and processes files for the tutorlink block
 */
### @export "csvhandler"
class csvhandler {
### @end
    /**
     * The ID of the file uploaded through the form
     *
     * @var string
     */
    private $filename;

    /**
     * Constructor, sets the filename
     *
     * @param string $filename
     */
    ### @export "csvhandler_construct"
    public function __construct($filename) {
        $this->filename = $filename;
    }
    ### @end

    /**
     * Attempts to open the file
     *
     * Open the file using the File API.
     * Return the file handler.
     *
     * @throws moodle_exception if the file cant be opened for reading
     * @global object $USER
     * @return object File handler
     */
    ### @export "csvhandler_openfile"
    private function open_file() {
        global $USER;

        $fs = \get_file_storage();
        $context = \get_context_instance(CONTEXT_USER, $USER->id);
        if (!$files = $fs->get_area_files($context->id, 'user', 'draft', $this->filename, 'id DESC', false)) {
            throw new \moodle_exception('cantreadcsv', 'tool_targetgrades');
        }
        $file = \reset($files);
        if (!$file = $file->get_content_file_handle()) {
            throw new \moodle_exception('cantreadcsv', 'tool_targetgrades');
        }

        return $file;
    }
    ### @end


    /**
     * Checks that the file is valid CSV in the expected format
     *
     * Opens the file, then checks each row contains 3 either 1 or 6 comma-separated values
     *
     * @see open_file()
     * @throws moodle_exeption if there are the wrong number of columns
     * @return true on success
     */
    ### @export "csvhandler_validate"
    public function validate() {
        $line = 0;
        $file = $this->open_file();
        while ($csvrow = \fgetcsv($file, 0, '|')) {
            $line++;
            if (count($csvrow) != 1 && count($csvrow) != 6) {
                throw new \moodle_exception('wrongcolcsv', 'tool_targetgrades', '', $line);
            }
        }
        \fclose($file);
        return true;
    }
    ### @end

    /**
     * Processes the file to import the ALIS data
     *
     * Opens the file, loops through each row. Cleans the values in each column,
     * and inserts or updates the statistics for each subject, then loops over
     * the records in the table and flags any quality issues.
     *
     * Returns a report of successess and failures.
     *
     * @see open_file()
     * @global object $DB Database interface
     * @return string A report of successes and failures.
     */
    ### @export "csvhandler_process"
    public function process() {
        global $DB;

        $file = $this->open_file();
        $qualtype = false;
        $import->qualcount = 0;
        $import->subjectcount = 0;
        $import->updatecount = 0;
        while ($line = \fgetcsv($file, 0, '|')) {
            ### @export "csvhandler_process_heading"

            // If there's only one column on this line, then it's a qualification heading
            if (\count($line) == 1) {
                $qualname = \clean_param($line[0], \PARAM_TEXT);
                // Create a new qualtype record if there isn't one already.
                $where = $DB->sql_compare_text('name').' = ?';
                $params = array($qualname);
                if(!$qualtype = $DB->get_record_select('tool_targetgrades_qualtype', $where, $params)) {
                    if(!$qualscale = $DB->get_record('scale', array('name' => $qualname.' MTG'))) {

                        if($scale = get_scale($qualname)) {
                            $qualscale = new \stdClass;
                            $qualscale->name = $qualname.' MTG';
                            $qualscale->scale = $scale;
                            $qualscale->description = $qualname.' Minimum/Target Grades';
                            $qualscale->id = $DB->insert_record('scale', $qualscale);
                        }

                    }

                    if($qualscale) {
                        $qualtype = new \stdClass;
                        $qualtype->name = $qualname;
                        $qualtype->scaleid = $qualscale->id;
                        $qualtype->id = $DB->insert_record('tool_targetgrades_qualtype', $qualtype);
                        $import->qualcount++;
                    }
                }
            ### @export "csvhandler_process_qualtype"
            } else {
                // If we have a record for this course's qualtype
                if ($qualtype) {
                    $name = \clean_param($line[0], \PARAM_TEXT);
                    $samplesize = \clean_param(str_replace(',', '', $line[1]), \PARAM_INT);
                    $gradient = \clean_param($line[2], \PARAM_FLOAT);
                    $intercept = \clean_param($line[3], \PARAM_FLOAT);
                    $correlation = \clean_param($line[4], \PARAM_FLOAT);
                    $standarddeviation = \clean_param($line[5], \PARAM_FLOAT);
                    $where = $DB->sql_compare_text('name').' = ? AND qualtypeid = ?';
                    $params = array($name, $qualtype->id);
                    if($subject = $DB->get_record_select('tool_targetgrades_alisdata', $where, $params)) {
                        $subject->samplesize = $samplesize;
                        $subject->gradient = $gradient;
                        $subject->intercept = $intercept;
                        $subject->correlation = $correlation;
                        $subject->standarddeviation = $standarddeviation;
                        $DB->update_record('tool_targetgrades_alisdata', $subject);
                        $import->updatecount++;
                    } else {
                        $subject = new \stdClass;
                        $subject->name= $name;
                        $subject->samplesize = $samplesize;
                        $subject->gradient = $gradient;
                        $subject->intercept = $intercept;
                        $subject->correlation = $correlation;
                        $subject->standarddeviation = $standarddeviation;
                        $subject->qualtypeid = $qualtype->id;
                        $DB->insert_record('tool_targetgrades_alisdata', $subject);
                        $import->subjectcount++;
                    }
                }
            }
        }

        \fclose($file);

        // All the stats are now in the DB, so do a pass over the table to flag up any quality issues with the data
        ### @export "csvhandler_process_quality"
        $averagesize = round($DB->get_record_sql('SELECT AVG(samplesize) as avg FROM {tool_targetgrades_alisdata}')->avg);
        $select = 'SELECT ta.*, tq.name as qualification ';
        $from = 'FROM {tool_targetgrades_alisdata} ta
            JOIN {tool_targetgrades_qualtype} tq ON ta.qualtypeid = tq.id';
        $alisdata = $DB->get_records_sql($select.$from);

        foreach ($alisdata as $alis) {
            ### @export "csvhandler_process_samplesize"
            if ($alis->samplesize < $averagesize) {
                if ($alis->samplesize < $averagesize/2) {
                    if ($alis->samplesize < $averagesize/4) {
                        $alis->quality_samplesize = 3;
                    } else {
                        $alis->quality_samplesize = 2;
                    }
                } else {
                    $alis->quality_samplesize = 1;
                }
            } else {
                $alis->quality_samplesize = 0;
            }

            ### @export "csvhandler_process_correlation"
            if ($alis->correlation < CORRELATION_THRESHOLD) {
                $alis->quality_correlation = 1;
            } else {
                $alis->quality_correlation = 0;
            }

            ### @export "csvhandler_process_deviation"
            switch ($alis->qualification) {
                case ALIS_GCSE:
                case ALIS_BTEC_FIRST_DIPLOMA:
                case ALIS_IB_STANDARD:
                case ALIS_IB_HIGHER:
                case ALIS_OCR_NATIONAL_CERTIFICATE:
                case ALIS_OCR_NATIONAL_DIPLOMA:
                    $boundary = 1;
                    break;

                case ALIS_ADVANCED_GCE:
                case ALIS_ADVANCED_GCE_DOUBLE:
                    $boundary = 20;
                    break;

                case ALIS_ADVANCED_SUBSIDIARY_GCE:
                case ALIS_ADVANCED_SUBSIDIARY_GCE_DOUBLE:
                    $boundary = 10;
                    break;

                case ALIS_BTEC_NATIONAL_AWARD:
                case ALIS_BTEC_NATIONAL_DIPLOMA:
                case ALIS_BTEC_NATIONAL_CERTIFICATE:
                    $boundary = 40;
                    break;

                case ALIS_CACHE_L3_DIPLOMA:
                    $boundary = 60;
                    break;
            }

            if ($alis->standarddeviation > $boundary) {
                if ($alis->standarddeviation > $boundary*2) {
                    $alis->quality_deviation = 2;
                } else {
                    $alis->quality_deviation = 1;
                }
            } else {
                $alis->quality_deviation = 0;
            }

            ### @export "csvhandler_process_end"
            $DB->update_record('tool_targetgrades_alisdata', $alis);
        }

        return $import;
    }
### @end
}


// We need these class definitions here so that we can get all the functions we need when doing
// AJAX searching in the big select list. However, we don't want to have \user_selector_base
// as a dependency on every page where we include this lib, so only define the select list classes
// on pages where the user selector lib has already been included.
if (class_exists('\user_selector_base')) {

    /**
     * Select list for courses without Target Grade items
     */
    ### @export "pcs"
    class potential_course_selector extends \user_selector_base {
    ### @end

        /**
         * Add the file name to the $options array to make AJAX searching work
         * @return array
         */
        ### @export "pcs_get_options"
        protected function get_options() {
            global $CFG;
            $options = parent::get_options();
            $options['file'] = $CFG->admin.'/tool/targetgrades/lib.php';
            return $options;
        }
        ### @end

        /**
         * Get list of courses for potential distribution
         *
         * Get all the courses that are in the categories selected in $config->categories,
         * and aren't filtered out by $config->exclude_regex, and don't already have the grade
         * items.  This function uses odd names for the fields to avoid having to override
         * additional methods from the parent class.
         *
         * @param $search Optional string to search for in shortname and fullname
         * @return array Matching course records
         */
        ### @export "pcs_find_users"
        public function find_users($search) {
            global $DB;
            $config = get_config();

            ### @export "pcs_find_users_categories"
            list($in_sql, $params) = $DB->get_in_or_equal($config->categories);

            ### @export "pcs_find_users_group"
            $select = 'SELECT c.id, c.shortname AS lastname, "" AS firstname, c.fullname AS email, q.name AS qualtype, ';
            if(!empty($config->group_field) && !empty($config->group_length)) {
                $args = array($config->group_field, $config->group_length);
                $select .= \vsprintf('LEFT(c.%1$s, %2$d) AS pattern ', $args);
                $from = \vsprintf('FROM {course} c
                            LEFT JOIN {tool_targetgrades_patterns} p
                                ON LEFT(c.%1$s, %2$d) = CAST(p.pattern AS CHAR)
                            LEFT JOIN {tool_targetgrades_alisdata} d ON p.alisdataid = d.id
                            LEFT JOIN {tool_targetgrades_qualtype} q ON d.qualtypeid = q.id ', $args);
            } else {
                $select .= 'shortname AS pattern ';
                $from = 'FROM {course} c
                            LEFT JOIN {tool_targetgrades_patterns} p ON c.shortname = p.pattern
                            LEFT JOIN {tool_targetgrades_alisdata} d ON p.alisdataid = d.id
                            LEFT JOIN {tool_targetgrades_qualtype} q ON d.qualtypeid = q.id ';
            }

            $order = 'ORDER BY pattern';

            ### @export "pcs_find_users_regex"
            if ($DB->sql_regex_supported()) {
                $where = 'WHERE category '.$in_sql.' AND '.$config->exclude_field.' '.$DB->sql_regex(false).' ? ';
                $params = array_merge($params, array($config->exclude_regex));
            } else {
                $courses = $DB->get_records_select('course', 'category '.$in_sql, $params);

                \array_filter($courses, function($course, $config) {
                    $field = $config->exclude_field;
                    return !\preg_match('/'.$config->exclude_regex.'/', $course->$field);
                });

                list($in_sql, $params) = $DB->get_in_or_equal(array_keys($courses));
                $where = 'WHERE id '.$in_sql.' ';
            }

            ### @export "pcs_find_users_search"
            $optgroupname = get_string('courseswithoutgrades', 'tool_targetgrades');

            if (!empty($search)) {
                $shortnamelike = $DB->sql_like('shortname', '?');
                $fullnamelike = $DB->sql_like('fullname', '?');
                $where .= 'AND ('.$shortnamelike.' OR '.$fullnamelike.') ';
                $params = array_merge($params, array('%'.$search.'%', '%'.$search.'%'));
                $optgroupname .= ' - '.get_string('searchresults', 'tool_targetgrades');
            }

            ### @export "pcs_find_users_exclude"
            if (!empty($this->exclude)) {
                list($not_in_sql, $not_in_params) = $DB->get_in_or_equal($this->exclude, SQL_PARAMS_QM, '', false);
                $where .= 'AND c.id '.$not_in_sql.' ';
                $params = array_merge($params, $not_in_params);
            }

            ### @export "pcs_find_users_end"
            $options = $DB->get_records_sql($select.$from.$where.$order, $params);

            $options = hasconfig($options);

            return array($optgroupname => $options);
        }
        ### @end

    };

    /**
     * Select list for courses with target grade items.
     */
    ### @export "dcs"
    class distributed_course_selector extends potential_course_selector {
    ### @end

        /**
         * Get a list of courses that have Target Grade grade items on.
         *
         * Gets all the courses with one or more grade items match the ones
         * added by the distribution script.
         *
         * @param $search Compulsory arugment due to abstract parent method. Defaults to empty string, doesn't do anything
         * @return array All the matching courses
         */
        ### @export "dcs_find_users"
        function find_users($search = null) {
            global $DB;
            $config = get_config();

            ### @export "dcs_find_users_categories"
            list($insql, $inparams) = $DB->get_in_or_equal($config->categories);

            ### @export "dcs_find_users_fields"
            $select = 'SELECT DISTINCT c.id, c.shortname AS lastname,
                        "" AS firstname, c.fullname AS email, q.name AS qualtype, ';

            $from = 'FROM {course} c
                        JOIN {grade_items} g ON c.id = g.courseid ';

            ### @export "dcs_find_users_group"
            if(!empty($config->group_field) && !empty($config->group_length)) {
                $args = array($config->group_field, $config->group_length);
                $select .= \vsprintf('LEFT(c.%1$s, %2$d) AS pattern ', $args);
                $from .= \vsprintf('LEFT JOIN {tool_targetgrades_patterns} p
                                        ON LEFT(c.%1$s, %2$d) = CAST(p.pattern AS CHAR)
                                    LEFT JOIN {tool_targetgrades_alisdata} d ON p.alisdataid = d.id
                                    LEFT JOIN {tool_targetgrades_qualtype} q ON d.qualtypeid = q.id ', $args);
            } else {
                $select .= 'shortname AS pattern ';
                $from .= 'LEFT JOIN {tool_targetgrades_patterns} p ON c.shortname = p.pattern
                            LEFT JOIN {tool_targetgrades_alisdata} d ON p.alisdataid = d.id
                            LEFT JOIN {tool_targetgrades_qualtype} q ON d.qualtypeid = q.id ';
            }

            ### @export "dcs_find_users_regex"
            if ($DB->sql_regex_supported()) {
                $where = 'WHERE category '.$insql.' AND '.$config->exclude_field.' '.$DB->sql_regex(false).' ? ';
                $params = array_merge($inparams, array($config->exclude_regex));
            } else {
                $courses = $DB->get_records_select('course', 'category '.$insql, $inparams);

                \array_filter($courses, function($course, $config) {
                    $field = $config->exclude_field;
                    return !\preg_match('/'.$config->exclude_regex.'/', $course->$field);
                });

                list($insql, $inparams) = $DB->get_in_or_equal(array_keys($courses));
                $where = 'WHERE id '.$insql.' ';
                $params = $inparams;
            }

            ### @export "dcs_find_users_items"
            $itemnames = array(get_string('item_avgcse', 'tool_targetgrades'),
            get_string('item_alisnum', 'tool_targetgrades'),
            get_string('item_alis', 'tool_targetgrades'),
            get_string('item_mtg', 'tool_targetgrades'));
            list($itemsql, $itemparams) = $DB->get_in_or_equal($itemnames);

            $where .= 'AND itemname '.$itemsql;
            $params = array_merge($params, $itemparams);
            $options = $DB->get_records_sql($select.$from.$where, $params);

            ### @export "dcs_find_users_end"
            $options = hasconfig($options);
            return array(get_string('courseswithgrades', 'tool_targetgrades') => $options);
        }
        ### @end
   };

}
/**
 * Used to flag up when a class has no students
 */
### @export "e_nostudents"
class no_students_exception extends \Exception {}
### @end

/**
 * Used to flag up when a student has no Average GCSE data
 */
### @export "e_nodataforstudent"
class no_data_for_student_exception extends \Exception {}
### @end

/**
 * Used to flag when a student's MTG calcucation failed for some reason
 */
### @export "e_nomtgforstudent"
class no_mtg_for_student_exception extends \Exception {}
### @end

/**
 * Used to flag when a course has no ALIS data configured
 */
### @export "e_noconfigforcourse"
class no_config_for_course_exception extends \Exception {}
### @end

/**
 * Used to return the ID of a grade item if one already exists for the given
 * criteria.
 */
### @export "e_gradeitemexists"
class grade_item_exists_exception extends \Exception {
    private $id;

    public function __construct($message = "", $id = 0, $code = 0) {
        parent::__construct($message, $code);
        $this->id = $id;
    }

    public function getId() {
        return $this->id;
    }
}
### @end

/**
 * Used to flag when an regex with a risk of ReDOS is detected
 */
### @export "e_unsaferegex"
class unsafe_regex_exception extends \Exception {
    public function __construct() {
        parent::__construct('unsaferegex');
    }
}
### @end

### @export "e_needsconfig"
class needsconfig_exception extends \Exception {}
### @end

?>
