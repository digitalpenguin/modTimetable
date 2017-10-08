<?php
/**
 * @package modtimetable
 */
$xpdo_meta_map['modTimetableTimetable']= array (
  'package' => 'modtimetable',
  'version' => '0.1',
  'table' => 'modtimetable_timetables',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'name' => '',
    'description' => '',
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
    'modTimetableDay' => 
    array (
      'class' => 'modTimetableDay',
      'local' => 'id',
      'foreign' => 'timetable_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
