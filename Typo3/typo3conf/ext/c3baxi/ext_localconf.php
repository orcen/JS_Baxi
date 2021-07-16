<?php
	defined( 'TYPO3_MODE' ) || die( 'Access denied.' );
	call_user_func(
		function () {


			// Add TSconfig
			\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:c3baxi/Configuration/PageTSconfig/PageConfig.t3s">');

			\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
				'C3.C3baxi',
				'BaxiUserRides',
				[
					'Booking' => 'list, detail, cancel',
				],
				// non-cacheable actions
				[
					'Booking' => ''
				],
				\TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
			);

			\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
				'C3.C3baxi',
				'BaxiUserPage',
				[
					'Baxiuser' => 'index',
				],
				// non-cacheable actions
				[
				]
			);

			\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
				'C3.C3baxi',
				'BaxiUserFavorites',
				[
					'Favorites' => 'list,add',
					'Ajax'      => 'addFavorite'
				],
				// non-cacheable actions
				[
					'Favorites' => ''
				]
			);

			\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
				'C3.C3baxi',
				'BaxiSuche',
				[
					'FESuche' => 'index, search, findRoute, reserve, save',
					'Ajax'    => 'search, autocomplete, stationDetail, help, favorites, ride, rating',
					'Rating' => 'create'
				],
				[
					'FESuche' => 'index, search, findRoute, reserve, save',
				]
			);

			\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
				'C3.C3baxi',
				'DeepLinks',
				[
					'Deep' => 'index, confirmRide',
				],
				[]
			);


			$extbaseObjectContainer = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
				\TYPO3\CMS\Extbase\Object\Container\Container::class
			);
			$extbaseObjectContainer->registerImplementation(
				\TYPO3\CMS\Extbase\Persistence\Generic\QueryFactoryInterface::class,
				\C3\C3baxi\Persistence\Generic\QueryFactory::class
			);
		}
	);

	$GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['baxi'] = [
		'C3\C3baxi\ViewHelpers',
	];

	$GLOBALS['TYPO3_CONF_VARS']['LOG']['writerConfiguration'] = array(
		// configuration for ERROR level log entries
		\TYPO3\CMS\Core\Log\LogLevel::INFO => array(
			// add a FileWriter
			'TYPO3\\CMS\\Core\\Log\\Writer\\FileWriter' => array(
				// configuration for the writer
				'logFile' => 'typo3temp/var/log/typo3_info.log'
			)
		)
	);

	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['felogin']['login_confirmed'][] = C3\C3baxi\Domain\Service\AppService::class . '->afterLogin';

	$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects']['TYPO3\\CMS\\Extbase\\Mvc\\Controller\\Argument'] = array('className' => 'C3\\C3baxi\\Xclass\\Extbase\\Mvc\\Controller\\Argument');

	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks'][C3\C3baxi\Tasks\BaxiInfo::class] = [
		'extension'        => 'c3baxi',
		'title'            => 'Infomails',
		'description'      => '',
		'additionalFields' => C3\C3baxi\Tasks\BaxiInfoFieldProvider::class
	];

	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks'][C3\C3baxi\Tasks\Report::class] = [
		'extension'        => 'c3baxi',
		'title'            => 'Reports',
		'description'      => '',
		//		'additionalFields' => C3\C3local\Tasks\NewsImportFieldProvider::class
	];

	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks'][C3\C3baxi\Tasks\Subscription::class] = [
		'extension'        => 'c3baxi',
		'title'            => 'Subscriptions',
		'description'      => 'ABO',
		'additionalFields' => C3\C3baxi\Tasks\SubscriptionFieldProvider::class
	];
