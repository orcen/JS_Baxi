<?php
	use TYPO3\CMS\Backend\Controller;

	return [
		'C3BaxiZone' => [
			'path' => '/C3/Baxi/Zone',
			'target' => \C3\C3baxi\Controller\AjaxController::class. '::saveZone'
		],
		'C3BaxiLinie' => [
			'path' => '/C3/Baxi/Linie',
			'target' => \C3\C3baxi\Controller\AjaxController::class. '::saveLinie'
		],
		'C3BaxiCompanyRoute' => [
			'path' => '/C3/Baxi/Company',
			'target' => \C3\C3baxi\Controller\AjaxController::class. '::saveRoute'
		]
	];