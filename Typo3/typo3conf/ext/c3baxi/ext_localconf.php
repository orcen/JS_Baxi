<?php
	defined( 'TYPO3_MODE' ) || die( 'Access denied.' );
	call_user_func(
		function () {

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
				[]
			);
		}
	);

//	$signalSlotDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\SignalSlot\\Dispatcher');
//	$signalSlotDispatcher->connect(
//		'\In2code\Femanager\Controller\UserController', // source Class
//		'loginAsAction', // source method
//		'\C3\C3baxi\Domain\Service\AppService' , // target Class
//		'afterLogin', // target method
//		FALSE
//	);

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

//	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_userauthgroup.php']['backendUserLogin'][] =
//		\Vendor\MyExtension\Hooks\BackendUserLogin::class . '->dispatch';
	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['felogin']['login_confirmed'][] = C3\C3baxi\Domain\Service\AppService::class . '->afterLogin';

