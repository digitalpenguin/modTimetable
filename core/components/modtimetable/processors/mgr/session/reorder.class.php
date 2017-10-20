<?php
/**
 * Reorder Sessions
 *
 * @package modtimetable
 * @subpackage processors
 */
class modTimetableReorderSessionUpdateProcessor extends modObjectProcessor {
    public $classKey = 'modTimetableSession';
    public $languageTopics = array('modtimetable:default');
    public $objectType = 'modtimetable.session';

    public function process(){
        $idItem = $this->getProperty('idItem');
        $oldIndex = $this->getProperty('oldIndex');
        $newIndex = $this->getProperty('newIndex');
        $dayId = $this->getProperty('dayId');

        $items = $this->modx->newQuery($this->classKey);
        $items->where(array(
                "id:!=" => $idItem,
                "position:>=" => min($oldIndex, $newIndex),
                "position:<=" => max($oldIndex, $newIndex),
                'day_id:=' => $dayId
            ));

        $items->sortby('position', 'ASC');
        $itemsCollection = $this->modx->getCollection($this->classKey, $items);

        if(min($oldIndex, $newIndex) == $newIndex){
            foreach ($itemsCollection as $item) {
                $itemObject = $this->modx->getObject($this->classKey, $item->get('id'));
                $itemObject->set('position', $itemObject->get('position') + 1);
                $itemObject->save();
            }
        }else{
            foreach ($itemsCollection as $item) {
                $itemObject = $this->modx->getObject($this->classKey, $item->get('id'));
                $itemObject->set('position', $itemObject->get('position') - 1);
                $itemObject->save();
            }
        }

        $itemObject = $this->modx->getObject($this->classKey, $idItem);
        $itemObject->set('position', $newIndex);
        $itemObject->save();


        return $this->success('', $itemObject);
    }

}
return 'modTimetableReorderSessionUpdateProcessor';
