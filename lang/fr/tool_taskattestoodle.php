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
$string['titletraining'] = 'Formation :';
$string['titleplanning1'] = 'Planification des attestations';
$string['titleplanning2'] = 'Gestion des dates d\'attestation';
$string['autogenerate'] = 'Génération automatique';
$string['notifyto'] = 'Email à notifier';
$string['nbgeneration'] = 'Nombre de génération';
$string['offsetexec'] = 'Décalage en jour avec la fin période';
$string['errstartdate'] = 'La date de début est requise !!';
$string['errenddate'] = 'La date de fin est requise !!';
$string['errnbautolaunch'] = 'La planification nécessite un nombre de génération > 0 !!';
$string['errdaysmax'] = 'Nombre de génération trop important !!';
$string['erroffset'] = 'Le décalage ne doit pas être supérieur à la durée de l\'intervalle !!';
$string['dateformatDay'] = 'd/m/Y H:i (D)';
$string['trainingat'] = 'Du ';
$string['trainingto'] = ' au ';
$string['automatic'] = 'Automatique';
$string['done'] = 'Fait';
$string['actions'] = 'Actions';
$string['reinit'] = 'Réinitialiser';
$string['errhour'] = 'L\'heure doit appartenir à l\'intervalle [0,23]';
$string['errminu'] = 'Les minutes doivent appartenir à l\'intervalle [0,59]';
$string['titleplanning3'] = 'Modification d\'un intervalle';
$string['executiondate'] = 'Date de génération des attestations';
$string['beginperiod'] = 'Début de l\'intervalle';
$string['endperiod'] = 'Fin de l\'intervalle';
$string['errexectooearly'] = 'La date d\'exécution est trop en avance sur l\'intervalle !!';
$string['taskname'] = 'Générer les attestations';
$string['titlemsgtask'] = 'Traitement Attestoodle';
$string['lig1msgtask'] = '<p>Génération des attestations pour la formation : ';
$string['lig2msgtask'] = '<p>Penser à réaliser les attestations  pour la formation : ';
$string['lnkmsgtask'] = 'lien';
$string['privacy:metadata'] = 'L\'outil de planification des tâches d\'Attestoodle n\'enregistre aucune donnée personnelle.';
$string['messageprovider:generatecertificate'] = 'Génération des attestations';
