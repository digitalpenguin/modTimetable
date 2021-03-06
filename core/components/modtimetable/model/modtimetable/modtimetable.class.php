<?php

/**
 * The main modTimetable service class.
 *
 * @package modtimetable
 */
class modTimetable {
    public $modx = null;
    public $namespace = 'modtimetable';
    public $cache = null;
    public $options = array();
    public $timetableIds = array();
    public $singleDay = 0;
    public $timetableTpl = null;
    public $dayTpl = null;
    public $sessionTpl = null;
    public $headerRowTpl = null;
    public $sortby = 'position';
    public $sortdir = 'ASC';
    public $tableHeaderRow = array();
    public $tableRow = array();
    public $cellOpenTag = '<td>';
    public $cellCloseTag = '</td>';
    public $rowOpenTag = '<tr>';
    public $rowCloseTag = '</tr>';
    private $renderTimesCol = true;
    private $outputSeparator = '<br>';

    public function __construct(modX &$modx, array $options = array()) {
        $this->modx =& $modx;
        $this->namespace = $this->getOption('namespace', $options, 'modtimetable');

        $corePath = $this->getOption('core_path', $options, $this->modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/modtimetable/');
        $assetsPath = $this->getOption('assets_path', $options, $this->modx->getOption('assets_path', null, MODX_ASSETS_PATH) . 'components/modtimetable/');
        $assetsUrl = $this->getOption('assets_url', $options, $this->modx->getOption('assets_url', null, MODX_ASSETS_URL) . 'components/modtimetable/');

        $this->options = array_merge(array(
            'namespace' => $this->namespace,
            'corePath' => $corePath,
            'modelPath' => $corePath . 'model/',
            'chunksPath' => $corePath . 'elements/chunks/',
            'snippetsPath' => $corePath . 'elements/snippets/',
            'templatesPath' => $corePath . 'templates/',
            'assetsPath' => $assetsPath,
            'assetsUrl' => $assetsUrl,
            'jsUrl' => $assetsUrl . 'js/',
            'cssUrl' => $assetsUrl . 'css/',
            'connectorUrl' => $assetsUrl . 'connector.php',
            'timepicker_minute_interval' => (integer)$this->getOption('timepicker_minute_interval'),
            'timepicker_format' => $this->getOption('timepicker_format'),
            'timepicker_min_time' => $this->getOption('timepicker_min_time'),
            'timepicker_max_time' => $this->getOption('timepicker_max_time')
        ), $options);

        $this->modx->addPackage('modtimetable', $this->getOption('modelPath'));
        $this->modx->loadClass('modtimetable.modTimetableObject', $this->getOption('modelPath'));
        $this->modx->lexicon->load('modtimetable:default');
    }

    /**
     * Get a local configuration option or a namespaced system setting by key.
     *
     * @param string $key The option key to search for.
     * @param array $options An array of options that override local options.
     * @param mixed $default The default value returned if the option is not found locally or as a
     * namespaced system setting; by default this value is null.
     * @return mixed The option value or the default value specified.
     */
    public function getOption($key, $options = array(), $default = null) {
        $option = $default;
        if (!empty($key) && is_string($key)) {
            if ($options != null && array_key_exists($key, $options)) {
                $option = $options[$key];
            } elseif (array_key_exists($key, $this->options)) {
                $option = $this->options[$key];
            } elseif (array_key_exists("{$this->namespace}.{$key}", $this->modx->config)) {
                $option = $this->modx->getOption("{$this->namespace}.{$key}");
            }
        }
        return $option;
    }

    public function getTimetables($timetables, $day, $renderTable, $timetableTpl, $dayTpl,
                                  $sessionTpl,$headerRowTpl, $sortBy, $sortDir, $outputSeparator, $toPlaceholder) {
        $this->outputSeparator = $outputSeparator;

        if(!empty($timetables)) {
            $this->timetableIds = explode(",",$timetables);
        }
        $this->timetableTpl = $timetableTpl;
        $this->dayTpl = $dayTpl;
        $this->sessionTpl = $sessionTpl;
        $this->headerRowTpl = $headerRowTpl;

        $c = $this->modx->newQuery('modTimetableTimetable');
        $c->sortby($this->sortby,$this->sortdir);
        $c->where(array(
            'id:IN'=>$this->timetableIds,
            'active:='=>1
        ));
        $timetables = $this->modx->getCollection('modTimetableTimetable',$c);

        // Render sessions for single day
        if($day != null) {
            $this->singleDay=true;
            return $this->getDayOfSessionsFromManyTimetables($day,$timetables);
        }

        $timetableList = array();
        // If selected render timetable output in rows for displaying as HTML table
        if ($renderTable) {
            // if chunks not specified, load defaults for rendering table
            if($this->timetableTpl ===null) $this->timetableTpl = 'tableTimetableTpl';
            if($this->headerRowTpl ===null) $this->headerRowTpl = 'tableHeaderRowTpl';
            if($this->sessionTpl ===null) $this->sessionTpl = 'tableSessionTpl';
            foreach ($timetables as $timetable) {
                $timetableList[] = $this->renderRows($timetable);
            }
            // Convert output to a string and return.
            $output = $this->cleanup($outputSeparator,$timetableList,$toPlaceholder);
            return $output;
        }


        // if chunks not specified, load defaults for this option
        if($this->timetableTpl ===null) $this->timetableTpl = 'timetableTpl';
        if($this->dayTpl ===null) $this->dayTpl = 'dayTpl';
        if($this->sessionTpl ===null) $this->sessionTpl = 'sessionTpl';

        foreach ($timetables as $timetable) {
            $timetableArray = $timetable->toArray();
            //print_r($timetableArray);
            // Grab the days within each timetable
            $c = $this->modx->newQuery('modTimetableDay');
            $c->sortby($this->sortby,$this->sortdir);
            $c->where(array(
                'timetable_id'=>$timetableArray['id'],
                'active:='=>1
            ));
            $days = $this->modx->getCollection('modTimetableDay',$c);
            $dayArray = array();
            $dayList = array();
            foreach($days as $day) {
                $dayArray = $day->toArray();

                // Grab the sessions within each day
                $c = $this->modx->newQuery('modTimetableSession');
                $c->sortby($this->sortby,$this->sortdir);
                $c->where(array(
                    'day_id'=>$dayArray['id'],
                    'active:='=>1
                ));
                $sessions = $this->modx->getCollection('modTimetableSession',$c);
                $sessionArray = array();
                $sessionList = array();
                foreach($sessions as $session) {
                    $sessionArray = $session->toArray();
                    $sessionList[] = $this->modx->getChunk($this->sessionTpl,$sessionArray);
                }
                // Grab the day_id from the last iteration of the $sessionArray as they'll all be the same.
                // We can then compare it with the current id of the day so we only get these sessions on the correct day.
                if($dayArray['id'] === $sessionArray['day_id']) {
                    $this->modx->setPlaceholder('sessions',implode($sessionList));
                } else {
                    $this->modx->setPlaceholder('sessions','');
                }
                $dayList[] = $this->modx->getChunk($this->dayTpl,$dayArray);
            }
            // Grab the timetable_id from the last iteration of the $dayArray as they'll all be the same.
            // We can then compare it with the current id of the timetable so we only get these days on the correct timetable.
            if($timetableArray['id'] === $dayArray['timetable_id']) {
                $this->modx->setPlaceholder('days',implode($dayList));
            } else {
                $this->modx->setPlaceholder('days','');
            }
            $timetableList[] = $this->modx->getChunk($this->timetableTpl,$timetableArray);
        }
        // Convert output to string and return.
        return $this->cleanUp($outputSeparator,$timetableList,$toPlaceholder);
    }


    /**
     * Converts array of timetables to string and either returns or outputs to a placeholder.
     * @param $outputSeparator
     * @param $timetableList
     * @param $toPlaceholder
     * @return string
     */
    public function cleanUp($outputSeparator,$timetableList,$toPlaceholder) {
        $output = implode($outputSeparator,$timetableList);
        if (!empty($toPlaceholder)) {
            $this->modx->setPlaceholder($toPlaceholder,$output);
            return '';
        }
        return $output;
    }


    /**
     * Render timetables as HTML tables
     * @param $timetable
     * @return string
     */
    public function renderRows($timetable) {
        $c = $this->modx->newQuery('modTimetableDay');
        $c->sortby($this->sortby,$this->sortdir);
        $c->where(array(
            'timetable_id'=>$timetable->get('id'),
            'active:='=>1
        ));
        $days = $this->modx->getCollection('modTimetableDay',$c);
        $headerRow = '';
        // Render header for times col if enabled.
        if($this->renderTimesCol) $headerRow = '<th></th>';
        $sessionRows = array();
        $dayIdx = 0;
        // Grab array of session times that has been sorted and duplicates removed.
        $sessionTimes = $this->getSessionTimes($days);
        // Day names
        $dayNames = array();
        foreach($days as $day) {
            $dayNames[] = $day->get('name');
            $headerRow .= $this->modx->getChunk($this->headerRowTpl,$day->toArray());
            $c = $this->modx->newQuery('modTimetableSession');
            $c->sortby('start_time',$this->sortdir);
            $c->where(array(
                'day_id'=>$day->get('id'),
                'active:='=>1
            ));
            $sessions = $this->modx->getCollection('modTimetableSession',$c);
            $sessionIdx = 0;
            // Make sure column is created even if no sessions.
            if(empty($sessions)) {
                $sessionRows[$dayIdx][$sessionIdx] = array();
            }
            foreach($sessions as $session) {
                $sessionArray = $session->toArray();
                $sessionArray['day_num'] = $day->get('day_num');
                $sessionArray['day_name'] = $day->get('name');
                $sessionRows[$dayIdx][$sessionIdx] = $sessionArray;
                $sessionIdx++;
            }
            $dayIdx++;
        }

        $numOfRows = count($sessionTimes);
        $numOfCols = $dayIdx;
        // Get grid with correct dimensions and rows represented by the session times
        $grid = $this->prepareGridWithSessionTimes($sessionTimes,$dayNames);
        // Populate grid with session info added to correct coordinates.
        $grid = $this->populateGridWithSessions($grid,$sessionRows,$numOfRows,$numOfCols);
        $rows='';
        $idx = 0;
        foreach($grid as $rowArray) {
            $rows .= $this->rowOpenTag;
            // Check if time column should be rendered
            if($this->renderTimesCol) {
                $rows .= '<td class="time-td">' . $sessionTimes[$idx] . '</td>';
            }
            foreach($rowArray as $singleArray) {
                if (!empty($singleArray['name'])) {
                    $rows .= $this->modx->getChunk($this->sessionTpl, $singleArray);
                } else {
                    $rows .= '<td></td>';
                }
            }
            $rows .= $this->rowCloseTag;
            $idx++;
        }
        $this->modx->setPlaceholder('headerRow',$headerRow);
        $this->modx->setPlaceholder('sessionRows',$rows);
        $output = $this->modx->getChunk($this->timetableTpl,$timetable->toArray());
        return $output;
    }


    /**
     * Renders a view that contains all the sessions with the specified day from many timetables.
     * If day value is auto, first get results from current day, if not then next available day.
     * Current day gets a 'Today' placeholder.
     * Tomorrow gets a 'tomorrow' placeholder.
     * @param string $dayName
     * @param array $timetables
     * @return false|string
     */
    private function getDayOfSessionsFromManyTimetables($dayName,$timetables) {
        // if chunks not specified, load defaults for this option
        if($this->sessionTpl ===null) $this->sessionTpl = 'singleDaySessionTpl';
        if($this->timetableTpl ===null) $this->timetableTpl = 'timetableTpl';

        if($dayName == 'auto') {
            $dayName = $this->getNextAvailableDay();
            if($dayName === false) {
                $dayName = $this->getNextAvailableDay(true);
            }
        }
        $sessionList = array();
        foreach($timetables as $timetable) {
            $timetableArray = $timetable->toArray();
            // Grab the specified day within each timetable
            $c = $this->modx->newQuery('modTimetableDay');
            $c->sortby($this->sortby,$this->sortdir);
            $c->where(array(
                'timetable_id'  => $timetableArray['id'],
                'active:='      => 1,
                'name:='        => $dayName
            ));
            $days = $this->modx->getCollection('modTimetableDay',$c);
            foreach($days as $day) {
                $dayArray = $day->toArray();
                // Grab the sessions within each day
                $c = $this->modx->newQuery('modTimetableSession');
                $c->sortby('start_time', 'ASC');
                $c->where(array(
                    'day_id' => $dayArray['id'],
                    'active:=' => 1
                ));
                $sessions = $this->modx->getCollection('modTimetableSession', $c);
                foreach ($sessions as $session) {
                    $sessionList[] = $session;
                }
            }
        }

        // Sort sessions from all timetables by start_time
        function cmp($a, $b) {
            return strcmp($a->start_time, $b->start_time);
        }
        usort($sessionList, "cmp");

        $sessionArrays = array();
        foreach($sessionList as $session) {
            $sessionArrays[] = $this->modx->getChunk($this->sessionTpl, $session->toArray());
        }
        $output = implode($this->outputSeparator,$sessionArrays);
        if (!empty($toPlaceholder)) {
            $this->modx->setPlaceholder($toPlaceholder,$output);
            return '';
        }
        return $output;
    }

    /**
     * Returns the name of the next available day starting with today.
     * If today is not active or has no sessions, it tries the next according the the position value.
     * If the last day is not active or has no sessions, the first will return.
     *
     * This function will loop through the days once. If it doesn't find it it will return false.
     * If the $todayFound param is set to true, it won't care about today and just iterate from the first day of the week.
     * @param bool $todayFound
     * @return string
     */
    public function getNextAvailableDay($todayFound = false) {

        $dayNum = $this->getCurrentDayNum();
        $c = $this->modx->newQuery('modTimetableDay');
        $c->sortby('day_num','ASC');
        if(!empty($this->timetableIds)) { // possible fix for sql error if there are no timetables specified.
            $c->where(array(
                'timetable_id:IN' => $this->timetableIds
            ));
        }
        $days = $this->modx->getCollection('modTimetableDay',$c);
        $numOfDays = count($days);
        $idx=0;
        foreach($days as $day) {
            $idx++;
            //echo $numOfDays;
            //echo $idx;
            if($day->get('day_num') == $dayNum) {
                $todayFound = true;
                if($this->hasSessions($day)) {
                    return $day->get('name');
                }
            } else if($todayFound) {
                if($this->hasSessions($day)) {
                    return $day->get('name');
                }
            }
            if($idx == $numOfDays) {
                if($this->hasSessions($day)) {
                    return $day->get('name');
                } else {
                    return false;
                }
            }
        }
        return false;
    }

    /**
     * Checks that a day is both active and has active sessions.
     * @param $day
     * @return bool
     */
    public function hasSessions($day) {
        $c = $this->modx->newQuery('modTimetableSession');
        $c->sortby('start_time', 'ASC');
        $c->where(array(
            'day_id' => $day->get('id'),
            'active:=' => 1
        ));
        $numOfSessions = $this->modx->getCount('modTimetableSession',$c);
        if($numOfSessions > 0 && $day->get('active')) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * Returns name of current Day.
     * @return false|string
     */
    private function getCurrentDayName() {
        $day = date('l');
        return $day;
    }

    /**
     * Returns name of current Day.
     * @return false|string
     */
    private function getCurrentDayNum() {
        $day = date('N');
        return $day;
    }

    /**
     * Returns number of sessions within multi-dimensional array.
     * @param $mdArray
     * @return int
     */
    private function maxLength($mdArray) {
        $max = 0;
        foreach($mdArray as $child) {
            if(count($child) > $max) {
                $max = count($child);
            }
        }
        return $max;
    }

    /**
     * Gets all session times for the specified timetable, sorts time ascending then removes duplicates.
     * @param $days
     * @return array
     */
    private function getSessionTimes($days) {
        $sessionTimes = array();
        foreach($days as $day) {
            $c = $this->modx->newQuery('modTimetableSession');
            $c->sortby('start_time',$this->sortdir);
            $c->where(array(
                'day_id'=>$day->get('id'),
                'active:='=>1
            ));
            $sessions = $this->modx->getCollection('modTimetableSession', $c);
            foreach($sessions as $session) {
                $sessionTimes[] = strtotime($session->get('start_time'));
            }
        }
        sort($sessionTimes);
        $sorted = array();
        foreach ($sessionTimes as $sessionTime) {
            //$sorted[] = date('H:i',$sessionTime);
            $sorted[] = date($this->getOption('timepicker_format'),$sessionTime);
        }
        $sorted = array_values(array_unique($sorted));

        return $sorted;
    }

    private function prepareGridWithSessionTimes($sessionTimes,$dayNames) {
        $grid = array();
        for($i=0;$i<count($sessionTimes);$i++) {
            $row = array();
            for($j=0;$j<count($dayNames);$j++) {
                $row[$dayNames[$j]] = '<td></td>';
            }
            $grid[$sessionTimes[$i]] = $row;
        }
        //echo "<pre>";
        //print_r($grid);
        return $grid;
    }


    private function flip($arr) {
        $out = array();
        foreach ($arr as $key => $subarr) {
            foreach ($subarr as $subkey => $subvalue) {
                $out[$subkey][$key] = $subvalue;
            }
        }
        return $out;
    }

    private function populateGridWithSessions($grid,$sessionRows,$numOfRows,$numOfCols) {
        //Fill grid with session data
        foreach($sessionRows as $sessionRow) {
            foreach($sessionRow as $session) {
                $grid[$session['start_time']][$session['day_name']] = $session;
            }
        }
        return $grid;
    }
}