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
 * Modify the definition of an interval.
 *
 * @package    tool_taskattestoodle
 * @copyright  2019 marc.leconte@univ-lemans.fr
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Main configuration importation (instanciate the $CFG global variable).
require_once(dirname(__FILE__) . '/../../../config.php');
require_once(dirname(__FILE__) . '/lib.php');
require_once(dirname(__FILE__) . "/classes/training_interval_form.php");

use tool_taskattestoodle\forms\training_interval_form;
use tool_attestoodle\factories\trainings_factory;


$id = required_param('id', PARAM_INT);
$reinit = optional_param('reinit', 0, PARAM_INT);

$context = context_system::instance();
require_login();
global $DB, $USER;
$record = $DB->get_record('tool_taskattestoodle', array('id' => $id));

$PAGE->set_context($context);
$PAGE->navbar->ignore_active();
$navlevel1 = get_string('navlevel1', 'tool_attestoodle');
$PAGE->navbar->add($navlevel1, new moodle_url('/admin/tool/attestoodle/index.php', array()));
$navlevel2 = get_string('titleplanning2', 'tool_taskattestoodle');
$PAGE->navbar->add($navlevel2, new moodle_url('/admin/tool/taskattestoodle/listinterval.php',
        array('trainingid' => $record->trainingid)));

$title = get_string('titleplanning3', 'tool_taskattestoodle');
$url = new \moodle_url('/admin/tool/taskattestoodle/interval.php', array('id' => $id));
$PAGE->navbar->add($title, $url);

$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_url($url);
$mform = new training_interval_form($url);

if ($fromform = $mform->get_data()) {
    // Process validated data.
    if (isset($fromform->cancel)) {
        $redirecturl = new \moodle_url('/admin/tool/taskattestoodle/listinterval.php', array('trainingid' => $record->trainingid));
        redirect($redirecturl);
        return;
    }
    // Modify interval.
    $dataobject = new \stdClass();
    $dataobject->id = $id;
    $dataobject->trainingid = $record->trainingid;
    $dataobject->executiondate = $fromform->executiondate;
    $dataobject->beginperiod = $fromform->startdate;
    $dataobject->endperiod = $fromform->enddate;
    $dataobject->auto = $fromform->auto;
    $dataobject->mailto = $fromform->email;
    $dataobject->operatorid = $USER->id;
    $dataobject->togenerate = 1 - $fromform->togenerate;
    $DB->update_record('tool_taskattestoodle', $dataobject);

    trainings_factory::get_instance()->create_training_by_category(1, $record->trainingid);
    $training = trainings_factory::get_instance()->retrieve_training_by_id($record->trainingid);
    newdeadline($training);
    // Redirect to the list.
    $redirecturl = new \moodle_url('/admin/tool/taskattestoodle/listinterval.php', array('trainingid' => $record->trainingid));
    redirect($redirecturl);
    return;
}

$entry = array (
                'executiondate' => $record->executiondate,
                'startdate' => $record->beginperiod,
                'enddate' => $record->endperiod,
                'auto' => $record->auto,
                'email' => $record->mailto,
                'togenerate' => 1 - $record->togenerate,
                );
$mform->set_data($entry);

echo $OUTPUT->header();

$mform->display();

echo $OUTPUT->footer();
