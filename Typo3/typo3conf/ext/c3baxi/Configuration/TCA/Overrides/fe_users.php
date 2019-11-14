<?php
	defined('TYPO3_MODE') or die();

	$temporaryColumns = [
		'tx_c3baxi_favorites' => [
			'exclude' => 1,
			'label' => 'Favoriten',
			'config' => [
				'type' => 'input',
				'size' => '30',
				'eval' => 'trim'
			]
		]
	];

	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(
		'fe_users',
		$temporaryColumns
	);
//	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
//		'fe_users',
//		'tx_c3baxi_favorites',
//		0,
//		'after:comments'
//	);