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

require_once(dirname(dirname(dirname(__FILE__))).'/lib/questionlib.php');

$cmid = required_param('cmid', PARAM_INT);   // course module ID
$query = required_param('query', PARAM_TEXT);
$start = optional_param('start', 0, PARAM_INT);

$cm = get_coursemodule_from_id('interactivequiz', $cmid, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$interactivequiz  = $DB->get_record('interactivequiz', array('id' => $cm->instance), '*',
    MUST_EXIST);

require_login($course, true, $cm);
$context = get_context_instance(CONTEXT_MODULE, $cm->id);

require_capability('mod/interactivequiz:manage', $context);

$PAGE->set_context($context);




if($query == 'category') {
    $category = required_param('category', PARAM_INT);
    category($category);
} else if($query == 'builder') {
    builder();
} else if($query == 'addquestion') {
    $questionid = required_param('question', PARAM_INT);
    $order = required_param('order', PARAM_INT);

    $iquestions = $DB->get_records_select('interactivequiz_questions',
        'interactivequiz_id = ? AND question_order >= ?', array($cm->id, $order));
    // Bump the existing ones up
    foreach($iquestions as $iquestion) {
        $DB->set_field('interactivequiz_questions', 'question_order', $iquestion->question_order+1,
            array('id' => $iquestion->id));
    }

    $question = new stdClass();
    $question->interactivequiz_id = $cm->id;
    $question->question_id = $questionid;
    $question->question_order = $order;
    $question->top_level = 1;
    $DB->insert_record('interactivequiz_questions', $question);
} else if($query == 'deletequestion') {
    $iquestionid = required_param('iquestion', PARAM_INT);
    $DB->delete_records('interactivequiz_questions', array('id' => $iquestionid));
    // Reorder records
    $iquestions = $DB->get_records('interactivequiz_questions', array('interactivequiz_id' => $cm->id));
    $count = 1;
    foreach($iquestions as $iquestion) {
        $DB->set_field('interactivequiz_questions', 'question_order', $count++,
            array('id' => $iquestion->id));
    }

    // Delete answers
    $answers = $DB->get_records('interactivequiz_answers',
        array('interactivequiz_question_from' => $iquestionid));
    foreach($answers as $answer) {
        $DB->delete_records('interactivequiz_questions',
            array('id' => $answer->interactivequiz_question_next));
        $DB->delete_records('interactivequiz_answers', array('id' => $answer->id));
    }
} else if($query == 'addsubquestion') {
    $questionid = required_param('question', PARAM_INT);
    $answerid = required_param('answer', PARAM_INT);
    $iquestionid = required_param('iquestion', PARAM_INT);

    // First add the sub question
    $question = new stdClass();
    $question->interactivequiz_id = $cm->id;
    $question->question_id = $questionid;
    $question->question_order = 0;
    $question->top_level = 0;
    $nextid = $DB->insert_record('interactivequiz_questions', $question);

    // Link the subquestion via an answer
    $answer = new stdClass();
    $answer->interactivequiz_question_from = $iquestionid;
    $answer->interactivequiz_question_next = $nextid;
    $answer->question_answer_id = $answerid;
    $DB->insert_record('interactivequiz_answers', $answer);
}

function builder() {
    global $DB, $OUTPUT, $cm;
    $quiz_questions = $DB->get_records('interactivequiz_questions',
        array('interactivequiz_id' => $cm->id, 'top_level' => 1), 'question_order ASC');

    // Top placeholder
    $placeholder_number = 1;

    echo '<div class="interactivequiz-builder-placeholder ';
    if(count($quiz_questions) == 0) {
        echo 'interactivequiz-builder-placeholder-highlight';
    }
    echo '" data-order="'.
        $placeholder_number++.'">';
    echo get_string('dragquestionhere', 'interactivequiz');
    echo '</div>';

    foreach($quiz_questions as $quiz_question) {
        $question = $DB->get_record('question', array('id' => $quiz_question->question_id));
        echo '<div class="interactivequiz-builder-question">';
        echo '<div class="interactivequiz-builder-question-name">';
        echo $question->name;
        echo '</div>';
        echo '<div class="interactivequiz-builder-question-text">';
        echo $question->questiontext;
        echo '</div>';

        // Answers
        $answers = $DB->get_records('question_answers', array('question' => $question->id));
        echo '<div class="interactivequiz-builder-question-answers">';
        echo get_string('answers', 'interactivequiz');
        echo '</div>';

        $i = 1;
        foreach($answers as $answer) {
            echo '<div class="interactivequiz-builder-question-answer">';
            echo '<table>';
            echo '<tr>';
            echo '<td rowspan="2" class="interactivequiz-builder-question-answer-number">';
            echo $i;
            echo '</td>';
            echo '<td class="interactivequiz-builder-question-answer-label">';
            if($answer->fraction == 1) {
                echo '<span class="interactivequiz-builder-question-answer-correct">';
                echo get_string('correctanswer', 'interactivequiz');
                echo '</span>';
            }
            echo '</td><td class="interactivequiz-builder-question-answer-text">';
            if($question->qtype == 'mathexpression') {
                echo '\\(';
            }
            echo $answer->answer;
            if($question->qtype == 'mathexpression') {
                echo '\\)';
            }
            echo '</td>';
            echo '</tr>';
            echo '<tr>';
            $answer_subquestion = $DB->get_record('interactivequiz_answers',
                array('interactivequiz_question_from' => $quiz_question->id,
                    'question_answer_id' => $answer->id));
            if($answer_subquestion) {
                $isubquestion = $DB->get_record('interactivequiz_questions',
                    array('id' => $answer_subquestion->interactivequiz_question_next));
                $subquestion = $DB->get_record('question', array('id' => $isubquestion->question_id));
                
                echo '<td>';
                echo '<span class="interactivequiz-builder-question-answer-subquestion">';
                echo get_string('subquestion', 'interactivequiz');
                echo '</span>';
                $image = $OUTPUT->pix_icon('t/delete', get_string('delete'), '',
                    array('class' => 'iconsmall'));
                echo ' <a href="" class="interactivequiz-builder-question-delete" ';
                echo 'data-iquestionid="'.$isubquestion->id.'">';
                echo $image;
                echo '</a>';
                echo '</td>';

                echo '<td class="interactivequiz-builder-question-answer-subquestion-name">';
                echo $subquestion->name;
                echo '</td>';
            } else {
                echo '<td colspan="2">';
                echo '<div class="interactivequiz-builder-placeholdersmall" ';
                echo 'data-iquestionid="'.$quiz_question->id.'" data-answer="'.$answer->id.'">';
                echo get_string('dragnextquestionhere', 'interactivequiz');
                echo '</div>';
                echo '</td>';
            }

            echo '</td>';
            echo '</tr>';
            echo '</table>';
            echo '</div>';

            $i++;
        }

        // Controls
        echo '<div class="interactivequiz-builder-question-buttons">';
        $image = $OUTPUT->pix_icon('t/delete', get_string('delete'), '',
            array('class' => 'iconsmall'));
        echo '<a href="" class="interactivequiz-builder-question-delete" ';
        echo 'data-iquestionid="'.$quiz_question->id.'">';
        echo $image;
        echo '</a>';
        echo '<span class="interactivequiz-builder-question-delete-confirm">';
        echo get_string('clickagaintoconfirm', 'interactivequiz');
        echo '</span>';
        echo '</div>';
        echo '</div>';

        // Placeholder
        echo '<div class="interactivequiz-builder-placeholder" data-order="'.
            $placeholder_number++.'">';
        echo get_string('dragquestionhere', 'interactivequiz');
        echo '</div>';
    }
}

function category($category) {
    global $DB, $OUTPUT, $start, $cmid;
    $question_limit = 5;

    $questions = $DB->get_records('question', array('category' => $category), '', '*',
        $start, $question_limit);
    $question_count = $DB->count_records('question', array('category' => $category));

    foreach($questions as $question) {
        echo '<div class="interactivequiz-questionbank-question" ';
        echo 'data-questionid="'.$question->id.'">';
        echo '<div class="interactivequiz-questionbank-question-name">';
        echo $question->name;
        echo '</div>';
        echo '<div class="interactivequiz-questionbank-question-text">';
        echo $question->questiontext;
        echo '</div>';
        echo '<div class="interactivequiz-questionbank-question-buttons">';
        // Preview
        $image = $OUTPUT->pix_icon('t/preview', get_string('preview'), '',
            array('class' => 'iconsmall'));
        $link = new moodle_url('/question/preview.php',
            array('id' => $category, 'cmid' => $cmid));
        $action = new popup_action('click', $link, 'questionpreview',
            question_preview_popup_params());
        echo $OUTPUT->action_link($link, $image, $action,
            array('title' => get_string('preview'), 'target' => '_blank'));

        // Edit
        $image = $OUTPUT->pix_icon('t/edit', get_string('edit'), '',
            array('class' => 'iconsmall'));
        $link = new moodle_url('/question/question.php',
            array('id' => $category, 'cmid' => $cmid,
            'returnurl' => '/mod/interactivequiz/edit.php?cmid='.$cmid));
        $action = new component_action('click', '');
        echo $OUTPUT->action_link($link, $image, $action, array('title' => get_string('edit')));

        echo '</div>';
        echo '</div>';
    }

    echo '<div class="interactivequiz-questionbank-arrows">';

    if($question_count > $question_limit && $start != 0) {
        echo '<span data-category="'.$category.'" data-start="'.($start-$question_limit).'">';
        echo $OUTPUT->pix_icon('t/left', get_string('left', 'interactivequiz'), '',
            array('class' => 'iconsmall'));
        echo '</span>';
        echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
    }
    if($question_count > $question_limit && $start < ($question_count - $question_limit)) {
        echo '<span data-category="'.$category.'" data-start="'.($start+$question_limit).'">';
        echo $OUTPUT->pix_icon('t/right', get_string('right', 'interactivequiz'), '',
            array('class' => 'iconsmall'));
        echo '</span>';
    }
    echo '</div>';
}
