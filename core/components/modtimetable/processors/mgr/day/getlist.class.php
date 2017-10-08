<?php
/**
 * Get list of Days
 *
 * @package modtimetable
 * @subpackage processors
 */
class modTimetableDayGetListProcessor extends modObjectGetListProcessor {
    public $classKey = 'modTimetableDay';
    public $languageTopics = array('modtimetable:default');
    public $defaultSortField = 'position';
    public $defaultSortDirection = 'ASC';
    public $objectType = 'modtimetable.day';

    public function initialize() {
        if(!$this->getProperty('timetableId')) return 'Failed to find Timetable ID';
        return parent::initialize();
    }

    public function prepareQueryBeforeCount(xPDOQuery $c) {
        $c->where(array(
            'timetable_id'   =>  $this->getProperty('timetableId')
        ));
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
return 'modTimetableDayGetListProcessor';