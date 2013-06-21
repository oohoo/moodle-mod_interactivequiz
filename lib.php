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

defined('MOODLE_INTERNAL') || die();

/** example constant */
//define('interactivequiz_ULTIMATE_ANSWER', 42);

////////////////////////////////////////////////////////////////////////////////
// Moodle core API                                                            //
////////////////////////////////////////////////////////////////////////////////

/**
 * Returns the information on whether the module supports a feature
 *
 * @see plugin_supports() in lib/moodlelib.php
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed true if the feature is supported, null if unknown
 */
function interactivequiz_supports($feature) {
    switch($feature) {
        case FEATURE_MOD_INTRO:         return true;
        default:                        return null;
    }
}

/**
 * Saves a new instance of the interactivequiz into the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param object $interactivequiz An object from the form in mod_form.php
 * @param mod_interactivequiz_mod_form $mform
 * @return int The id of the newly inserted interactivequiz record
 */
function interactivequiz_add_instance(stdClass $interactivequiz, mod_interactivequiz_mod_form $mform = null) {
    global $DB;

    $interactivequiz->timecreated = time();

    # You may have to add extra stuff in here #

    return $DB->insert_record('interactivequiz', $interactivequiz);
}

/**
 * Updates an instance of the interactivequiz in the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param object $interactivequiz An object from the form in mod_form.php
 * @param mod_interactivequiz_mod_form $mform
 * @return boolean Success/Fail
 */
function interactivequiz_update_instance(stdClass $interactivequiz, mod_interactivequiz_mod_form $mform = null) {
    global $DB;

    $interactivequiz->timemodified = time();
    $interactivequiz->id = $interactivequiz->instance;

    # You may have to add extra stuff in here #

    return $DB->update_record('interactivequiz', $interactivequiz);
}

/**
 * Removes an instance of the interactivequiz from the database
 *
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 */
function interactivequiz_delete_instance($id) {
    global $DB;

    if (! $interactivequiz = $DB->get_record('interactivequiz', array('id' => $id))) {
        return false;
    }

    # Delete any dependent records here #

    $DB->delete_records('interactivequiz', array('id' => $interactivequiz->id));

    return true;
}

/**
 * Returns a small object with summary information about what a
 * user has done with a given particular instance of this module
 * Used for user activity reports.
 * $return->time = the time they did it
 * $return->info = a short text description
 *
 * @return stdClass|null
 */
function interactivequiz_user_outline($course, $user, $mod, $interactivequiz) {

    $return = new stdClass();
    $return->time = 0;
    $return->info = '';
    return $return;
}

/**
 * Prints a detailed representation of what a user has done with
 * a given particular instance of this module, for user activity reports.
 *
 * @param stdClass $course the current course record
 * @param stdClass $user the record of the user we are generating report for
 * @param cm_info $mod course module info
 * @param stdClass $interactivequiz the module instance record
 * @return void, is supposed to echp directly
 */
function interactivequiz_user_complete($course, $user, $mod, $interactivequiz) {
}

/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in interactivequiz activities and print it out.
 * Return true if there was output, or false is there was none.
 *
 * @return boolean
 */
function interactivequiz_print_recent_activity($course, $viewfullnames, $timestart) {
    return false;  //  True if anything was printed, otherwise false
}

/**
 * Prepares the recent activity data
 *
 * This callback function is supposed to populate the passed array with
 * custom activity records. These records are then rendered into HTML via
 * {@link interactivequiz_print_recent_mod_activity()}.
 *
 * @param array $activities sequentially indexed array of objects with the 'cmid' property
 * @param int $index the index in the $activities to use for the next record
 * @param int $timestart append activity since this time
 * @param int $courseid the id of the course we produce the report for
 * @param int $cmid course module id
 * @param int $userid check for a particular user's activity only, defaults to 0 (all users)
 * @param int $groupid check for a particular group's activity only, defaults to 0 (all groups)
 * @return void adds items into $activities and increases $index
 */
function interactivequiz_get_recent_mod_activity(&$activities, &$index, $timestart, $courseid, $cmid, $userid=0, $groupid=0) {
}

/**
 * Prints single activity item prepared by {@see interactivequiz_get_recent_mod_activity()}
 *
 * @return void
 */
function interactivequiz_print_recent_mod_activity($activity, $courseid, $detail, $modnames, $viewfullnames) {
}

/**
 * Function to be run periodically according to the moodle cron
 * This function searches for things that need to be done, such
 * as sending out mail, toggling flags etc ...
 *
 * @return boolean
 * @todo Finish documenting this function
 **/
function interactivequiz_cron () {
    return true;
}

/**
 * Returns all other caps used in the module
 *
 * @example return array('moodle/site:accessallgroups');
 * @return array
 */
function interactivequiz_get_extra_capabilities() {
    return array();
}

////////////////////////////////////////////////////////////////////////////////
// Gradebook API                                                              //
////////////////////////////////////////////////////////////////////////////////

/**
 * Is a given scale used by the instance of interactivequiz?
 *
 * This function returns if a scale is being used by one interactivequiz
 * if it has support for grading and scales. Commented code should be
 * modified if necessary. See forum, glossary or journal modules
 * as reference.
 *
 * @param int $interactivequizid ID of an instance of this module
 * @return bool true if the scale is used by the given interactivequiz instance
 */
function interactivequiz_scale_used($interactivequizid, $scaleid) {
    global $DB;

    /** @example */
    if ($scaleid and $DB->record_exists('interactivequiz', array('id' => $interactivequizid, 'grade' => -$scaleid))) {
        return true;
    } else {
        return false;
    }
}

