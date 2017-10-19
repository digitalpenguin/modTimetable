<?php
/**
 * Update a Session
 * 
 * @package modtimetable
 * @subpackage processors
 */

class modTimetableSessionUpdateProcessor extends modObjectUpdateProcessor {
    public $classKey = 'modTimetableSession';
    public $languageTopics = array('modtimetable:default');
    public $objectType = 'modtimetable.session';

    public function beforeSet() {
        $name = $this->getProperty('name');
        if (empty($name)) {
            $this->addFieldError('name',$this->modx->lexicon('modtimetable.err.session_name_ns'));
        }
        $startTime = $this->getProperty('start_time');
        if (empty($startTime)) {
            $this->addFieldError('start_time',$this->modx->lexicon('modtimetable.err.session_start_time_ns'));
        }
        return parent::beforeSet();
    }
}
return 'modTimetableSessionUpdateProcessor';