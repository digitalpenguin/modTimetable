<?php
/**
 * The base modTimetable snippet.
 *
 * @package modtimetable
 */
$modtimetable = $modx->getService('modtimetable','modTimetable',$modx->getOption('modtimetable.core_path',null,$modx->getOption('core_path').'components/modtimetable/').'model/modtimetable/',$scriptProperties);
if (!($modtimetable instanceof modTimetable)) return '';

$tpl = $modx->getOption('tpl',$scriptProperties,'timetableTpl');
$sortBy = $modx->getOption('sortBy',$scriptProperties,'name');
$sortDir = $modx->getOption('sortDir',$scriptProperties,'ASC');
$limit = $modx->getOption('limit',$scriptProperties,5);
$outputSeparator = $modx->getOption('outputSeparator',$scriptProperties,"\n");

$c = $modx->newQuery('modTimetableTimetable');
$c->sortby($sortBy,$sortDir);
$c->limit($limit);
$timetables = $modx->getCollection('modTimetableTimetable',$c);

$timetableList = array();
foreach ($timetables as $timetable) {
    $timetableArray = $timetable->toArray();
    $timetableList[] = $modx->getChunk($tpl,$timetableArray);
}

$dayList = array();
foreach ($timetableList as $timetable) {
    $c = $modx->newQuery('modTimetableDay');
    $c->where(array(
        'timetable_id'  =>  $timetable['id']
    ));
    $c->sortby($sortBy,$sortDir);
    $c->limit($limit);
    $days = $modx->getCollection('modTimetableDay',$c);
    foreach($days as $day){
        $dayArray = $day->toArray();
        $dayList[] = $modx->getChunk('dayTpl',$dayArray);
        $timetableList[][] = $dayList;
    }
}
echo '<pre>';
print_r($timetableList);

$output = implode($outputSeparator,$timetableList);
$toPlaceholder = $modx->getOption('toPlaceholder',$scriptProperties,false);
if (!empty($toPlaceholder)) {
    /* if using a placeholder, output nothing and set output to specified placeholder */
    $modx->setPlaceholder($toPlaceholder,$output);
    return '';
}

return $output;