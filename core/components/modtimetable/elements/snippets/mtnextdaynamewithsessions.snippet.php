<?php
/**
 * Returns the next day name that has sessions. Starting from today
 *
 * @package modtimetable
 */
$modtimetable = $modx->getService('modtimetable','modTimetable',$modx->getOption('modtimetable.core_path',null,$modx->getOption('core_path').'components/modtimetable/').'model/modtimetable/',$scriptProperties);
if (!($modtimetable instanceof modTimetable)) return '';

return $modtimetable->getNextAvailableDay();