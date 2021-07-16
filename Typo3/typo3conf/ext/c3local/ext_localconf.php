<?php

defined('TYPO3_MODE') || die();


// Add TSconfig
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:c3local/Configuration/PageTSconfig/PageConfig.t3s">');


$GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['c3_full'] = 'EXT:c3local/Configuration/RTE/full.yaml';
$GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['c3_inline'] = 'EXT:c3local/Configuration/RTE/inline.yaml';
$GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['c3'] = [	'C3\Local\ViewHelpers'];

// Add Example Signal Slots for Powermail
$signalSlotDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance( \TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class );
$signalSlotDispatcher->connect(
    \In2code\Powermail\Controller\FormController::class,
    'createActionBeforeRenderView',
    \C3\Local\Controller\FormController::class,
    'manipulateMailObjectOnCreate', false );

$signalSlotDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance( \TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class );
$signalSlotDispatcher->connect(
    \In2code\Powermail\Domain\Service\Mail\SendMailService::class,
    'sendTemplateEmailBeforeSend',
    \C3\Local\Domain\Service\SendMailService::class,
    'manipulateMailTo', false );

$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\In2code\Femanager\Domain\Validator\PasswordValidator::class] = [
	'className' => \C3\C3local\Domain\Validator\PasswordValidator::class
];
