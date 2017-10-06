<?php
/**
 * Get list Items
 *
 * @package modtimetable
 * @subpackage processors
 */
class modTimetableItemGetListProcessor extends modObjectGetListProcessor {
    public $classKey = 'modTimetableItem';
    public $languageTopics = array('modtimetable:default');
    public $defaultSortField = 'position';
    public $defaultSortDirection = 'ASC';
    public $objectType = 'modtimetable.item';

    public function prepareQueryBeforeCount(xPDOQuery $c) {
        $query = $this->getProperty('query');
        if (!empty($query)) {
            $c->where(array(
                    'name:LIKE' => '%'.$query.'%',
                    'OR:description:LIKE' => '%'.$query.'%',
                ));
        }
        return $c;
    }
}
return 'modTimetableItemGetListProcessor';