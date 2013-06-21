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

// JS Dependencies
$PAGE->requires->jquery();
$PAGE->requires->js('/mod/interactivequiz/javascript/editquiz.js');
$PAGE->requires->jquery_plugin('ui');
$PAGE->requires->jquery_plugin('ui-css');

$PAGE->set_url('/mod/interactivequiz/edit.php', array('cmid' => $cmid));
$PAGE->set_pagelayout('course');
$PAGE->set_title(format_string($course->fullname));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($coursecontext);

echo $OUTPUT->header();

echo '<div class="interactivequiz-clear"></div>';

// QUESTION BANK
echo '<div class="interactivequiz-questionbank">';

//echo '<div class="interactivequiz-preview">';
//echo 'Preview';
//echo '</div>';

echo $OUTPUT->box_start('generalbox interactivequiz-editlayout');
echo get_string('category').': ';
echo '<select class="interactivequiz-questionbank-category">';
$categories = $DB->get_records('question_categories');
foreach($categories as $category) {
    echo '<option value="'.$category->id.'">'.$category->name.'</option>';
}
echo '</select>';

echo '<div class="interactivequiz-questionbank-questions"></div>';

echo $OUTPUT->box_end();
echo '</div>';

echo '<div class="interactivequiz-spacer"></div>';

// MAIN CANVAS
echo '<div class="interactivequiz-builder"></div>';

echo $OUTPUT->footer();
