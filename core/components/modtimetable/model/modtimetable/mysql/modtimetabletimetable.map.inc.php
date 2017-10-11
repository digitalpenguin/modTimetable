<?php
/**
 * @package modtimetable
 */
$xpdo_meta_map['modTimetableTimetable']= array (
  'package' => 'modtimetable',
  'version' => '1.1',
  'table' => 'modtimetable_timetables',
  'extends' => 'modTimetableObject',
  'fields' => 
  array (
  ),
  'fieldMeta' => 
  array (
  ),
  'composites' => 
  array (
    'TimetableDay' => 
    array (
      'class' => 'modTimetableDay',
      'local' => 'id',
      'foreign' => 'timetable_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
