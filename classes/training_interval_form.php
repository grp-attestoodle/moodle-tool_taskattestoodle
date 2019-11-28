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
class training_interval_form extends \moodleform {
    /**
     * Method automagically called when the form is instanciated. It defines
     * all the elements (inputs, titles, buttons, ...) in the form.
     */
    public function definition() {
        $mform = $this->_form;
        $options = array('step' => 1);
        $mform->addElement('date_time_selector', 'executiondate', get_string('executiondate', 'tool_taskattestoodle'), $options);
        $mform->addElement('date_selector', 'startdate', get_string('beginperiod', 'tool_taskattestoodle'));
        $mform->addElement('date_selector', 'enddate', get_string('endperiod', 'tool_taskattestoodle'));
        $mform->addElement('checkbox', 'auto', get_string('autogenerate', 'tool_taskattestoodle'), ' ');
        $mform->addElement('checkbox', 'togenerate', get_string('done', 'tool_taskattestoodle'), ' ');
        $mform->addElement('text', 'email', get_string('notifyto', 'tool_taskattestoodle'), array("size" => 70));
        $mform->setType('email', PARAM_EMAIL);

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
        $errors = parent::validation($data, $files);

        if (isset($data['cancel'])) {
            return $errors;
        }

        if (isset($data['enddate']) && $data['startdate'] > $data['enddate']) {
            $errors['enddate'] = get_string('errdateend', 'tool_attestoodle');
        }
        if ($data['executiondate'] < $data['startdate']) {
            $errors['executiondate'] = get_string('errexectooearly', 'tool_taskattestoodle');
        }
        return $errors;
    }
}
