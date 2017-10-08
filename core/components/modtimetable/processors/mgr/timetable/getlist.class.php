<?php
/**
 * Get list Timetables
 *
 * @package modtimetable
 * @subpackage processors
 */
class modTimetableTimetableGetListProcessor extends modObjectGetListProcessor {
    public $classKey = 'modTimetableTimetable';
    public $languageTopics = array('modtimetable:default');
    public $defaultSortField = 'position';
    public $defaultSortDirection = 'ASC';
    public $objectType = 'modtimetable.timetable';

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
return 'modTimetableTimetableGetListProcessor';