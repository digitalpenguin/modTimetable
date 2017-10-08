<?php
/**
 * @package modtimetable
 */
$xpdo_meta_map['modTimetableSessionType']= array (
  'package' => 'modtimetable',
  'version' => '0.1',
  'table' => 'modtimetable_sessiontypes',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'name' => '',
    'description' => '',
    'start_time' => NULL,
    'end_time' => NULL,
    'image' => NULL,
    'position' => NULL,
  ),
  'fieldMeta' => 
  array (
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
  'composites' => 
  array (
    'modTimetableSession' => 
    array (
      'class' => 'modTimetableSession',
      'local' => 'id',
      'foreign' => 'sessiontype_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
