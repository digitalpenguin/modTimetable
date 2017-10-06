<?php
/**
 * Remove an Item.
 * 
 * @package modtimetable
 * @subpackage processors
 */
class modTimetableItemRemoveProcessor extends modObjectRemoveProcessor {
    public $classKey = 'modTimetableItem';
    public $languageTopics = array('modtimetable:default');
    public $objectType = 'modtimetable.item';
}
return 'modTimetableItemRemoveProcessor';