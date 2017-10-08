<?php
/**
 * Update a Day
 * 
 * @package modtimetable
 * @subpackage processors
 */

class modTimetableDayUpdateProcessor extends modObjectUpdateProcessor {
    public $classKey = 'modTimetableDay';
    public $languageTopics = array('modtimetable:default');
    public $objectType = 'modtimetable.day';

    public function beforeSet() {
        $name = $this->getProperty('name');
        if (empty($name)) {
            $this->addFieldError('name',$this->modx->lexicon('modtimetable.err.day_name_ns'));
        } else if ($this->modx->getCount($this->classKey, array('name' => $name)) && ($this->object->name != $name)) {
            $this->addFieldError('name',$this->modx->lexicon('modtimetable.err.day_name_ae'));
        }
        return parent::beforeSet();
    }
}
return 'modTimetableDayUpdateProcessor';