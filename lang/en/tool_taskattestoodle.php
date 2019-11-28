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
 * Plugin strings are defined here.
 *
 * @package     tool_taskattestoodle
 * @category    string
 * @copyright   marc.leconte@univ-lemans.fr
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'taskattestoodle';
$string['titletraining'] = 'Training :';
$string['titleplanning1'] = 'Planning of certificates';
$string['titleplanning2'] = 'Management of certification dates';
$string['autogenerate'] = 'Automatic generation';
$string['notifyto'] = 'Email to be notified';
$string['nbgeneration'] = 'Number of generations';
$string['offsetexec'] = 'Day shift with the end of the period';
$string['errstartdate'] = 'The start date is required !!';
$string['errenddate'] = 'The end date is required !!';
$string['errnbautolaunch'] = 'Planning requires a number of generations > 0 !!';
$string['errdaysmax'] = 'Number of generations too many !!';
$string['erroffset'] = 'The offset must not be greater than the duration of the interval !!';
$string['dateformatDay'] = 'Y-m-d H:i (D)';
$string['trainingat'] = 'From ';
$string['trainingto'] = ' to ';
$string['automatic'] = 'Automatic';
$string['done'] = 'done';
$string['actions'] = 'Actions';
$string['reinit'] = 'Reset';
$string['errhour'] = 'The time must be within the interval [0,23]';
$string['errminu'] = 'The minutes must be within the interval [0,59]';
$string['titleplanning3'] = 'Changing an interval';
$string['executiondate'] = 'Date of generation of the certificates';
$string['beginperiod'] = 'Beginning of the interval';
$string['endperiod'] = 'End of the interval';
$string['errexectooearly'] = 'Execution date too early !!';
$string['taskname'] = 'Generate certificates';
$string['titlemsgtask'] = 'Task Attestoodle';
$string['lig1msgtask'] = '<p>Generation of certificates for training : ';
$string['lig2msgtask'] = '<p>Think of carrying out the certificates for the training : ';
$string['lnkmsgtask'] = 'link';
$string['privacy:metadata'] = 'Attestoodle\'s task scheduling tool does not record any personal data.';
