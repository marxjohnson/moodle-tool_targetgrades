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
 * Defines capabilities for the targetgrades tool
 *
 * @package		tool
 * @subpackage	targetgrades
 * @author      Mark Johnson <mark.johnson@tauntons.ac.uk>
 * @copyright   2011 Tauntons College, UK
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


$capabilities = array(

    'tool/targetgrades:view' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'legacy' => array(
            'manager' => CAP_ALLOW
        )
    ),

    'tool/targetgrades:distribute' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'legacy' => array(
            'manager' => CAP_ALLOW
        )
    )

);

?>
