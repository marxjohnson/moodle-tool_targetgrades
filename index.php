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
 * Determines whether the plugin is configured and directs the user to the appropriate page
 *
 * If the tool is yet to be configured, the user is directed to the settings page.
 * If the tool has been configured but there is no ALIS data, the user is directed to the
 * ALIS upload page.
 * If ALIS data is present, the user is directed to the Distribute page.
 *
 * @package tool
 * @subpackage targetgrades
 * @author      Mark Johnson <mark.johnson@tauntons.ac.uk>
 * @copyright   2011 Tauntons College, UK
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../../config.php');
require_once($CFG->dirroot.'/'.$CFG->admin.'/tool/targetgrades/lib.php');

### @export "alias"
use tool\targetgrades as tg;
### @end

require_login($SITE);

### @export "get_config"
$config = tg\get_config();
### @end


try {
    if(!empty($config->roles) && !empty($config->categories)) {
        if(!empty($config->gcse_field) && !empty($roles) && !empty($categories)) {
            if(!$DB->get_records('tool_targetgrades_alisdata')) {
                redirect(new moodle_url('/'.$CFG->admin.'/tool/targetgrades/alisdata.php'));
            } else {
                redirect(new moodle_url('/'.$CFG->admin.'/tool/targetgrades/distribute.php'));
            }
        } else {
            throw new tg\needsconfig_exception();
        }

    } else {
        throw new tg\needsconfig_exception();
    }
} catch (tg\needsconfig_exception $e) {
    $url = new moodle_url('/'.$CFG->admin.'/tool/targetgrades/settingsform.php', array());
    redirect($url->out(), get_string('needsconfig', 'tool_targetgrades'), 5);
}

?>
