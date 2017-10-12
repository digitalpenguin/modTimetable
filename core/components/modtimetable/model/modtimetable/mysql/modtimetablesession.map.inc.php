<?php
/**
 * @package modtimetable
 */
$xpdo_meta_map['modTimetableSession']= array (
  'package' => 'modtimetable',
  'version' => '1.1',
  'table' => 'modtimetable_sessions',
  'extends' => 'modTimetableObject',
  'fields' => 
  array (
    'day_id' => NULL,
    'start_time' => NULL,
    'end_time' => NULL,
    'teacher' => NULL,
  ),
  'fieldMeta' => 
  array (
    'day_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
    ),
    'start_time' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '100',
      'phptype' => 'string',
      'null' => true,
    ),
    'end_time' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '100',
      'phptype' => 'string',
      'null' => true,
    ),
    'teacher' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '100',
      'phptype' => 'string',
      'null' => true,
    ),
  ),
  'indexes' => 
  array (
    'day_id' => 
    array (
      'alias' => 'day_id',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'day_id' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
  'aggregates' => 
  array (
    'TimetableDay' => 
    array (
      'class' => 'modTimetableDay',
      'local' => 'day_id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
