<?php

defined('MOODLE_INTERNAL') || die;

function xmldb_tool_targetgrades_install() {
    global $CFG, $DB;

    if (!file_exists($CFG->dirroot.'/report/targetgrades')) {
        $config = get_config('report_targetgrades');
        foreach ((array)$config as $name => $value) {
            set_config($name, $value, 'tool_targetgrades');
        }
        unset_all_config_for_plugin('report_targetgrades');
        capabilities_cleanup('report_targetgrades');

        $tables = array('alisdata', 'qualtype', 'patterns');
        $dbman = $DB->get_manager();
        foreach ($tables as $tablename) {
            $table = new xmldb_table('report_targetgrades_'.$tablename);
            if ($dbman->table_exists($table)) {
                $records = $DB->get_records('report_targetgrades_'.$tablename);
                foreach ($records as $record) {
                    $DB->insert_record_raw('tool_targetgrades_'.$tablename, $record, false, false, true);
                }
                $dbman->drop_table($table);
            }
        }
    }
}
