<?php
/**
 * @package modtimetable
 */
$xpdo_meta_map['modTimetableExtraFieldClosure']= array (
  'package' => 'modtimetable',
  'version' => '1.1',
  'table' => 'modtimetable_extra_field_closures',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'object_id' => NULL,
    'field_id' => NULL,
  ),
  'fieldMeta' => 
  array (
    'object_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
    ),
    'field_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
    ),
  ),
  'indexes' => 
  array (
    'object_id' => 
    array (
      'alias' => 'object_id',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'object_id' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'field_id' => 
    array (
      'alias' => 'field_id',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'field_id' => 
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
    'TimetableObject' => 
    array (
      'class' => 'modTimetableObject',
      'local' => 'object_id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'ExtraField' => 
    array (
      'class' => 'modTimetableExtraField',
      'local' => 'field_id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
