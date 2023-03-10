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
 * List of planning intervals.
 *
 * @package    tool_taskattestoodle
 * @copyright  2019 Pole de Ressource Numerique de l'Universite du Mans
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../../config.php');
require_once(dirname(__FILE__) . '/lib.php');
require_once($CFG->libdir.'/tablelib.php');

use tool_attestoodle\factories\trainings_factory;
use tool_attestoodle\utils\db_accessor;

define('DEFAULT_PAGE_SIZE', 10);

$trainingid = required_param('trainingid', PARAM_INT);
$delete = optional_param('delete', 0, PARAM_INT);
$cut = optional_param('cut', 0, PARAM_INT);
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', DEFAULT_PAGE_SIZE, PARAM_INT);

require_login();
$context = context_system::instance();

$PAGE->set_context($context);
$PAGE->navbar->ignore_active();
$navlevel1 = get_string('navlevel1', 'tool_attestoodle');
$PAGE->navbar->add($navlevel1, new moodle_url('/admin/tool/attestoodle/index.php', array()));

$title = get_string('titleplanning2', 'tool_taskattestoodle');
$PAGE->navbar->add($title, new moodle_url('/admin/tool/taskattestoodle/listinterval.php',
        array('trainingid' => $trainingid)));

$PAGE->set_title($title);
$PAGE->set_heading($title);

$baseurl = new moodle_url('/admin/tool/taskattestoodle/listinterval.php', array(
        'trainingid' => $trainingid,
        'page' => $page,
        'perpage' => $perpage));
$PAGE->set_url($baseurl);

trainings_factory::get_instance()->create_training_by_category(1, $trainingid);
$training = trainings_factory::get_instance()->retrieve_training_by_id($trainingid);

if ($delete) {
    $DB->delete_records('tool_taskattestoodle', array('id' => $delete));
    $nb = $training->get_nbautolaunch() - 1;
    $training->set_nbautolaunch($nb);

    if ($nb == 0) {
        $training->set_nextlaunch(null);
        db_accessor::get_instance()->updatetraining($training);

        $redirecturl = new \moodle_url('/admin/tool/taskattestoodle/plan.php', array('trainingid' => $trainingid));
        redirect($redirecturl);
        return;
    }
    newdeadline($training);
}

if ($cut) {
    $record = $DB->get_record('tool_taskattestoodle', array('id' => $cut));
    $duration = $record->endperiod - $record->beginperiod;
    if ($duration > 86400) {
        $inter = floor($duration / 2);
        $executiondate = $record->beginperiod;
        $wdate = new \DateTime();
        $secnow = $wdate->getTimestamp();
        $wdate->setTimestamp($record->executiondate);
        $hour = $wdate->format('H');
        $minu = $wdate->format('i');
        $beginperiod = $record->beginperiod;

        for ($i = 0; $i < 2; $i++) {
            $dataobject = new \stdClass();
            $dataobject->trainingid = $trainingid;
            $executiondate += $inter;
            $wdate->setTimestamp($executiondate);
            $wdate->setTime($hour, $minu);
            $dataobject->executiondate = $wdate->getTimestamp();

            $dataobject->beginperiod = $beginperiod;
            $beginperiod += $inter;
            $dataobject->endperiod = $beginperiod;
            $dataobject->mailto = $record->mailto;
            $dataobject->operatorid = $USER->id;
            $dataobject->auto = $record->auto;
            $dataobject->togenerate = 1;
            if ($secnow > $executiondate) {
                $dataobject->togenerate = 0;
            }
            $DB->insert_record('tool_taskattestoodle', $dataobject);
        }
        $DB->delete_records('tool_taskattestoodle', array('id' => $cut));
        newdeadline($training);
    } else {
         \core\notification::error("Cet intervalle est trop petit pour être subdivisé !!");
    }
}

// Data preparation.
echo $OUTPUT->header();
$renderer = new core_renderer($PAGE, null);

$wdate = new \DateTime();
$wdate->setTimestamp($training->get_start());
$trainingstart = $wdate->format(get_string('dateformat', 'tool_attestoodle'));
$wdate->setTimestamp($training->get_end());
$trainingend = $wdate->format(get_string('dateformat', 'tool_attestoodle'));

echo $renderer->render_from_template('tool_taskattestoodle/training', array(
        'titletraining' => get_string('titletraining', 'tool_taskattestoodle'),
        'trainingname' => $training->get_name(),
        'trainingstart' => $trainingstart,
        'trainingend' => $trainingend,
        'trainingat' => get_string('trainingat', 'tool_taskattestoodle'),
        'trainingto' => get_string('trainingto', 'tool_taskattestoodle')
    ), null);


