<?php
/**
 * @package modtimetable
 */
$xpdo_meta_map['modTimetableDay']= array (
  'package' => 'modtimetable',
  'version' => '1.1',
  'table' => 'modtimetable_days',
  'extends' => 'modTimetableObject',
  'fields' => 
  array (
    'timetable_id' => NULL,
  ),
  'fieldMeta' => 
  array (
    'timetable_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
    ),
  ),
  'indexes' => 
  array (
    'timetable_id' => 
    array (
      'alias' => 'timetable_id',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'timetable_id' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
  'composites' => 
  array (
    'TimetableSession' => 
    array (
      'class' => 'modTimetableSession',
      'local' => 'id',
      'foreign' => 'day_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
  'aggregates' => 
  array (
    'TimetableTimetable' => 
    array (
      'class' => 'modTimetableTimetable',
      'local' => 'timetable_id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
