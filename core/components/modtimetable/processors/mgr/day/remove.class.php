<?php
/**
 * Remove an Day.
 * 
 * @package modtimetable
 * @subpackage processors
 */
class modTimetableDayRemoveProcessor extends modObjectRemoveProcessor {
    public $classKey = 'modTimetableDay';
    public $languageTopics = array('modtimetable:default');
    public $objectType = 'modtimetable.day';
}
return 'modTimetableDayRemoveProcessor';