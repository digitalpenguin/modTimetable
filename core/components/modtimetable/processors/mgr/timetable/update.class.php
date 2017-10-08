<?php
/**
 * Update a Timetable
 * 
 * @package modtimetable
 * @subpackage processors
 */

class modTimetableTimetableUpdateProcessor extends modObjectUpdateProcessor {
    public $classKey = 'modTimetableTimetable';
    public $languageTopics = array('modtimetable:default');
    public $objectType = 'modtimetable.timetable';

    public function beforeSet() {
        $name = $this->getProperty('name');

        if (empty($name)) {
            $this->addFieldError('name',$this->modx->lexicon('modtimetable.err.timetable_name_ns'));

        } else if ($this->modx->getCount($this->classKey, array('name' => $name)) && ($this->object->name != $name)) {
            $this->addFieldError('name',$this->modx->lexicon('modtimetable.err.timetable_name_ae'));
        }
        return parent::beforeSet();
    }

}
return 'modTimetableTimetableUpdateProcessor';