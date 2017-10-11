<?php
/**
 * @package modtimetable
 */
require_once (strtr(realpath(dirname(dirname(__FILE__))), '\\', '/') . '/modtimetableextrafieldclosure.class.php');
class modTimetableExtraFieldClosure_mysql extends modTimetableExtraFieldClosure {}
?>