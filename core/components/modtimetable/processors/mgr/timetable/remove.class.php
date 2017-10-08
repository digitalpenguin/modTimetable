<?php
/**
 * Remove a Timetable.
 * 
 * @package modtimetable
 * @subpackage processors
 */
class modTimetableTimetableRemoveProcessor extends modObjectRemoveProcessor {
    public $classKey = 'modTimetableTimetable';
    public $languageTopics = array('modtimetable:default');
    public $objectType = 'modtimetable.timetable';
}
return 'modTimetableTimetableRemoveProcessor';