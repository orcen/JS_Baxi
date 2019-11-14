<?php

defined('TYPO3_MODE') || die();


/**
 * Include TypoScript
 */
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'c3local',
    'Configuration/TypoScript',
    'Local Template'
);
