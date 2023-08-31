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
 * Useful global functions for Task Attestoodle.
 *
 * @package    tool_taskattestoodle
 * @copyright  2019 Pole de Ressource Numerique de l'Université du Mans
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


use tool_attestoodle\utils\db_accessor;

/**
 * Link to the source that manages the planning.
 *
 * @param int $trainingid ID of training to plan.
 * @return moodle_url to the url to the source that manages the planning.
 */
function task_link($trainingid) {
    $url = new moodle_url('/admin/tool/taskattestoodle/plan.php', ['trainingid' => $trainingid]);
    return $url;
}

/**
 * Deletes the schedule of the deleted training.
 *
 * @param int $trainingid ID of deleted training.
 */
function tool_taskattestoodle_deletetraining($trainingid) {
    global $DB;
    $DB->delete_records('tool_taskattestoodle', array('trainingid' => $trainingid));
    return "";
}
/**
 * Provides the most appropriate interval, based on the current date.
 *
 * @param int $trainingid ID of the training.
 */
function tool_taskattestoodle_get_interval($trainingid) {
    global $DB;
    $ret = new \stdClass();
    $ret->d_start = 0;
    $ret->d_end = 0;

    $rs = $DB->get_recordset_sql('select executiondate, beginperiod, endperiod
                                    from {tool_taskattestoodle}
                                   where trainingid = ? order by executiondate', array($trainingid));

    $now = new \DateTime();
    $secnow = $now->getTimestamp();

    if (empty($rs)) {
        return $ret;
    }

    $dist1 = 0;
    foreach ($rs as $result) {
        $dist2 = abs($result->endperiod - $secnow);

        if ($ret->d_end == 0 || $dist1 > $dist2) {
            $ret->d_start = $result->beginperiod;
            $ret->d_end = $result->endperiod;
            $dist1 = $dist2;
        }
    }
    return $ret;
}

/**
 * Compute new deadline.
 *
 * @param Training $training where we compute the new deadline.
 */
function newdeadline($training) {
    global $DB;
    $now = new \DateTime();
    $secnow = $now->getTimestamp();
    $rs = $DB->get_recordset_sql(
        'select * from {tool_taskattestoodle} where trainingid = ? order by executiondate',
        array($training->get_id())
    );
    $nextlaunch = 0;
    foreach ($rs as $result) {
        if ($secnow < $result->executiondate && $nextlaunch == 0) {
            $nextlaunch = $result->executiondate;
        }
    }
    $training->set_nextlaunch($nextlaunch);
    db_accessor::get_instance()->updatetraining($training);
}