/**
 * Checks if scale is being used by any instance of interactivequiz.
 *
 * This is used to find out if scale used anywhere.
 *
 * @param $scaleid int
 * @return boolean true if the scale is used by any interactivequiz instance
 */
function interactivequiz_scale_used_anywhere($scaleid) {
    global $DB;

    /** @example */
    if ($scaleid and $DB->record_exists('interactivequiz', array('grade' => -$scaleid))) {
        return true;
    } else {
        return false;
    }
}

/**
 * Creates or updates grade item for the give interactivequiz instance
 *
 * Needed by grade_update_mod_grades() in lib/gradelib.php
 *
 * @param stdClass $interactivequiz instance object with extra cmidnumber and modname property
 * @return void
 */
function interactivequiz_grade_item_update(stdClass $interactivequiz) {
    global $CFG;
    require_once($CFG->libdir.'/gradelib.php');

    /** @example */
    $item = array();
    $item['itemname'] = clean_param($interactivequiz->name, PARAM_NOTAGS);
    $item['gradetype'] = GRADE_TYPE_VALUE;
    $item['grademax']  = $interactivequiz->grade;
    $item['grademin']  = 0;

    grade_update('mod/interactivequiz', $interactivequiz->course, 'mod', 'interactivequiz', $interactivequiz->id, 0, null, $item);
}

/**
 * Update interactivequiz grades in the gradebook
 *
 * Needed by grade_update_mod_grades() in lib/gradelib.php
 *
 * @param stdClass $interactivequiz instance object with extra cmidnumber and modname property
 * @param int $userid update grade of specific user only, 0 means all participants
 * @return void
 */
function interactivequiz_update_grades(stdClass $interactivequiz, $userid = 0) {
    global $CFG, $DB;
    require_once($CFG->libdir.'/gradelib.php');

    /** @example */
    $grades = array(); // populate array of grade objects indexed by userid

    grade_update('mod/interactivequiz', $interactivequiz->course, 'mod', 'interactivequiz', $interactivequiz->id, 0, $grades);
}

////////////////////////////////////////////////////////////////////////////////
// File API                                                                   //
////////////////////////////////////////////////////////////////////////////////

/**
 * Returns the lists of all browsable file areas within the given module context
 *
 * The file area 'intro' for the activity introduction field is added automatically
 * by {@link file_browser::get_file_info_context_module()}
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @return array of [(string)filearea] => (string)description
 */
function interactivequiz_get_file_areas($course, $cm, $context) {
    return array();
}

/**
 * File browsing support for interactivequiz file areas
 *
 * @package mod_interactivequiz
 * @category files
 *
 * @param file_browser $browser
 * @param array $areas
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @param string $filearea
 * @param int $itemid
 * @param string $filepath
 * @param string $filename
 * @return file_info instance or null if not found
 */
function interactivequiz_get_file_info($browser, $areas, $course, $cm, $context, $filearea, $itemid, $filepath, $filename) {
    return null;
}

/**
 * Serves the files from the interactivequiz file areas
 *
 * @package mod_interactivequiz
 * @category files
 *
 * @param stdClass $course the course object
 * @param stdClass $cm the course module object
 * @param stdClass $context the interactivequiz's context
 * @param string $filearea the name of the file area
 * @param array $args extra arguments (itemid, path)
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 */
function interactivequiz_pluginfile($course, $cm, $context, $filearea, array $args, $forcedownload, array $options=array()) {
    global $DB, $CFG;

    if ($context->contextlevel != CONTEXT_MODULE) {
        send_file_not_found();
    }

    require_login($course, true, $cm);

    send_file_not_found();
}

////////////////////////////////////////////////////////////////////////////////
// Navigation API                                                             //
////////////////////////////////////////////////////////////////////////////////

/**
 * Extends the global navigation tree by adding interactivequiz nodes if there is a relevant content
 *
 * This can be called by an AJAX request so do not rely on $PAGE as it might not be set up properly.
 *
 * @param navigation_node $navref An object representing the navigation tree node of the interactivequiz module instance
 * @param stdClass $course
 * @param stdClass $module
 * @param cm_info $cm
 */
function interactivequiz_extend_navigation(navigation_node $navref, stdclass $course, stdclass $module, cm_info $cm) {
}

/**
 * Extends the settings navigation with the interactivequiz settings
 *
 * This function is called when the context for the page is a interactivequiz module. This is not called by AJAX
 * so it is safe to rely on the $PAGE.
 *
 * @param settings_navigation $settingsnav {@link settings_navigation}
 * @param navigation_node $interactivequiznode {@link navigation_node}
 */
function interactivequiz_extend_settings_navigation(settings_navigation $settingsnav, navigation_node $interactivequiznode=null) {
    global $PAGE;

    // We want to add these new nodes after the Edit settings node, and before the
    // Locally assigned roles node. Of course, both of those are controlled by capabilities.
    $keys = $interactivequiznode->get_children_key_list();

    $beforekey = null;
    $i = array_search('modedit', $keys);
    if ($i === false and array_key_exists(0, $keys)) {
        $beforekey = $keys[0];
    } else if (array_key_exists($i + 1, $keys)) {
        $beforekey = $keys[$i + 1];
    }

    if (has_capability('mod/interactivequiz:manage', $PAGE->cm->context)) {
        $node = navigation_node::create(get_string('editquiz', 'interactivequiz'),
                new moodle_url('/mod/interactivequiz/edit.php', array('cmid'=>$PAGE->cm->id)),
                navigation_node::TYPE_SETTING, null, 'mod_interactivequiz_edit',
                new pix_icon('t/edit', ''));
        $interactivequiznode->add_node($node, $beforekey);
    }
}
