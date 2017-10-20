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
    public $dayId;

    public function initialize() {
        $this->dayId = $this->getProperty('dayId');
        return parent::initialize();
    }

    public function beforeSet() {
        $name = $this->getProperty('name');
        if (empty($name)) {
            $this->addFieldError('name',$this->modx->lexicon('modtimetable.err.session_name_ns'));
        }
        $startTime = $this->getProperty('start_time');
        if (empty($startTime)) {
            $this->addFieldError('start_time',$this->modx->lexicon('modtimetable.err.session_start_time_ns'));
        } else if ($this->doesAlreadyExist(array('start_time' => $startTime,'day_id' => $this->dayId,'id:!=' => $this->object->get('id')))) {
            $this->addFieldError('start_time',$this->modx->lexicon('modtimetable.err.session_start_time_ae'));
            return $this->modx->lexicon('modtimetable.err.session_start_time_ae');
        }
        return parent::beforeSet();
    }
}
return 'modTimetableSessionUpdateProcessor';