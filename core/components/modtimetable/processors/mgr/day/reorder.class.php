<?php
/**
 * Reorder Days
 *
 * @package modtimetable
 * @subpackage processors
 */
class modTimetableReorderDayUpdateProcessor extends modObjectProcessor {
    public $classKey = 'modTimetableDay';
    public $languageTopics = array('modtimetable:default');
    public $objectType = 'modtimetable.day';

    public function process(){
        $idItem = $this->getProperty('idItem');
        $oldIndex = $this->getProperty('oldIndex');
        $newIndex = $this->getProperty('newIndex');
        $timetableId = $this->getProperty('timetableId');

        $items = $this->modx->newQuery($this->classKey);
        $items->where(array(
                "id:!=" => $idItem,
                "position:>=" => min($oldIndex, $newIndex),
                "position:<=" => max($oldIndex, $newIndex),
                'timetable_id:=' => $timetableId
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
return 'modTimetableReorderDayUpdateProcessor';
