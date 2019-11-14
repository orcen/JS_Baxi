<?php

	defined( 'TYPO3_MODE' ) or die();

	$tempColumns = [
		'fluid_icon' => [
			'exclude' => 1,
			'label'   => 'Icon',
			'config'  => [
				'type'  => 'select',
				'items' => [
					['Keine',''],
					['Konto', 'Konto'],
					['BAXI', 'BAXI'],
					['Einstellungen', 'Einstellungen'],
					['Pfeil_R', 'Pfeil_R'],
					['Pfeil_L', 'Pfeil_L'],
					['Umstieg', 'Umstieg'],
					['Standort', 'Standort'],
					['Uhr', 'Uhr'],
					['Uhr_kontur', 'Uhr_kontur'],
					['Dropdown', 'Dropdown'],
					['Favoriten', 'Favoriten'],
					['Favoriten_kontur', 'Favoriten_kontur'],
					['Kontur', 'Kontur'],
					['Konto', 'Konto'],
					['Hilfe', 'Hilfe'],
					['Hilfe_Klein', 'Hilfe_Klein'],
					['Abbrechen', 'Abbrechen'],
					['back', 'back'],
					['info', 'info'],
					['delete', 'delete'],
					['add', 'add'],
				],
				'size' => 1,
				'maxitems' => 1
			]
		]
	];

	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(
		'tt_content',
		$tempColumns
	);

	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
		'tt_content',
		'fluid_icon',
		'textmedia,header',
		'before:header'
	);