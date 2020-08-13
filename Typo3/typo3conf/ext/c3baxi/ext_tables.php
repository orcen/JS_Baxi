<?php
	defined( 'TYPO3_MODE' ) || die( 'Access denied.' );

	call_user_func(
		function () {

			\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
				'C3.C3baxi',
				'BaxiSuche',
				'Baxi - Routen Suche'
			);

			\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
				'C3.C3baxi',
				'BaxiUserRides',
				'Baxi - Gebuchte Fahrten'
			);

			\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
				'C3.C3baxi',
				'BaxiUserFavorites',
				'Baxi - Favoriten'
			);

			\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
				'C3.C3baxi',
				'BaxiUserPage',
				'Baxi - Kontoseite'
			);

			$pluginSignature = 'c3baxi' . '_baxiuserpage';
			$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
			\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature, 'FILE:EXT:c3baxi/Configuration/FlexForms/BaxiuserPlugin.xml');

			if ( TYPO3_MODE === 'BE' ) {
				\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
					'C3.C3baxi',
					'tools', // Make module a submodule of 'web'
					'baxi', // Submodule key
					'', // Position
					[
						'Baxi'        => 'index, detailBooking, showMapBooking',
						'Linie'       => 'list, new, create, edit, update, import, delete, export',
						'Haltestelle' => 'list, show, new, create, edit, update, delete, import',
						'Zone'        => 'list, show, new, create, edit, update, delete, import',
						'Fahrt'       => 'list, show, new, create, edit, update, delete, export',
						'FahrtZeit'   => 'list, show, new, create, edit,update, delete',
						'Company'     => 'list, show, new, create, edit,update, delete, import',
						'Booking'     => 'list, listBE, show, new, create, edit, update, delete',
					],
					[
						'access'                => 'user,group',
						'icon'                  => 'EXT:c3baxi/Resources/Public/Icons/user_mod_baxi.svg',
						'labels'                => 'LLL:EXT:c3baxi/Resources/Private/Language/locallang_baxi.xlf',
						'navigationComponentId' => ''
					]
				);
			}

			\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile( 'c3baxi', 'Configuration/TypoScript', 'Baxi Fahrten' );

			\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr( 'tx_c3baxi_domain_model_haltestelle', 'EXT:c3baxi/Resources/Private/Language/locallang_csh_tx_c3baxi_domain_model_haltestelle.xlf' );
			\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages( 'tx_c3baxi_domain_model_haltestelle' );

			\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr( 'tx_c3baxi_domain_model_zone', 'EXT:c3baxi/Resources/Private/Language/locallang_csh_tx_c3baxi_domain_model_zone.xlf' );
			\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages( 'tx_c3baxi_domain_model_zone' );

			\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr( 'tx_c3baxi_domain_model_linie', 'EXT:c3baxi/Resources/Private/Language/locallang_csh_tx_c3baxi_domain_model_linie.xlf' );
			\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages( 'tx_c3baxi_domain_model_linie' );

			\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr( 'tx_c3baxi_domain_model_fahrt', 'EXT:c3baxi/Resources/Private/Language/locallang_csh_tx_c3baxi_domain_model_fahrt.xlf' );
			\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages( 'tx_c3baxi_domain_model_fahrt' );

			\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr( 'tx_c3baxi_domain_model_fahrtzeit', 'EXT:c3baxi/Resources/Private/Language/locallang_csh_tx_c3baxi_domain_model_fahrtzeit.xlf' );
			\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages( 'tx_c3baxi_domain_model_fahrtzeit' );


		}
	);