<?php
/**
 * Create a Day
 * 
 * @package modtimetable
 * @subpackage processors
 */
class modTimetableDayCreateProcessor extends modObjectCreateProcessor {
    public $classKey = 'modTimetableDay';
    public $languageTopics = array('modtimetable:default');
    public $objectType = 'modtimetable.day';
    public $timetableId;

    public function initialize() {
        if(!$this->getProperty('timetableId')) return 'Unable to find Timetable id';
        $this->timetableId = $this->getProperty('timetableId');
        $this->modx->log(1,'tt id: '.$this->getProperty('timetableId'));
        return parent::initialize();
    }

    public function beforeSet(){
        $items = $this->modx->getCollection($this->classKey);
        $this->setProperty('position', count($items));
        $this->setProperty('timetable_id',$this->timetableId);
        $this->setProperty('num_in_week',1);
        $this->modx->log(1,print_r($this->getProperties(),true));
        return parent::beforeSet();
    }

    public function beforeSave() {
        $name = $this->getProperty('name');
        if (empty($name)) {
            $this->addFieldError('name',$this->modx->lexicon('modtimetable.err.day_name_ns'));
        }
        return parent::beforeSave();
    }
}
return 'modTimetableDayCreateProcessor';
