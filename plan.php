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
 * Entry point on the planning of certification dates.
 *
 * @package    tool_taskattestoodle
 * @copyright  2019 marc.leconte@univ-lemans.fr
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Main configuration importation (instanciate the $CFG global variable).
require_once(dirname(__FILE__) . '/../../../config.php');

use tool_attestoodle\factories\trainings_factory;
use tool_attestoodle\utils\db_accessor;

require_once(dirname(__FILE__) . "/classes/training_update_form.php");

use tool_taskattestoodle\forms\training_update_form;

$trainingid = required_param('trainingid', PARAM_INT);
$reinit = optional_param('reinit', 0, PARAM_INT);

$context = context_system::instance();
require_login();

$PAGE->set_context($context);
$PAGE->navbar->ignore_active();
$navlevel1 = get_string('navlevel1', 'tool_attestoodle');
$PAGE->navbar->add($navlevel1, new moodle_url('/admin/tool/attestoodle/index.php', array()));

$title = get_string('titleplanning1', 'tool_taskattestoodle');
$url = new moodle_url('/admin/tool/taskattestoodle/plan.php', array('trainingid' => $trainingid));
$PAGE->navbar->add($title, $url);
$PAGE->set_url($url);

$PAGE->set_title($title);
$PAGE->set_heading($title);


global $DB, $USER;
trainings_factory::get_instance()->create_training_by_category(1, $trainingid);
$training = trainings_factory::get_instance()->retrieve_training_by_id($trainingid);

if ($reinit == 1) {
    $DB->delete_records('tool_taskattestoodle', array('trainingid' => $trainingid));
    $training->set_nextlaunch(null);
    db_accessor::get_instance()->updatetraining($training);
}

$records = $DB->get_records('tool_taskattestoodle', array('trainingid' => $trainingid));
$cpt = count($records);


$mform = new training_update_form($url);

if ($fromform = $mform->get_data()) {
    // Process validated data.
    if (isset($fromform->cancel)) {
        $redirecturl = new \moodle_url('/admin/tool/attestoodle/index.php', array());
        redirect($redirecturl);
        return;
    }
    // Creating intervals.
    if ($cpt == 0) {
        $now = new \DateTime();
        $secnow = $now->getTimestamp();

        $inter = floor (($fromform->enddate - $fromform->startdate) / $fromform->nbautolaunch);
        $executiondate = $fromform->startdate + ($fromform->offset * 86400);

        $beginperiod = $fromform->startdate;
        $auto = 0;
        if (isset($fromform->auto)) {
            $auto = 1;
        }
        $nextlaunch = 0;
        $wdate = new \DateTime();
        for ($i = 1; $i <= $fromform->nbautolaunch; $i++) {
            $dataobject = new \stdClass();
            $dataobject->trainingid = $trainingid;
            $executiondate += $inter;
            $wdate->setTimestamp($executiondate);
            $wdate->setTime($fromform->hour, $fromform->minu);

            $dataobject->executiondate = $wdate->getTimestamp();
            $dataobject->beginperiod = $beginperiod;
            $beginperiod += $inter;
            $dataobject->endperiod = $beginperiod;
            $dataobject->mailto = $fromform->email;
            $dataobject->operatorid = $USER->id;
            $dataobject->auto = $auto;
            $dataobject->togenerate = 1;
            if ($secnow > $executiondate) {
                $dataobject->togenerate = 0;
            } else if ($nextlaunch == 0) {
                $nextlaunch = $executiondate;
            }
            $DB->insert_record('tool_taskattestoodle', $dataobject);
        }

        if ($nextlaunch == 0) {
            $nextlaunch = $fromform->enddate;
        }
        $training->set_nextlaunch($nextlaunch);
        $training->set_nbautolaunch($fromform->nbautolaunch);
        $training->set_start($fromform->startdate);
        $training->set_end($fromform->enddate);
        db_accessor::get_instance()->updatetraining($training);
        $cpt = $fromform->nbautolaunch;
    }
}

if ($cpt > 0) {
    // If a planning already exists, the user is redirected to the management of the attestation dates.
    $redirecturl = new \moodle_url('/admin/tool/taskattestoodle/listinterval.php', array('trainingid' => $trainingid));
    redirect($redirecturl);
    return;
}

$entry = array ('name' => $training->get_name(),
                'startdate' => $training->get_start(),
                'enddate' => $training->get_end(),
                'email' => $USER->email);
$mform->set_data($entry);

echo $OUTPUT->header();

$mform->display();

echo $OUTPUT->footer();
