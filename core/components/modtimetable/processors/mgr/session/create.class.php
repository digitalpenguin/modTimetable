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
        $name = $this->getProperty('name');
        $startTime = $this->getProperty('start_time');

        if (empty($name)) {
            $this->addFieldError('name',$this->modx->lexicon('modtimetable.err.session_name_ns'));
        }
        if (empty($startTime)) {
            $this->addFieldError('start_time',$this->modx->lexicon('modtimetable.err.session_start_time_ns'));
        } else if ($this->doesAlreadyExist(array('start_time' => $startTime,'day_id' => $this->dayId))) {
            $this->addFieldError('start_time',$this->modx->lexicon('modtimetable.err.session_start_time_ae'));
            return $this->modx->lexicon('modtimetable.err.session_start_time_ae');
        }

        // Count current number of sessions for this day
        $sessions = $this->modx->getCollection($this->classKey, array(
            'day_id'  =>  $this->dayId
        ));
        $this->setProperty('position', count($sessions));
        $this->setProperty('day_id',$this->dayId);
        return parent::beforeSet();
    }
}
return 'modTimetableSessionCreateProcessor';
