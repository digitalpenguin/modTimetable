<?php
/**
 * The base modTimetable snippet.
 *
 * @package modtimetable
 */
$modtimetable = $modx->getService('modtimetable','modTimetable',$modx->getOption('modtimetable.core_path',null,$modx->getOption('core_path').'components/modtimetable/').'model/modtimetable/',$scriptProperties);
if (!($modtimetable instanceof modTimetable)) return '';

$timetableTpl = $modx->getOption('timetableTpl',$scriptProperties,'timetableTpl');
$dayTpl = $modx->getOption('dayTpl',$scriptProperties,'dayTpl');
$sessionTpl = $modx->getOption('sessionTpl',$scriptProperties,'sessionTpl');
$sortBy = $modx->getOption('sortBy',$scriptProperties,'position');
$sortDir = $modx->getOption('sortDir',$scriptProperties,'ASC');
$limit = $modx->getOption('limit',$scriptProperties,10);
$outputSeparator = $modx->getOption('outputSeparator',$scriptProperties,"\n");

// Grab the main timetables
$c = $modx->newQuery('modTimetableTimetable');
$c->sortby($sortBy,$sortDir);
$c->limit($limit);
$timetables = $modx->getCollection('modTimetableTimetable',$c);
$timetableList = array();
$idx = 0;
foreach ($timetables as $timetable) {
    $timetableArray = $timetable->toArray();
    $timetableList[] = $modx->getChunk($timetableTpl,$timetableArray);

    // Grab the days within each timetable
    $c = $modx->newQuery('modTimetableDay');
    $c->sortby($sortBy,$sortDir);
    $c->where(array('timetable_id'=>$timetableArray['id']));
    $days = $modx->getCollection('modTimetableDay',$c);
    $dayArray = array();
    $dayList = array();
    $dayIdx = 0;
    foreach($days as $day) {
        $dayArray = $day->toArray();
        $dayList[] = $modx->getChunk($dayTpl,$dayArray);
        // Grab the sessions within each day
        $c = $modx->newQuery('modTimetableSession');
        $c->sortby($sortBy,$sortDir);
        $c->where(array('day_id'=>$dayArray['id']));
        $sessions = $modx->getCollection('modTimetableSession',$c);
        $sessionArray = array();
        $sessionList = array();
        foreach($sessions as $session) {
            $sessionArray = $session->toArray();
            $sessionList[] = $modx->getChunk($sessionTpl,$sessionArray);
        }
        // Grab the day_id from the last iteration of the $sessionArray as they'll all be the same.
        // We can then compare it with the current id of the day so we only get these sessions on the correct day.
        if($dayArray['id'] === $sessionArray['day_id']) {
            //$modx->setPlaceholder('sessions',implode($sessionList));
            //$dayList[$dayIdx] .= implode($sessionList);
            $modx->setPlaceholder('sessions',implode($sessionList));
        }
        $dayIdx++;
    }
    // Grab the timetable_id from the last iteration of the $dayArray as they'll all be the same.
    // We can then compare it with the current id of the timetable so we only get these days on the correct timetable.
    if($timetableArray['id'] === $dayArray['timetable_id']) {
        $modx->setPlaceholder('days',implode($dayList));
        //$timetableList[$idx] .= implode($dayList);
    }
    $idx++;
}

$output = implode($outputSeparator,$timetableList);
$toPlaceholder = $modx->getOption('toPlaceholder',$scriptProperties,false);
if (!empty($toPlaceholder)) {
    $modx->setPlaceholder($toPlaceholder,$output);
    return '';
}

return $output;