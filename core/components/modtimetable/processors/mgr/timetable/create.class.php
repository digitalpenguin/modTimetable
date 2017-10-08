<?php
/**
 * Create a Timetable
 * 
 * @package modtimetable
 * @subpackage processors
 */
class modTimetableTimetableCreateProcessor extends modObjectCreateProcessor {
    public $classKey = 'modTimetableTimetable';
    public $languageTopics = array('modtimetable:default');
    public $objectType = 'modtimetable.timetable';

    public function beforeSet(){
        $timetables = $this->modx->getCollection($this->classKey);

        $this->setProperty('position', count($timetables));

        return parent::beforeSet();
    }

    public function beforeSave() {
        $name = $this->getProperty('name');

        if (empty($name)) {
            $this->addFieldError('name',$this->modx->lexicon('modtimetable.err.timetable_name_ns'));
        } else if ($this->doesAlreadyExist(array('name' => $name))) {
            $this->addFieldError('name',$this->modx->lexicon('modtimetable.err.timetable_name_ae'));
        }
        return parent::beforeSave();
    }
}
return 'modTimetableTimetableCreateProcessor';
