<?php
/**
 * The base modTimetable snippet.
 *
 * @package modtimetable
 */
$modtimetable = $modx->getService('modtimetable','modTimetable',$modx->getOption('modtimetable.core_path',null,$modx->getOption('core_path').'components/modtimetable/').'model/modtimetable/',$scriptProperties);
if (!($modtimetable instanceof modTimetable)) return '';

$timetables = $modx->getOption('timetables',$scriptProperties,1);
$singleDay = $modx->getOption('singleDay',$scriptProperties,0);
$timetableTpl = $modx->getOption('timetableTpl',$scriptProperties,'timetableTpl');
$dayTpl = $modx->getOption('dayTpl',$scriptProperties,'dayTpl');
$sessionTpl = $modx->getOption('sessionTpl',$scriptProperties,'sessionTpl');
$sortBy = $modx->getOption('sortBy',$scriptProperties,'position');
$sortDir = $modx->getOption('sortDir',$scriptProperties,'ASC');
$outputSeparator = $modx->getOption('outputSeparator',$scriptProperties,"\n");
$toPlaceholder = $modx->getOption('toPlaceholder',$scriptProperties,false);

return $modtimetable->getTimetables($timetables,$singleDay,$timetableTpl,$dayTpl,$sessionTpl,$sortBy,$sortDir,$outputSeparator,$toPlaceholder);