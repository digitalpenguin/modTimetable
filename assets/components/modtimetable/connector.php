<?php
/**
 * modTimetable Connector
 *
 * @package modtimetable
 */
require_once dirname(dirname(dirname(dirname(__FILE__)))).'/config.core.php';
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
require_once MODX_CONNECTORS_PATH . 'index.php';

$corePath = $modx->getOption('modtimetable.core_path', null, $modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/modtimetable/');
$modtimetable = $modx->getService(
    'modtimetable',
    'modTimetable',
    $corePath . 'model/modtimetable/',
    array(
        'core_path' => $corePath
    )
);

/* handle request */
$modx->request->handleRequest(
    array(
        'processors_path' => $modtimetable->getOption('processorsPath', null, $corePath . 'processors/'),
        'location' => '',
    )
);