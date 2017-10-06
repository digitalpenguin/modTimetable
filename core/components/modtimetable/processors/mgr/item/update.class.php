<?php
/**
 * Update an Item
 * 
 * @package modtimetable
 * @subpackage processors
 */

class modTimetableItemUpdateProcessor extends modObjectUpdateProcessor {
    public $classKey = 'modTimetableItem';
    public $languageTopics = array('modtimetable:default');
    public $objectType = 'modtimetable.item';

    public function beforeSet() {
        $name = $this->getProperty('name');

        if (empty($name)) {
            $this->addFieldError('name',$this->modx->lexicon('modtimetable.err.item_name_ns'));

        } else if ($this->modx->getCount($this->classKey, array('name' => $name)) && ($this->object->name != $name)) {
            $this->addFieldError('name',$this->modx->lexicon('modtimetable.err.item_name_ae'));
        }
        return parent::beforeSet();
    }

}
return 'modTimetableItemUpdateProcessor';