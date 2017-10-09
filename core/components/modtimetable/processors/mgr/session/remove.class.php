<?php
/**
 * Remove a Session.
 * 
 * @package modtimetable
 * @subpackage processors
 */
class modTimetableSessionRemoveProcessor extends modObjectRemoveProcessor {
    public $classKey = 'modTimetableSession';
    public $languageTopics = array('modtimetable:default');
    public $objectType = 'modtimetable.session';
}
return 'modTimetableSessionRemoveProcessor';