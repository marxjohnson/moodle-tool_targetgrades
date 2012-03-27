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
 * Defines english strings for the targetgrades tool
 *
 * @package tool
 * @subpackage targetgrades
 * @author      Mark Johnson <mark.johnson@tauntons.ac.uk>
 * @copyright   2011 Tauntons College, UK
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['alisdata'] = 'Upload ALIS data';
$string['calculategrades'] = 'Calculate Target Grades';
$string['calculategrades_help'] = 'Clicking this button will add 4 grade items to each of the courses selected from the list on the right - Average GCSE Points, ALIS Points, Minimum Grade and Target Grade.

If a set of ALIS statistics has been linked to a pattern matching a course, a Minimum Grade will be calculated for each student. The raw points score will be stored in the ALIS Points item, and the grade in the Minimum Grade item. The Target Grade will be left blank, to be filled in with an individual target for that student, using the Minimum Grade as a guide.

Courses marked with a * have no statistics linked to them, so will just have grade items added.';
$string['cantreadcsv'] = 'The CSV file could not be read! Try uploading /.';
$string['categories'] = 'Categories for Distribution';
$string['categories_help'] = 'Only courses checked will be available for MTG distribution. Disable those you don\'t need to make the list more manageable';
$string['col_name'] = 'Course Name';
$string['col_pattern'] = 'Apply stats to pattern';
$string['col_gradient'] = 'Gradient';
$string['col_intercept'] = 'Intercept';
$string['col_qualtype'] = 'Qualification';
$string['col_quality'] = 'Quality';
$string['col_quality_help'] = 'This column indicates the quality of the statistics.  This should be used when considering whether Target Grades should be calculated for each subject, since low quality statistics may not produce meaningful Target Grades.

An S indicates a below average sample size. This means that the statistics were calculated from a small number of actual grades.  Without knowing how many students took each subject, it\'s hard to determine confidence from sample size, so this indicator should be considered in the context of other indicators that may be present.

A C indicates low correlation (<0.3, where 0.6-0.7 is considered strong).  This means that the actual results gathered don\'t follow the line described by the statistics to a large degree.

A D indicates a high standard deviation.  This means that, while the trend of actual grades may match the line described by the statistics, the grades themselves are generally not on or close to the line itself.

Each of these indicators should be considered in context when deciding which subjects to calculate target grades for. For example, a below average sample size doesn\'t mean statistics should be automatically disregarded. However, where there is a combination of quality issues such as a very high standard deviation and a low correlation, it\'s unlikely that grades calculated from the statistics would have any meaning.';
$string['configtitle'] = 'Configure Target Grades Report';
$string['configalis'] = 'ALIS (Advanced Level Information System) data is used to calculate minimum target grades for students based on their average GCSE score and past statistics for the relevant course.<br />
                        Latest data can be downloaded <a href="https://css.cemcentre.org/ALIS/Site/reports/default.aspx?reptype=6">here</a> in PDF format. This must be converted to CSV format using the script included with this tool before uploading.';
