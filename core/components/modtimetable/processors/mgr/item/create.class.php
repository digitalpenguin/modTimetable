<?php
/**
 * Create an Item
 * 
 * @package modtimetable
 * @subpackage processors
 */
class modTimetableItemCreateProcessor extends modObjectCreateProcessor {
    public $classKey = 'modTimetableItem';
    public $languageTopics = array('modtimetable:default');
    public $objectType = 'modtimetable.item';

    public function beforeSet(){
        $items = $this->modx->getCollection($this->classKey);

        $this->setProperty('position', count($items));

        return parent::beforeSet();
    }

    public function beforeSave() {
        $name = $this->getProperty('name');

        if (empty($name)) {
            $this->addFieldError('name',$this->modx->lexicon('modtimetable.err.item_name_ns'));
        } else if ($this->doesAlreadyExist(array('name' => $name))) {
            $this->addFieldError('name',$this->modx->lexicon('modtimetable.err.item_name_ae'));
        }
        return parent::beforeSave();
    }
}
return 'modTimetableItemCreateProcessor';
