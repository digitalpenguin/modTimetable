<?php
require_once dirname(__FILE__) . '/model/modtimetable/modtimetable.class.php';
/**
 * @package modtimetable
 */

abstract class modTimetableBaseManagerController extends modExtraManagerController {
    /** @var modTimetable $modtimetable */
    public $modtimetable;
    public function initialize() {
        $this->modtimetable = new modTimetable($this->modx);

        $this->addCss($this->modtimetable->getOption('cssUrl').'mgr.css');
        $this->addJavascript($this->modtimetable->getOption('jsUrl').'mgr/modtimetable.js');
        $this->addHtml('<script type="text/javascript">
        Ext.onReady(function() {
            modTimetable.config = '.$this->modx->toJSON($this->modtimetable->options).';
            modTimetable.config.connector_url = "'.$this->modtimetable->getOption('connectorUrl').'";
        });
        </script>');
        
        parent::initialize();
    }
    public function getLanguageTopics() {
        return array('modtimetable:default');
    }
    public function checkPermissions() { return true;}
}