<?php

$modtimetable = $modx->getService('modtimetable','modTimetable',$modx->getOption('modtimetable.core_path',null,$modx->getOption('core_path').'components/modtimetable/').'model/modtimetable/',$scriptProperties);
if (!($modtimetable instanceof modTimetable)) return '';

$dayTpl = $modx->getOption('dayTpl',$scriptProperties,'dayTpl');
$sessionTpl = $modx->getOption('sessionTpl',$scriptProperties,'sessionTpl');
$sortBy = $modx->getOption('sortBy',$scriptProperties,'position');
$sortDir = $modx->getOption('sortDir',$scriptProperties,'ASC');

// Grab the days within each timetable
$c = $modx->newQuery('modTimetableDay');
$c->sortby($sortBy,$sortDir);
$c->where(array('timetable_id'=>1));
$days = $modx->getCollection('modTimetableDay',$c);
$dayArray = array();
$dayList = array();

foreach($days as $day) {
    $dayArray = $day->toArray();

    // Grab the sessions within each day
    $c = $modx->newQuery('modTimetableSession');
    $c->sortby($sortBy, $sortDir);
    $c->where(array('day_id' => $dayArray['id']));
    $sessions = $modx->getCollection('modTimetableSession', $c);

    $sessionArray = array();
    $sessionList = array();
    foreach ($sessions as $session) {
        $sessionArray = $session->toArray();
        $sessionList[] = $modx->getChunk($sessionTpl, $sessionArray);
    }
    // Grab the day_id from the last iteration of the $sessionArray as they'll all be the same.
    // We can then compare it with the current id of the day so we only get these sessions on the correct day.
    if ($dayArray['id'] === $sessionArray['day_id']) {
        $modx->setPlaceholder('sessions', implode($sessionList));
    } else {
        $modx->setPlaceholder('sessions', '');
    }
    $dayList[] = $modx->getChunk($dayTpl, $dayArray);
}

$output = implode($outputSeparator,$dayList);
return $output;