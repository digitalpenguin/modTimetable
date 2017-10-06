<?php
/**
 * @package modtimetable
 */
require_once (strtr(realpath(dirname(dirname(__FILE__))), '\\', '/') . '/modtimetableitem.class.php');
class modTimetableItem_mysql extends modTimetableItem {}
?>