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

/**
 * Execute interactivequiz upgrade from the given old version
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_newmodule_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager(); // loads ddl manager and xmldb classes

    return true;
}
