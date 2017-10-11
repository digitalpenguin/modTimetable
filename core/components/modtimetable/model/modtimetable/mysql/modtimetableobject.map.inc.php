<?php
/**
 * @package modtimetable
 */
$xpdo_meta_map['modTimetableObject']= array (
  'package' => 'modtimetable',
  'version' => '1.1',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'name' => '',
    'description' => '',
    'image' => NULL,
    'active' => NULL,
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
    'active' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'integer',
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
    'ExtraFieldClosure' => 
    array (
      'class' => 'modTimetableExtraFieldClosure',
      'local' => 'id',
      'foreign' => 'object_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
