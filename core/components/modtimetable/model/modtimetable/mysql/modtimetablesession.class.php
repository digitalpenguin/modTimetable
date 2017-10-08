<?php
/**
 * @package modtimetable
 */
require_once (strtr(realpath(dirname(dirname(__FILE__))), '\\', '/') . '/modtimetablesession.class.php');
class modTimetableSession_mysql extends modTimetableSession {}
?>