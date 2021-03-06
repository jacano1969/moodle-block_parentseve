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
 * Deletes a parents' evening
 *
 * Much like {@see cancel.php}, this displays a confirmation form which, once submitted,
 * will delete and entire parents' evening and associated appointments.
 *
 * @package block_parenteseve
 * @author Mark Johnson <johnsom@tauntons.ac.uk>
 * @copyright Copyright &copy; 2009, Taunton's College, Southampton, UK
 * @param int id The ID of the parents' evening record to delete
 * @param int confirm Whether the deletion has been confirmed
 */

require_once('../../config.php');
require_once($CFG->dirroot.'/blocks/parentseve/lib.php');
$id = required_param('id', PARAM_INT);
$parentseve = required_param('parentseve', PARAM_INT);

require_login($SITE);
$context = get_context_instance(CONTEXT_BLOCK, $id);
require_capability('block/parentseve:manage', $context);

$confirm = optional_param('confirm', 0, PARAM_BOOL);
$parentseve = $DB->get_record('parentseve', array('id' => $parentseve));
if (!$parentseve) {
    print_error('noparentseve', 'block_parentseve');
}

$urlparams = array('id' => $id, 'parentseve' => $parentseve->id);
$PAGE->set_url(new moodle_url('/blocks/parentseve/delete.php', $urlparams));

if (has_capability('block/parentseve:manage', $context)) {
    $url = new moodle_url('/blocks/parentseve/manage.php', array('id' => $id));
    $PAGE->navbar->add(get_string('parentseve', 'block_parentseve'), $url);
} else {
    $PAGE->navbar->add(get_string('parentseve', 'block_parentseve'));
}
$viewall = has_capability('block/parentseve:viewall', $context);
$isteacher = parentseve_isteacher($USER->id, $parentseve);
if ($viewall || $isteacher) {
    $url = new moodle_url('/blocks/parentseve/schedule.php', array('id' => $parentseve->id));
    $PAGE->navbar->add(date('l jS M Y', $parentseve->timestart), $url);
} else {
    $PAGE->navbar->add(date('l jS M Y', $parentseve->timestart));
}
$PAGE->navbar->add(get_string('delete'));

if ($confirm) {
    $DB->delete_records('parentseve_app', array('parentseveid' => $parentseve->id));
    $DB->delete_records('parentseve_teacher', array('parentseveid' => $parentseve->id));
    $DB->delete_records('parentseve', array('id' => $parentseve->id));
    redirect($CFG->wwwroot.'/blocks/parentseve/manage.php?id='.$id);
} else {

    $a->date = date('d/M/Y', $parentseve->timestart);
    $a->time = date('H:i', $parentseve->timestart);
    $content = '<p>'.get_string('parentsevedelete', 'block_parentseve', $a).'</p>
    <p>'.get_string('confirmdelete', 'block_parentseve').'</p>
    <form method="post" action="'.$CFG->wwwroot.'/blocks/parentseve/delete.php">
    <input type="hidden" name="confirm" value="1" />
    <input type="hidden" name="id" value="'.$id.'" />
    <input type="hidden" name="parentseve" value="'.$parentseve->id.'" />
    <input type="submit" value="'.get_string('yes').'" />
    </form>
    <form method="post" action="'.$CFG->wwwroot.'/blocks/parentseve/manage.php">
    <input type="submit" value="'.get_string('no').'" />
    </form>';
}

echo $OUTPUT->header();

echo $content;

echo $OUTPUT->footer();
