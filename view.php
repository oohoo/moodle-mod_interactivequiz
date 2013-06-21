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

global $DB;

$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
$n  = optional_param('i', 0, PARAM_INT);  // interactivequiz instance ID - it should be named as the first character of the module

if ($id) {
    $cm         = get_coursemodule_from_id('interactivequiz', $id, 0, false, MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $interactivequiz  = $DB->get_record('interactivequiz', array('id' => $cm->instance), '*', MUST_EXIST);
} elseif ($i) {
    $interactivequiz  = $DB->get_record('interactivequiz', array('id' => $n), '*', MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $interactivequiz->course), '*', MUST_EXIST);
    $cm         = get_coursemodule_from_instance('interactivequiz', $interactivequiz->id, $course->id, false, MUST_EXIST);
} else {
    error('You must specify a course_module ID or an instance ID');
}

require_login($course, true, $cm);
$context = get_context_instance(CONTEXT_MODULE, $cm->id);

add_to_log($course->id, 'interactivequiz', 'view', "view.php?id={$cm->id}", $interactivequiz->name, $cm->id);

/// Print the page header

$PAGE->set_url('/mod/interactivequiz/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($interactivequiz->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

// other things you may want to set - remove if not needed
//$PAGE->set_cacheable(false);
//$PAGE->set_focuscontrol('some-html-id');
//$PAGE->add_body_class('interactivequiz-'.$somevar);

// Output starts here
echo $OUTPUT->header();

if ($interactivequiz->intro) { // Conditions to show the intro can change to look for own settings or whatever
    echo $OUTPUT->box(format_module_intro('interactivequiz', $interactivequiz, $cm->id), 'generalbox mod_introbox', 'interactivequizintro');
}

// Replace the following lines with you own code
echo $OUTPUT->heading('Yay! It works!');

$quba = question_engine::make_questions_usage_by_activity('mod_interactivequiz', $context);
$quba->set_preferred_behaviour('deferredfeedback');

$idstoslots = array();

$question = question_bank::load_question(7);
$idstoslots = $quba->add_question($question);

$quba->start_all_questions();

echo $quba->render_question($idstoslots, new question_display_options(), 1);

// Finish the page
echo $OUTPUT->footer();
