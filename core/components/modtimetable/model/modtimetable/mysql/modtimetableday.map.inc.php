<?php
/**
 * @package modtimetable
 */
$xpdo_meta_map['modTimetableDay']= array (
  'package' => 'modtimetable',
  'version' => '0.1',
  'table' => 'modtimetable_days',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'timetable_id' => NULL,
    'num_in_week' => NULL,
    'name' => '',
    'description' => '',
    'image' => NULL,
    'position' => NULL,
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
    'num_in_week' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
    ),
    'name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '100',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'description' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'text',
      'null' => false,
      'default' => '',
    ),
    'image' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => true,
    ),
    'position' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => true,
    ),
  ),
  'composites' => 
  array (
    'modTimetableSession' => 
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
    'modTimetableTimetable' => 
    array (
      'class' => 'modTimetableTimetable',
      'local' => 'timetable_id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
