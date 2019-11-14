<?php


	namespace C3\C3baxi\Domain\Service;


	use TYPO3\CMS\Extbase\Mvc\Controller\AbstractController;
	use TYPO3\CMS\Felogin\Controller\FrontendLoginController;
	use TYPO3\CMS\Core\Utility\GeneralUtility;
	use TYPO3\CMS\Extbase\Object\ObjectManager;

	class AppService extends AbstractController
	{
		/** @var $logger \TYPO3\CMS\Core\Log\Logger */
		protected $logger;

		function __construct()
		{

			$this->logger = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance( \TYPO3\CMS\Core\Log\LogManager::class )->getLogger( __CLASS__ );
		}

		public function afterLogin( $params, FrontendLoginController $frontendLoginController )
		{

			$selectedFahrt = $GLOBALS['TSFE']->fe_user->getKey( 'ses', 'baxi_fahrt' );

			if ( $selectedFahrt ) {
				$objectManager = GeneralUtility::makeInstance( \TYPO3\CMS\Extbase\Object\ObjectManager::class );
				$uriBuilder = $objectManager->get( \TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder::class );

				$url = $uriBuilder->reset()
					->setCreateAbsoluteUri( TRUE )
					->setTargetPageUid( 13 )

					->uriFor(
						'reserve',
						[
							'fahrt' => $selectedFahrt
						],
						'FESuche',
						'C3baxi',
						'BaxiSuche'
					);

				\TYPO3\CMS\Core\Utility\HttpUtility::redirect( $url );
				exit();
			}

		}
	}