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
 * Import ALIS statistics for target grade calculations
 *
 * Presents a form allowing a CSV file containing ALIS data (generated using
 * alis_pdf2csv.sh) to be uploaded. Once uploaded, a list of statistics is
 * displayed, allowing patterns to be defined for each set of statistics to be
 * applied to when grades are distributed.
 *
 * @package tool
 * @subpackage targetgrades
 * @author      Mark Johnson <mark.johnson@tauntons.ac.uk>
 * @copyright   2011 Tauntons College, UK
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../../config.php');
require_once($CFG->dirroot.'/'.$CFG->admin.'/tool/targetgrades/alisdata_form.php');
require_once($CFG->dirroot.'/'.$CFG->admin.'/tool/targetgrades/lib.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/tablelib.php');

use tool\targetgrades as tg;

require_login($SITE);
admin_externalpage_setup('tooltargetgrades', null, null, '/'.$CFG->admin.'/tool/targetgrades/alisdata.php');
$PAGE->navbar->add(get_string('alisdata', 'tool_targetgrades'));

$renderer = $PAGE->get_renderer('tool_targetgrades');

$savepatterns = optional_param('savepatterns', false, PARAM_BOOL);
$alispatterns = optional_param('alispatterns', array(), PARAM_CLEAN);
$addpattern = optional_param('addpattern', array(), PARAM_CLEAN);
$config = tg\get_config('tool_targetgrades');
$addfield = optional_param('addfield', null, PARAM_INT);
### @export 'pattern_process'
if ($savepatterns || !empty($addpattern)) {
    foreach ($alispatterns as $alisid => $patterns) {
        foreach ($patterns as $id => $pattern) {
            if ($alisdata = $DB->get_record('tool_targetgrades_alisdata', array('id' => $alisid))) {
                if ($patternrecord = $DB->get_record('tool_targetgrades_patterns', array('id' => $id))) {
                    if (empty($pattern)) {
                        $DB->delete_records('tool_targetgrades_patterns', array('id' => $id));
                    } else {
                        $patternrecord->pattern = $pattern;
                        $DB->update_record('tool_targetgrades_patterns', $patternrecord);
                    }
                } else if (!empty($pattern)) {
                    $patternrecord = new stdClass;
                    $patternrecord->alisdataid = $alisid;
                    $patternrecord->pattern = $pattern;
                    $patternrecord->id = $DB->insert_record('tool_targetgrades_patterns', $patternrecord);
                }
            }
        }
    }
    $output = '<p>'.get_string('changessaved').'</p>';
    if (!empty($addpattern)) {
        $params = array('addfield' => key($addpattern));
        redirect(new moodle_url('/admin/tool/targetgrades/alisdata.php#alis'.key($addpattern), $params), '', 0);
    }
}

### @export 'uploadform'
$uploadform = new alisdata_upload_form();
$uploaddata = $uploadform->get_data();

if ($uploaddata) {
    $handler = new tg\csvhandler($uploaddata->equationsfile);
    $handler->validate();
    $import = $handler->process();

    $output = '<p>'.get_string('importoutput', 'tool_targetgrades', $import).'</p>';

}

### @export 'query'
$select = 'SELECT a.*, a.name AS subject, q.name AS qualification ';
$from = 'FROM {tool_targetgrades_alisdata} a
    JOIN {tool_targetgrades_qualtype} q ON a.qualtypeid = q.id ';
$order = 'ORDER BY q.name, a.name ASC';
$alis_data = $DB->get_recordset_sql($select.$from.$order);

### @export 'table_patterns'
try {
    $options = tg\build_pattern_options();
} catch (unsafe_regex_exception $e) {
    print_error($e->getMessage(), 'tool_targetgrades');
}

### @export 'output'
echo $OUTPUT->header();
tg\print_tabs(1);

echo html_writer::tag('h2', get_string('alisdata', 'tool_targetgrades'));
echo html_writer::tag('p', get_string('configalis', 'tool_targetgrades'));
if (isset($output)) {
    echo $output;
}
$uploadform->display();

