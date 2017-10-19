<?php
/**
 * Create a Session
 * 
 * @package modtimetable
 * @subpackage processors
 */
class modTimetableSessionCreateProcessor extends modObjectCreateProcessor {
    public $classKey = 'modTimetableSession';
    public $languageTopics = array('modtimetable:default');
    public $objectType = 'modtimetable.session';
    public $dayId;

    public function initialize() {
        if(!$this->getProperty('dayId')) return 'Unable to find Day id#';
        $this->dayId = $this->getProperty('dayId');
        return parent::initialize();
    }

    public function beforeSet(){
        $items = $this->modx->getCollection($this->classKey);
        $this->setProperty('position', count($items));
        $this->setProperty('day_id',$this->dayId);
        return parent::beforeSet();
    }

    public function beforeSave() {
        $name = $this->getProperty('name');
        $startTime = $this->getProperty('start_time');
        if (empty($name)) {
            $this->addFieldError('name',$this->modx->lexicon('modtimetable.err.session_name_ns'));
        }
        if (empty($startTime)) {
            $this->addFieldError('start_time',$this->modx->lexicon('modtimetable.err.session_start_time_ns'));
        }
        return parent::beforeSave();
    }
}
return 'modTimetableSessionCreateProcessor';
