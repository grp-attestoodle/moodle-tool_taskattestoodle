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

namespace tool_taskattestoodle\task;

defined('MOODLE_INTERNAL') || die;

use tool_attestoodle\factories\trainings_factory;
use tool_attestoodle\certificate;
use tool_attestoodle\gabarit\attestation_pdf;

class generatecertificate extends \core\task\scheduled_task {
    /**
     * Returns the name of this task.
     */
    public function get_name() {
        // Shown in admin screens.
        return get_string('taskname', 'tool_taskattestoodle');
    }

    /**
     * Execute task.
     */
    public function execute() {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/admin/tool/taskattestoodle/lib.php');
        // Select task todo.
        $now = new \DateTime();
        $secnow = $now->getTimestamp();

        $req = 'select *
                  from {tool_taskattestoodle}
                 where executiondate < ?
                   and togenerate = 1';
        $todos = $DB->get_records_sql($req, array('executiondate' => $secnow));
        if (count($todos) == 0) {
            return;
        }

        foreach ($todos as $result) {
            $message = '<html>';
            $message .= '<head><title>' . get_string('titlemsgtask', 'tool_taskattestoodle') . '</title></head>';
            $message .= '<body>';

            $begindate = new \DateTime();
            $begindate->setTimestamp($result->beginperiod);
            $enddate = new \DateTime();
            $enddate->setTimestamp($result->endperiod);

            $trainingid = $result->trainingid;
            trainings_factory::get_instance()->create_training_by_category(1, $trainingid);
            $training = trainings_factory::get_instance()->retrieve_training_by_id($trainingid);

            if ($result->auto == 1) {
                $message .= get_string('lig1msgtask', 'tool_taskattestoodle');
                $this->generate($result, $training, $begindate, $enddate);
            } else {
                $message .= get_string('lig2msgtask', 'tool_taskattestoodle');
            }
            $message .= $training->get_name() . ' ' . get_string('trainingat', 'tool_taskattestoodle');
            $message .= $begindate->format('d/m/Y') . get_string('trainingto', 'tool_taskattestoodle') . $enddate->format('d/m/Y');

            // Update table taskattestoodle.
            $dataobject = new \stdClass();
            $dataobject->id = $result->id;
            $dataobject->togenerate = 0;
            $DB->update_record('tool_taskattestoodle', $dataobject);
            // Update training deadline.
            newdeadline($training);

            $urlview = new \moodle_url('/admin/tool/attestoodle/index.php',
                            array(
                                'typepage' => 'learners',
                                'trainingid' => $result->trainingid,
                                'categoryid' => $training->get_categoryid(),
                                'begindate' => $begindate->format('Y-m-d'),
                                'enddate' => $enddate->format('Y-m-d')));
            $message .= '<a href="'. $urlview->out() .'">  : ' . get_string('lnkmsgtask', 'tool_taskattestoodle') . '</a><br/>';
            $message .= '</p></body>';
            $message .= '</html>';
            // Mail.
            $admin = get_admin();
            if (!empty($result->mailto)) {
                $to = $DB->get_record('user', array('id' => $result->operatorid));
                $to->email = $result->mailto;

                $eventdata = new \core\message\message();
                $eventdata->courseid = 0;
                $eventdata->component = 'tool_taskattestoodle';
                $eventdata->name = 'generatecertificate';
                $eventdata->userfrom = $admin;
                $eventdata->userto = $to;
                $eventdata->notification = 1;
                $eventdata->subject = '[Task ATTESTOODLE]';
                $eventdata->fullmessage = html_to_text($message);
                $eventdata->fullmessageformat = FORMAT_HTML;
                $eventdata->fullmessagehtml   = $message;
                $eventdata->smallmessage      = '';
                message_send($eventdata);
            }
        }
    }

    /**
     * Generate certificate for the training.
     *
     * @param Object $task Task's description.
     * @param Training $training Training from which the certificates will be generated
     * @param DateTime $begindate start date of the certificate period.
     * @param DateTime $enddate end date of the certificate period.
     */
    protected function generate($task, $training, $begindate, $enddate) {
        global $DB;
        $trainingid = $training->get_id();

        foreach ($training->get_learners() as $learner) {
            $template = $DB->get_record('tool_attestoodle_user_style',
                                    array('userid' => $learner->get_id(), 'trainingid' => $trainingid));
            $enablecertificate = 1;
            if (isset($template->enablecertificate)) {
                $enablecertificate = $template->enablecertificate;
            }

            if ($enablecertificate == 1) {
                // Create row in table Launch log.
                $dataobject = new \stdClass();
                $dataobject->timegenerated = \time();
                $dataobject->begindate = $begindate->format('Y-m-d');
                $dataobject->enddate = $enddate->format('Y-m-d');
                $dataobject->operatorid = $task->operatorid;
                $dataobject->comment = 'generate by task';
                $launchid = $DB->insert_record('tool_attestoodle_launch_log', $dataobject, true);

                $certificate = new certificate($learner, $training, $begindate, $enddate);
                $status = $certificate->create_file_on_server();
                $pdfinfo = $certificate->get_pdf_informations();

                // Log the certificate informations.
                if ($launchid > 0) {
                    $statusstring = null;
                    switch ($status) {
                        case 0:
                            $statusstring = 'ERROR';
                            break;
                        case 1:
                            $statusstring = 'NEW';
                            break;
                        case 2:
                            $statusstring = 'OVERWRITTEN';
                            break;
                    }

                    // Try to record the certificate log.
                    $dataobject = new \stdClass();
                    $dataobject->filename = $certificate->get_file_name();
                    $dataobject->status = $statusstring;
                    $dataobject->trainingid = $trainingid;
                    $dataobject->learnerid = $learner->get_id();
                    $dataobject->launchid = $launchid;

                    $certificatelogid = $DB->insert_record('tool_attestoodle_certif_log', $dataobject, true);

                    // Try to record the values used to generate the certificate.
                    $milestones = array();
                    $activities = $pdfinfo->activities;;
                    foreach ($activities as $obj) {
                        $dataobject = new \stdClass();
                        $dataobject->creditedtime = $obj["totalminutes"];
                        $dataobject->certificateid = $certificatelogid;
                        $dataobject->moduleid = $obj["moduleid"];
                        $milestones[] = $dataobject;
                    }
                    $DB->insert_records('tool_attestoodle_value_log', $milestones);
                }
            }
        }
    }
}