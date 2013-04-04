#!/bin/bash
echo "### @export \"alisdata\""
echo "DESCRIBE mdl_report_targetgrades_alisdata" | mysql --table -u moodletest -pj66sRI7 moodle2 
echo "### @export \"qualtypes\""
echo "DESCRIBE mdl_report_targetgrades_patterns" | mysql --table -u moodletest -pj66sRI7 moodle2 
echo "### @export \"patterns\""
echo "DESCRIBE mdl_report_targetgrades_patterns" | mysql --table -u moodletest -pj66sRI7 moodle2 
