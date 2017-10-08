<?php
/**
 * @package modtimetable
 */
$xpdo_meta_map['modTimetableSession']= array (
  'package' => 'modtimetable',
  'version' => '0.1',
  'table' => 'modtimetable_sessions',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'day_id' => NULL,
    'sessiontype_id' => NULL,
    'name' => '',
    'description' => '',
    'start_time' => NULL,
    'end_time' => NULL,
    'image' => NULL,
    'position' => NULL,
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
    'sessiontype_id' => 
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
    'start_time' => 
    array (
      'dbtype' => 'time',
      'phptype' => 'datetime',
      'null' => true,
    ),
    'end_time' => 
    array (
      'dbtype' => 'time',
      'phptype' => 'datetime',
      'null' => true,
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
  'aggregates' => 
  array (
    'modTimetableSessionType' => 
    array (
      'class' => 'modTimetableSessionType',
      'local' => 'sessiontype_id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'modTimetableDay' => 
    array (
      'class' => 'modTimetableDay',
      'local' => 'day_id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
