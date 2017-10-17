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
    public $timetableTpl = '';
    public $dayTpl = '';
    public $sessionTpl = '';
    public $sortby = 'position';
    public $sortdir = 'ASC';
    public $tableHeaderRow = array();
    public $tableRow = array();
    public $cellOpenTag = '<td>';
    public $cellCloseTag = '</td>';
    public $rowOpenTag = '<tr>';
    public $rowCloseTag = '</tr>';

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
            'datetimepicker_default_time' => $this->getOption('datetimepicker_default_time'),
            'datetimepicker_minute_interval' => $this->getOption('datetimepicker_minute_interval'),
            'datetimepicker_time_format' => $this->getOption('datetimepicker_time_format')
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

    public function getTimetables($timetables,$day,$renderTable,$timetableTpl,$dayTpl,$sessionTpl,$sortBy,$sortDir,$outputSeparator,$toPlaceholder) {
        if(!empty($timetables)) {
            $this->timetableIds = explode(",",$timetables);
        }
        $this->timetableTpl = $timetableTpl;
        $this->dayTpl = $timetableTpl;
        $this->sessionTpl = $timetableTpl;

        // Render sessions for single day
        if($day != null) {
            $this->singleDay=true;
            return $this->getDayOfSessionsFromManyTimetables($day);
        }


        $c = $this->modx->newQuery('modTimetableTimetable');
        $c->sortby($sortBy,$sortDir);
        $c->where(array('id:IN'=>$this->timetableIds));
        $timetables = $this->modx->getCollection('modTimetableTimetable',$c);

        $timetableList = array();
        foreach ($timetables as $timetable) {

            // If selected render timetable output in rows for displaying as HTML table
            if ($renderTable) {
                return $this->renderRows($timetable);

            }

            $timetableArray = $timetable->toArray();
            // Grab the days within each timetable
            $c = $this->modx->newQuery('modTimetableDay');
            $c->sortby($sortBy,$sortDir);
            $c->where(array('timetable_id'=>$timetableArray['id']));
            $days = $this->modx->getCollection('modTimetableDay',$c);
            $dayArray = array();
            $dayList = array();
            foreach($days as $day) {
                $dayArray = $day->toArray();

                // Grab the sessions within each day
                $c = $this->modx->newQuery('modTimetableSession');
                $c->sortby($sortBy,$sortDir);
                $c->where(array('day_id'=>$dayArray['id']));
                $sessions = $this->modx->getCollection('modTimetableSession',$c);
                $sessionArray = array();
                $sessionList = array();
                foreach($sessions as $session) {
                    $sessionArray = $session->toArray();
                    $sessionList[] = $this->modx->getChunk($sessionTpl,$sessionArray);
                }
                // Grab the day_id from the last iteration of the $sessionArray as they'll all be the same.
                // We can then compare it with the current id of the day so we only get these sessions on the correct day.
                if($dayArray['id'] === $sessionArray['day_id']) {
                    $this->modx->setPlaceholder('sessions',implode($sessionList));
                } else {
                    $this->modx->setPlaceholder('sessions','');
                }
                $dayList[] = $this->modx->getChunk($dayTpl,$dayArray);
            }
            // Grab the timetable_id from the last iteration of the $dayArray as they'll all be the same.
            // We can then compare it with the current id of the timetable so we only get these days on the correct timetable.
            if($timetableArray['id'] === $dayArray['timetable_id']) {
                $this->modx->setPlaceholder('days',implode($dayList));
            } else {
                $this->modx->setPlaceholder('days','');
            }
            $timetableList[] = $this->modx->getChunk($timetableTpl,$timetableArray);
        }

        $output = implode($outputSeparator,$timetableList);
        if (!empty($toPlaceholder)) {
            $this->modx->setPlaceholder($toPlaceholder,$output);
            return '';
        }
        return $output;
    }

    public function renderRows($timetable) {
        $output = $this->modx->getChunk('tableTimetableTpl',$timetable->toArray());
        $c = $this->modx->newQuery('modTimetableDay');
        $c->sortby($this->sortby,$this->sortdir);
        $c->where(array('timetable_id'=>$timetable->get('id')));
        $days = $this->modx->getCollection('modTimetableDay',$c);

        $headerRow = '<th></th>';
        $sessionRows = array();
        $dayIdx = 0;

        // Grab array of session times that has been sorted and duplicates removed.
        $sessionTimes = $this->getSessionTimes($days);

        foreach($days as $day) {
            $headerRow .= $this->modx->getChunk('tableHeaderRowTpl',$day->toArray());

            $c = $this->modx->newQuery('modTimetableSession');
            $c->sortby($this->sortby,$this->sortdir);
            $c->where(array('day_id'=>$day->get('id')));
            $sessions = $this->modx->getCollection('modTimetableSession',$c);

            $sessionIdx = 0;
            // Make sure column is created even if no sessions.
            if(empty($sessions)) {
                $sessionRows[$dayIdx][$sessionIdx] = array();//$this->cellOpenTag.$this->cellCloseTag;
            }

            foreach($sessions as $session) {
                $sessionRows[$dayIdx][$sessionIdx] = $session->toArray();
                $sessionIdx++;
            }
            $dayIdx++;
        }

        echo '<pre>';
        //print_r($sessionRows);

        // get the longest count of sessions in a day //$maxLength = $this->maxLength($sessionRows);
        $maxLength = count($sessionTimes);

        $rows='';
        for($sessionIdx=0;$sessionIdx<$maxLength;$sessionIdx++) {
            $rows .= $this->rowOpenTag;

            for($dayIdx=0;$dayIdx<count($sessionRows);$dayIdx++) {
                // Set time in the first column. Values taken from the $sessionTimes array sorted earlier.
                if ($dayIdx===0) {
                    $rows .= '<td>' . $sessionTimes[$sessionIdx] . '</td>';
                }

                echo $sessionRows[$dayIdx][$sessionIdx]['start_time'].'<br>';
                echo $sessionTimes[$sessionIdx];

                // Checks for empty cells. Adds empty tags or adds value if there is one.
                if (!empty($sessionRows[$dayIdx][$sessionIdx]) &&
                    $sessionRows[$dayIdx][$sessionIdx]['start_time'] == $sessionTimes[$sessionIdx]) {
                    $rows .= $this->modx->getChunk('tableSessionTpl', $sessionRows[$dayIdx][$sessionIdx]);


                } else {
                    $rows .= $this->cellOpenTag.$this->cellCloseTag;
                }
            }
            $rows .= $this->rowCloseTag;
        }
        $this->modx->setPlaceholder('headerRow',$headerRow);
        $this->modx->setPlaceholder('sessionRows',$rows);

        return $output;
    }

    /**
     * Renders a view that contains all the sessions with the specified day from many timetables.
     * @param string $day
     * @return false|string
     */
    public function getDayOfSessionsFromManyTimetables($day = '') {
        if(!$day) {
            $day = $this->getCurrentDay();
        }
        $output = $day;
        return $output;
    }

    /**
     * Returns name of current Day.
     * @return false|string
     */
    public function getCurrentDay() {
        $day = date('l');
        return $day;
    }

    /**
     * Returns number of sessions within multi-dimensional array.
     * @param $mdArray
     * @return int
     */
    public function maxLength($mdArray) {
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
    public function getSessionTimes($days) {
        $sessionTimes = array();
        foreach($days as $day) {
            $c = $this->modx->newQuery('modTimetableSession');
            $c->where(array('day_id' => $day->get('id')));
            $sessions = $this->modx->getCollection('modTimetableSession', $c);
            foreach($sessions as $session) {
                $sessionTimes[] = strtotime($session->get('start_time'));
            }
        }
        sort($sessionTimes);
        $sorted = array();
        foreach ($sessionTimes as $sessionTime) {
            $sorted[] = date('H:i',$sessionTime);
        }
        $sorted = array_unique($sorted);
        echo '<pre>'; //TODO remove this
        print_r($sorted); //TODO remove this
        return $sorted;
    }

}