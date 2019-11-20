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
 * This is form for modification of training.
 *
 * @package    tool_taskattestoodle
 * @copyright  2018 Pole de Ressource Numerique de l'Universite du Mans
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_taskattestoodle\forms;
defined('MOODLE_INTERNAL') || die;

// Class \moodleform is defined in formslib.php.
require_once("$CFG->libdir/formslib.php");
/**
 * Class that handles the modification of trainings through moodleform.
 *
 * @copyright  2019 Pole de Ressource Numerique de l'Universite du Mans
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class training_update_form extends \moodleform {
    /**
     * Method automagically called when the form is instanciated. It defines
     * all the elements (inputs, titles, buttons, ...) in the form.
     */
    public function definition() {
        global $CFG, $DB;
        $mform = $this->_form;

        $mform->addElement('hidden', 'lock');
        $mform->setType('lock', PARAM_INT);
        $mform->setDefault('lock', 1);

        $mform->addElement('text', 'name', get_string('trainingname', 'tool_attestoodle'), array("size" => 50));
        $mform->setType('name', PARAM_NOTAGS);
        $mform->disabledIf('name', 'lock', 'eq', 1);

        $mform->addElement('date_selector', 'startdate', get_string('starttraining', 'tool_attestoodle'));
        $mform->addElement('date_selector', 'enddate', get_string('endtraining', 'tool_attestoodle'));
        $mform->addElement('checkbox', 'auto', get_string('autogenerate', 'tool_taskattestoodle'), ' ');
        $mform->setDefault('auto', 1);

        $mform->addElement('text', 'email', get_string('notifyto', 'tool_taskattestoodle'), array("size" => 70));
        $mform->setType('email', PARAM_EMAIL);

        $mform->addElement('text', 'nbautolaunch', get_string('nbgeneration', 'tool_taskattestoodle'), array("size" => 3));
        $mform->setType('nbautolaunch', PARAM_INT);

        $group = array();
        $group[] =& $mform->createElement('text', 'hour', 'HH:MM', array("size" => 2));
        $group[] =& $mform->createElement('text', 'minu', '', array("size" => 2));
        $mform->setType('hour', PARAM_INT);
        $mform->setType('minu', PARAM_INT);
        $mform->addGroup($group, 'time', 'Heure de génération', ' ', false);

        $mform->addElement('text', 'offset', get_string('offsetexec', 'tool_taskattestoodle'), array("size" => 3));
        $mform->setType('offset', PARAM_INT);
        $mform->setDefault('offset', 0);

        $actionbuttongroup = array();
        $actionbuttongroup[] =& $mform->createElement('submit', 'save', get_string('savechanges'),
                array('class' => 'send-button'));
        $actionbuttongroup[] =& $mform->createElement('submit', 'cancel', get_string('cancel'),
                array('class' => 'cancel-button'));
        $mform->addGroup($actionbuttongroup, 'actionbuttongroup', '', ' ', false);
    }

    /**
     * Custom validation function automagically called when the form
     * is submitted. The standard validations, such as required inputs or
     * value type check, are done by the parent validation() method.
     * See validation() method in moodleform class for more details.
     * @param stdClass $data of form
     * @param string $files list of the form files
     * @return array of error.
     */
    public function validation($data, $files) {
        global $DB;
        $errors = parent::validation($data, $files);

        if (isset($data['cancel'])) {
            return $errors;
        }

        if (!isset($data['startdate'])) {
            $errors['startdate'] = get_string('errstartdate', 'tool_taskattestoodle');
        }
        if (!isset($data['enddate'])) {
            $errors['enddate'] = get_string('errenddate', 'tool_taskattestoodle');
        }
        if (isset($data['enddate']) && $data['startdate'] > $data['enddate']) {
            $errors['enddate'] = get_string('errdateend', 'tool_attestoodle');
            return $errors;
        }

        if (isset($data['hour']) && ($data['hour'] < 0 || $data['hour'] > 23)) {
            $errors['time'] = get_string('errhour', 'tool_taskattestoodle');
        }
        if (isset($data['minu']) && ($data['minu'] < 0 || $data['minu'] > 59)) {
            $errors['time'] = get_string('errminu', 'tool_taskattestoodle');
        }

        if (!isset($data['nbautolaunch']) || $data['nbautolaunch'] <= 0) {
            $errors['nbautolaunch'] = get_string('errnbautolaunch', 'tool_taskattestoodle');
        } else {
            $daysmax = floor(($data['enddate'] - $data['startdate']) / 86400);
            if ($data['nbautolaunch'] > $daysmax) {
                $errors['nbautolaunch'] = get_string('errdaysmax', 'tool_taskattestoodle');
                return $errors;
            }
            if (isset($data['offset'])) {
                $intervaltime = floor(($data['enddate'] - $data['startdate']) / $data['nbautolaunch']);
                $diff = abs($data['offset']) * 86400;
                if ($diff > $intervaltime) {
                    $errors['offset'] = get_string('erroffset', 'tool_taskattestoodle');
                }
            }
        }
        return $errors;
    }
}
