<?php
require_once dirname(dirname(__FILE__)) . '/index.class.php';
/**
 * Loads the home page.
 *
 * @package modtimetable
 * @subpackage controllers
 */
class modTimetableHomeManagerController extends modTimetableBaseManagerController {
    public function process(array $scriptProperties = array()) {

    }
    public function getPageTitle() { return $this->modx->lexicon('modtimetable'); }
    public function loadCustomCssJs() {
    
    
        $this->addJavascript($this->modtimetable->getOption('jsUrl').'mgr/extras/griddraganddrop.js');

        $this->addJavascript($this->modtimetable->getOption('jsUrl').'mgr/widgets/days.grid.js');
        $this->addJavascript($this->modtimetable->getOption('jsUrl').'mgr/widgets/timetables.grid.js');
        $this->addJavascript($this->modtimetable->getOption('jsUrl').'mgr/widgets/home.panel.js');
        $this->addLastJavascript($this->modtimetable->getOption('jsUrl').'mgr/sections/home.js');
    
    }

    public function getTemplateFile() { return $this->modtimetable->getOption('templatesPath').'home.tpl'; }
}