<?php

/**
 * *************************************************************************
 * *                               MathEditor                             **
 * *************************************************************************
 * @package     mod                                                       **
 * @subpackage  interactivequiz                                           **
 * @name        Interactive Quiz                                          **
 * @copyright   oohoo.biz                                                 **
 * @link        http://oohoo.biz                                          **
 * @author      Raymond Wainman                                           **
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later  **
 * *************************************************************************
 * ************************************************************************ */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');

$cmid = required_param('cmid', PARAM_INT);   // course module ID

$cm         = get_coursemodule_from_id('interactivequiz', $cmid, 0, false, MUST_EXIST);
$course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$interactivequiz  = $DB->get_record('interactivequiz', array('id' => $cm->instance), '*', MUST_EXIST);

require_course_login($course);

add_to_log($course->id, 'interactivequiz', 'view all', 'edit.php?cmid='.$cmid, '');

$coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);

$PAGE->set_url('/mod/interactivequiz/edit.php', array('cmid' => $cmid));
$PAGE->set_title(format_string($course->fullname));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($coursecontext);

echo $OUTPUT->header();

echo $OUTPUT->footer();
