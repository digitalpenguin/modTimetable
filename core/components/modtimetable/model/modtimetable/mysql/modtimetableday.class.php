<?php
/**
 * @package modtimetable
 */
require_once (strtr(realpath(dirname(dirname(__FILE__))), '\\', '/') . '/modtimetableday.class.php');
class modTimetableDay_mysql extends modTimetableDay {}
?>