### @export 'table'
if ($alis_data->valid()) {

    $table = new flexible_table('alisdata');

    $table->define_columns(array('qualtype', 'name', 'pattern', 'gradient', 'intercept', 'quality'));
    $helpicon = $OUTPUT->help_icon('col_quality', 'tool_targetgrades');
    $table->define_headers(array(get_string('col_qualtype', 'tool_targetgrades'),
        get_string('col_name', 'tool_targetgrades'),
        get_string('col_pattern', 'tool_targetgrades'),
        get_string('col_gradient', 'tool_targetgrades'),
        get_string('col_intercept', 'tool_targetgrades'),
        get_string('col_quality', 'tool_targetgrades').$helpicon));
    $table->define_baseurl($PAGE->url);
    $table->setup();


    $PAGE->requires->js_init_call('M.tool_targetgrades.init_datalist');

    echo html_writer::tag('p', get_string('explainpatterns', 'tool_targetgrades', $config));
    echo html_writer::start_tag('form', array('action' => $PAGE->url->out(), 'method' => 'post'));


### @export 'table_loop'
    foreach ($alis_data as $alis) {
        $form = '';
### @export 'table_patternselector'
        $patterns = $DB->get_recordset('tool_targetgrades_patterns', array('alisdataid' => $alis->id));
        if ($patterns->valid()) {
            $break = 1;
            foreach ($patterns as $pattern) {
                $optionswithpattern = array_merge($options, array($pattern->pattern => $pattern->pattern));
                asort($optionswithpattern);
                $selectname = 'alispatterns['.$alis->id.']['.$pattern->id.']';
                $form .= $renderer->datalist($optionswithpattern, $selectname, $pattern->pattern);
                if ((count($patterns) > 1 && $break < count($patterns)) || $addfield == $alis->id) {
                    $form .= html_writer::empty_tag('br');
                }
                $break++;
            }
            if ($addfield == $alis->id) {
                $form .= $renderer->datalist($options, 'alispatterns['.$alis->id.'][]');
            }
        } else {
            $form .= $renderer->datalist($options, 'alispatterns['.$alis->id.'][]');
        }
        $patterns->close();

        $attrs = array('type' => 'submit',
                        'value' => '+',
                        'name' => 'addpattern['.$alis->id.']',
                        'title' => get_string('saveandadd', 'tool_targetgrades'));
        $form .= html_writer::empty_tag('input', $attrs);

### @export 'table_quality'
        $quality = array();
        $quality_samplesize = (object)array('field' => 'samplesize', 'display' => 'S');
        switch ($alis->quality_samplesize) {
            case 1:
                $quality_samplesize->class = 'ok';
                $quality_samplesize->message = 'oksize';
                $quality[] = $quality_samplesize;
                break;
            case 2:
                $quality_samplesize->class = 'low';
                $quality_samplesize->message = 'lowsize';
                $quality[] = $quality_samplesize;
                break;
            case 3:
                $quality_samplesize->class = 'vlow';
                $quality_samplesize->message = 'vlowsize';
                $quality[] = $quality_samplesize;
                break;
        }

        if ($alis->quality_correlation) {
                $quality[] = (object)array('field' => 'correlation',
                                            'message' => 'lowcorrelation',
                                            'class' => 'low',
                                            'display' => 'C');
        }

        $quality_deviation = (object)array('field' => 'standarddeviation', 'display' => 'D');
        switch ($alis->quality_deviation) {
            case 1:
                $quality_deviation->class = 'low';
                $quality_deviation->message = 'highdeviation';
                $quality[] = $quality_deviation;
                break;
            case 2:
                $quality_deviation->class = 'vlow';
                $quality_deviation->message = 'vhighdeviation';
                $quality[] = $quality_deviation;
                break;
        }

        $quality_html = array();
        if (!empty($quality)) {
            foreach($quality as $status) {
                $field = $status->field;
                $class = 'tg_'.$status->class.'quality';
                $title = get_string($status->message, 'tool_targetgrades', $alis->$field);
                $quality_html[] = html_writer::tag('abbr', $status->display, array('class' => $class, 'title' => $title));
            }
        } else {
            $src = $OUTPUT->pix_url('i/tick_green_big');
            $title = get_string('okquality', 'tool_targetgrades');
            $quality_html[] = html_writer::empty_tag('img', array('src' => $src, 'title' => $title));
        }

### @export 'table_row'
        $row = array();
        $row[] = $alis->qualification;
        $row[] = html_writer::tag('a', $alis->subject, array('name' => 'alis'.$alis->id));

        $row[] = $form;
        $row[] = $alis->gradient;
        $row[] = $alis->intercept;
        $row[] = implode('', $quality_html);
        $table->add_data($row);
        unset($row);
    }


### @export 'table_end'
    $alis_data->close();

    $table->finish_output();
    $attrs = array('type' => 'submit', 'name' => 'savepatterns', 'value' => get_string('savechanges'));
    echo html_writer::empty_tag('input', $attrs);
    echo html_writer::end_tag('form');
}

echo $OUTPUT->footer();
### @end
?>