$string['configgradient'] = 'Gradient';
$string['configintercept'] = 'Intercept';
$string['courseswithgrades'] = 'Courses with Target Grade items';
$string['courseswithoutgrades'] = 'Courses without Target Grade items';
$string['createdgis'] = 'Grade items created';
$string['equationsfile'] = 'ALIS Equations file';
$string['equationsfiledesc'] = 'This MUST be a CSV file produced using alis_pdf2csv.sh, included with this tool';
$string['exclude_field'] = 'Exclude courses where:';
$string['exclude_regex'] = 'matches pattern:';
$string['exclude_regexdesc'] = 'Exclude Regex';
$string['exclude_regexdesc_help'] = 'Must be a valid Regular Expression. Any courses matching this pattern will be excluded, and will NOT be available for distribution.<br />Use this to make the distribution list more manageable.';
$string['explainpatterns'] = 'A list of patterns has been generated based on the crietria defined in the tool\'s settings, and the courses currently on the system. Selecting a pattern for a row in the table will apply those statistics to courses matching that pattern. Currently the pattern matches the first {$a->group_length} characters of {$a->group_field}';
$string['defaultscale'] = 'Default grade scale';
$string['defaultscale_help'] = 'This scale will be applied to grade items in all courses with no configured ALIS Data. They can be changed after distribution in each course\'s gradebook';
$string['distribute_success'] = 'Grade items distributed to {$a} courses successfully';
$string['distribute_empty'] = '{$a} courses were ignored, becuase they have no students';
$string['distribute_unconfigured'] = '{$a} courses were ignored, becuase they have no ALIS data';
$string['distribute_noavgcse'] = '{$a} students were ignored, because they have no Average GCSE score';
$string['distribute_failedcalc'] = '{$a} grade calculations failed.';
$string['err_gcsefield'] = 'You must select a field';
$string['err_nouserfields'] = 'This tool needs a User Profile Field to be defined to hold students\' Average GCSE points score. Please define one before attempting to configure the tool';
$string['err_group_length'] = 'If you define a group field you must define a group length';
$string['err_group_field'] = 'If you define a group length you must define a group field';
$string['err_exclude_field'] = 'If you define an exclude pattern you must define a field to check';
$string['gcse_field'] = 'Average GCSE field';
$string['gcse_field_help'] = 'Select the user profile field where the user\'s average GCSE score is stored (as a number from 0-8).';
$string['gradesentered'] = 'Grades Entered';
$string['group_length'] = 'Group Courses By:';
$string['group_field'] = 'characters of';
$string['group_fielddesc'] = 'Grouping field';
$string['group_fielddesc_help'] = 'These settings allow you to apply a set of ALIS statistics to all courses that match the specified pattern. For example, you may have 3 classes in each A level which share the first 5 characters of their shortname.';
$string['highdeviation'] = 'The deviation of actual results from these statistics is typically more than 1 grade boundary.  Target Grades calulated using these statistics may not be reliable';
$string['item_avgcse'] = 'Average GCSE';
$string['item_alisnum'] = 'ALIS Number';
$string['item_alis'] = 'Minimum Grade';
$string['item_cpg'] = 'Current Performance Grade';
$string['item_mtg'] = 'Target Grade';
$string['importoutput'] = 'Imported {$a->qualcount} new qualification types, and {$a->subjectcount} new subjects. Updated {$a->updatecount} subjects.';
$string['lowsize'] = 'The sample size for this subject ({$a}) is less than half of the average.';
$string['lowcorrelation'] = 'The correlation of actual results to these statistics is low ({$a}). Target Grades calculated using these statistics may not be reliable.';
$string['mtgs'] = 'Minimum Target Grades';
$string['mtgdistribute'] = 'Distribute Target Grades';
$string['needsconfig'] = 'This tool is not configured. You must select at least a gcse_field, roles and categories on the tool\'s settings page.';
$string['needsalis'] = 'You must at least import ALIS data before you can distrbute grades.';
$string['noconfig'] = 'Some ALIS data is missing - please configure the tool';
$string['noalis'] = '*No ALIS Data - grade items will be created, but no MTG calculated.';
$string['nostuds'] = 'No Students - ignored';
$string['nogrades'] = 'missing data - MTG not calculated';
$string['nogradescale'] = 'No grade scale was found for {$a}, and no default scale has been specified.';
$string['distributegrades'] = 'Distribute Target Grades';
$string['noperms'] = 'You don\'t have permission to use this function!';
$string['okquality'] = 'These statistics are of sufficient quality to calculate a Target Grade';
$string['oksize'] = 'The sample size for this subject ({$a}) is below average. However, these stats should still be OK for calulating Target Grades.';
$string['pluginname'] = 'Target Grades';
$string['settingssaved'] = 'Settings Saved';
$string['unsaferegex'] = 'The exclusion pattern you entered in the tool\'s settings is unsafe and may overload the server. Matching it will not be attempted until you have edited it. Please see http://www.regular-expressions.info/catastrophic.html for more details.';
$string['uploadalis'] = 'Upload ALIS data';
$string['recalculate'] = 'Recalculate existing grades';
$string['recalculate_help'] = 'Clicking this button will recalulate ALIS points and Minimum Grades for all courses on the left hand list (those that already have the grade items).

For example, if you\'ve changed the statistics associated with a pattern, this will replace the grades with those calculated using the new statistics.';
$string['roles'] = 'Use Roles';
$string['roles_help'] = 'The tool will attempt to distribute to users with these roles on each selected course.';
$string['saveandadd'] = 'Save changes and Add another pattern';
$string['searchresults'] = 'Search Results';
$string['settingssaved'] = 'Settings Saved';
$string['wrongcolcsv'] = 'The CSV file has the wrong number of columns on line {$a} - It should either have 1 column for a subject type heading, or 6 for a set of statistics.';
$string['vlowsize'] = 'The sample size for these statisics ({$a}) is less than a quarter of the average.';
$string['vhighdeviation'] = 'Results typically deviate from these statistics by more than 2 grade boundaries.  Target Grades calulated using these statistics are unlikely to be reliable.';

?>
