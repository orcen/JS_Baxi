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
		],
		'ticket_art' => [
			'exclude' => 1,
			'label' => 'Kategorie',
			'config' => [
				'type' => 'input',
				'size' => '30',
				'eval' => 'trim'
			]
		],
		'tx_c3baxi_notification_telephone' => [
			'exclude' => 1,
			'label' => 'Benachrichtigung Telefon',
			'config' => [
				'type' => 'check',
				'renderType' => 'checkboxToggle',
				'items' => [
					'0' => [
						'0' => '',
						'1' => '',
					],
				],
			]
		],
		'tx_c3baxi_notification_email' => [
			'exclude' => 1,
			'label' => 'Benachrichtigung Email',
			'config' => [
				'type' => 'check',
				'renderType' => 'checkboxToggle',
				'items' => [
					'0' => [
						'0' => '',
						'1' => '',
					],
				],
			]
		],
	];

	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(
		'fe_users',
		$temporaryColumns
	);
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
		'fe_users',
		'tx_c3baxi_notification_telephone',
		0,
		'after:telephone'
	);
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
		'fe_users',
		'tx_c3baxi_notification_email',
		0,
		'after:email'
	);

