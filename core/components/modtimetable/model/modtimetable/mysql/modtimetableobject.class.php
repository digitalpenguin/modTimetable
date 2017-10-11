<?php
/**
 * @package modtimetable
 */
require_once (strtr(realpath(dirname(dirname(__FILE__))), '\\', '/') . '/modtimetableobject.class.php');
class modTimetableObject_mysql extends modTimetableObject {}
?>