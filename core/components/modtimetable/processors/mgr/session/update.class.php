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
        } else if ($this->modx->getCount($this->classKey, array('name' => $name)) && ($this->object->name != $name)) {
            $this->addFieldError('name',$this->modx->lexicon('modtimetable.err.session_name_ae'));
        }
        return parent::beforeSet();
    }
}
return 'modTimetableSessionUpdateProcessor';