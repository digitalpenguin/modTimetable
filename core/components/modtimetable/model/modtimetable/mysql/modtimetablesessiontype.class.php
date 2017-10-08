<?php
/**
 * @package modtimetable
 */
require_once (strtr(realpath(dirname(dirname(__FILE__))), '\\', '/') . '/modtimetablesessiontype.class.php');
class modTimetableSessionType_mysql extends modTimetableSessionType {}
?>