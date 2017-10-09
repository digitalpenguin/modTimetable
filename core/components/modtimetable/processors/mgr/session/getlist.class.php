<?php
/**
 * Get list of Sessions
 *
 * @package modtimetable
 * @subpackage processors
 */
class modTimetableSessionGetListProcessor extends modObjectGetListProcessor {
    public $classKey = 'modTimetableSession';
    public $languageTopics = array('modtimetable:default');
    public $defaultSortField = 'position';
    public $defaultSortDirection = 'ASC';
    public $objectType = 'modtimetable.session';

    public function initialize() {
        if(!$this->getProperty('dayId')) return 'Failed to find day id#';
        return parent::initialize();
    }

    public function prepareQueryBeforeCount(xPDOQuery $c) {
        $c->where(array(
            'day_id'   =>  $this->getProperty('dayId')
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
return 'modTimetableSessionGetListProcessor';