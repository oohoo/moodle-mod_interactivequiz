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

global $DB;

$logs = array(
    array('module'=>'interactivequiz', 'action'=>'add', 'mtable'=>'interactivequiz', 'field'=>'name'),
    array('module'=>'interactivequiz', 'action'=>'update', 'mtable'=>'interactivequiz', 'field'=>'name'),
    array('module'=>'interactivequiz', 'action'=>'view', 'mtable'=>'interactivequiz', 'field'=>'name'),
    array('module'=>'interactivequiz', 'action'=>'view all', 'mtable'=>'interactivequiz', 'field'=>'name')
);
