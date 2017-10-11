<?php
/**
 * @package modtimetable
 */
$xpdo_meta_map['modTimetableExtraField']= array (
  'package' => 'modtimetable',
  'version' => '1.1',
  'table' => 'modtimetable_extra_fields',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'key' => '',
    'name' => '',
    'value' => '',
  ),
  'fieldMeta' => 
  array (
    'key' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '100',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '100',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'value' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'text',
      'null' => false,
      'default' => '',
    ),
  ),
  'indexes' => 
  array (
    'key' => 
    array (
      'alias' => 'key',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'key' => 
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
