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

        if (empty($name)) {
            $this->addFieldError('name',$this->modx->lexicon('modtimetable.err.session_name_ns'));
        } else if ($this->doesAlreadyExist(array('name' => $name))) {
            $this->addFieldError('name',$this->modx->lexicon('modtimetable.err.session_name_ae'));
        }
        return parent::beforeSave();
    }
}
return 'modTimetableSessionCreateProcessor';