$table = new flexible_table('admin_tool_interval');
$tablecolumns = array('executiondate', 'beginperiod', 'endperiod', 'auto', 'togenerate', 'actions');
$tableheaders = array(get_string('deadline', 'tool_attestoodle'),
                      get_string('trainingat', 'tool_taskattestoodle'),
                      get_string('trainingto', 'tool_taskattestoodle'),
                      get_string('automatic', 'tool_taskattestoodle'),
                      get_string('done', 'tool_taskattestoodle'),
                      get_string('actions', 'tool_taskattestoodle'));

$table->define_columns($tablecolumns);
$table->define_headers($tableheaders);
$table->define_baseurl($baseurl->out());
$table->sortable(false);
$table->set_attribute('width', '80%');
$table->set_attribute('class', 'generaltable');

$table->column_style('actions', 'width', '10%');
$table->column_style('togenerate', 'width', '10%');
$table->column_style('auto', 'width', '10%');
$table->column_style('trainingat', 'width', '15%');
$table->column_style('trainingto', 'width', '15%');

$table->setup();
$matchcount = $DB->count_records_sql("SELECT COUNT(id) from {tool_taskattestoodle} where trainingid = " . $trainingid);

$table->pagesize($perpage, $matchcount);

$rs = $DB->get_recordset_sql('select * from {tool_taskattestoodle} where trainingid = ? order by executiondate',
                             array($trainingid), $table->get_page_start(), $table->get_page_size());

$rows = array();
foreach ($rs as $result) {
    // Possible suppression test.
    $dellink = "";
    $deleteurl = new moodle_url('/admin/tool/taskattestoodle/listinterval.php',
                          ['delete' => $result->id, 'trainingid' => $trainingid]);
    $dellink = "<a href=" . $deleteurl . "><i class='fa fa-trash'></i></a>&nbsp;&nbsp;";

    // Manage template.
    $editlink = "";
    $url = new moodle_url('/admin/tool/taskattestoodle/interval.php', ['id' => $result->id]);
    $editlink = "<a href=" . $url . "><i class='fa fa-edit'></i></a>  ";

    // Cut.
    $cutlink = "";
    $cuturl = new moodle_url('/admin/tool/taskattestoodle/listinterval.php',
                          ['cut' => $result->id, 'trainingid' => $trainingid]);
    $cutlink = "<a href=" . $cuturl . "><i class='fa fa-cut'></i></a>&nbsp;&nbsp;";

    $wdate = new \DateTime();
    $wdate->setTimestamp($result->executiondate);
    $executiondate = $wdate->format(get_string('dateformatDay', 'tool_taskattestoodle'));

    $wdate->setTimestamp($result->beginperiod);
    $beginperiod = $wdate->format(get_string('dateformat', 'tool_attestoodle'));

    $wdate->setTimestamp($result->endperiod);
    $endperiod = $wdate->format(get_string('dateformat', 'tool_attestoodle'));

    $auto = "<i class='fa fa-square-o'></i>";
    if ($result->auto > 0) {
        $auto = "<i class='fa fa-check-square-o'></i>";
    }
    $togenerate = "<i class='fa fa-check-square-o'></i>";
    if ($result->togenerate > 0) {
        $togenerate = "<i class='fa fa-square-o'></i>";
    }
    $rows[] = array('executiondate' => $executiondate, 'beginperiod' => $beginperiod,
                'endperiod' => $endperiod, 'auto' => $auto, 'togenerate' => $togenerate,
                'actions' => $dellink . $cutlink . $editlink);
}

foreach ($rows as $row) {
    $table->add_data(array(
                    $row['executiondate'], $row['beginperiod'],
                    $row['endperiod'], $row['auto'], $row['togenerate'], $row['actions']));
}

$table->print_html();

// Buttons réinit + cancel.
echo "<br/>";
$reiniturl = new moodle_url('/admin/tool/taskattestoodle/plan.php',
                          ['trainingid' => $trainingid, 'reinit' => 1]);
echo $OUTPUT->single_button($reiniturl, get_string('reinit', 'tool_taskattestoodle'), 'post');

$cancelurl = new moodle_url('/admin/tool/attestoodle/index.php', array());
echo $OUTPUT->single_button($cancelurl, get_string('cancel'), 'post');

echo $OUTPUT->footer();
