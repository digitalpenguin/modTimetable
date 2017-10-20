<?php
/**
 * Resolve creating db tables
 *
 * THIS RESOLVER IS AUTOMATICALLY GENERATED, NO CHANGES WILL APPLY
 *
 * @package modtimetable
 * @subpackage build
 *
 * @var mixed $object
 * @var modX $modx
 * @var array $options
 */

if ($object->xpdo) {
    $modx =& $object->xpdo;
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            $modelPath = $modx->getOption('modtimetable.core_path', null, $modx->getOption('core_path') . 'components/modtimetable/') . 'model/';
            
            $modx->addPackage('modtimetable', $modelPath, null);


            $manager = $modx->getManager();

            $manager->createObjectContainer('modTimetableObject');
            $manager->createObjectContainer('modTimetableTimetable');
            $manager->createObjectContainer('modTimetableDay');
            $manager->createObjectContainer('modTimetableSession');
            $manager->createObjectContainer('modTimetableExtraFieldClosure');
            $manager->createObjectContainer('modTimetableExtraField');

            break;
    }
}

return true;