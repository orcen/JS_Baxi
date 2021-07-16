<?php


	namespace C3\C3baxi\Controller;


	use C3\C3baxi\Domain\Model\Booking;
	use C3\C3baxi\Domain\Model\FahrtZeit;
	use C3\C3baxi\Helper\RouteFinder;
	use Psr\Log\LogLevel;
	use TYPO3\CMS\Core\Error\DebugExceptionHandler;
	use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
	use TYPO3\CMS\Core\Utility\GeneralUtility;
	use TYPO3\CMS\Extbase\Object\ObjectManager\ObjectManager;
	use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
	use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

	class FESucheController extends ActionController
	{
		/**
		 * @var \C3\C3baxi\Domain\Repository\HaltestelleRepository
		 * @inject
		 */
		protected $haltestellenRepository = NULL;

		/**
		 * @var \C3\C3baxi\Domain\Repository\FahrtRepository
		 * @inject
		 */
		protected $fahrtenRepository = NULL;
		/**
		 * @var \C3\C3baxi\Domain\Repository\BookingRepository
		 * @inject
		 */
		protected $bookingRepository = NULL;
		/**
		 * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
		 * @inject
		 */
		protected $persistenceManager;

		/**
		 * @var \C3\C3baxi\Domain\Repository\UserRepository
		 * @inject
		 */
		protected $frontendUserRepository;

		protected $uriBuilder;

		protected $logger;

		public function __construct()
		{
			$objectManager = GeneralUtility::makeInstance( \TYPO3\CMS\Extbase\Object\ObjectManager::class );
			$this->uriBuilder = $objectManager->get( \TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder::class );
			$this->uriBuilder
				->setUseCacheHash( FALSE )
				->setNoCache( TRUE )
				->setTargetPageUid( 1 )
				->setTargetPageType( 666 );

			$this->logger = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance( \TYPO3\CMS\Core\Log\LogManager::class )->getLogger( __CLASS__ );

			parent::__construct();
		}

		/**
		 *
		 */
		public function indexAction()
		{
			$pageRender = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance( \TYPO3\CMS\Core\Page\PageRenderer::class );
			$this->uriBuilder->reset()
				->setCreateAbsoluteUri( TRUE )
				->setTargetPageUid( 1 )
				->setTargetPageType( 666 );

			$pageRender->addJsFooterFile( 'EXT:c3baxi/Resources/Public/JavaScript/datetimepicker/build/jquery.datetimepicker.full.min.js' );
			$pageRender->addCssFile( 'EXT:c3baxi/Resources/Public/JavaScript/datetimepicker/jquery.datetimepicker.css' );
//

			$GLOBALS['TSFE']->fe_user->setKey( 'ses', 'baxi_fahrt', FALSE );
			$GLOBALS["TSFE"]->fe_user->storeSessionData();

			$this->initiateJSSettings();
		}

		public function searchAction()
		{

		}

		/**
		 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
		 * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
		 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
		 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException
		 */
		public function findRouteAction()
		{
			$storageType = 'ses'; // session

			if ( $this->request->hasArgument( 'route' ) || $this->request->hasArgument( 'shift' ) ) {/*return FALSE;*/

				if ( $this->request->hasArgument( 'shift' ) ) {
					$routeParams = [
						'time'  => $GLOBALS['TSFE']->fe_user->getKey( $storageType, 'baxi_time' ),
						'start' => $GLOBALS['TSFE']->fe_user->getKey( $storageType, 'baxi_start' ),
						'end'   => $GLOBALS['TSFE']->fe_user->getKey( $storageType, 'baxi_end' ),
					];



					if ( $this->request->getArgument( 'shift' ) == 'prev' )
						$routeParams['time'] -= (60 * 60) * 12;
					else
						$routeParams['time'] += (60 * 60) * 12;
				} else {
					$routeParams = $this->request->getArgument( 'route' );
				}

				$error = FALSE;
				$errorMsg = [];

				if ( isset( $routeParams['time'] ) && $routeParams['time'] != NULL ) {
					$GLOBALS['TSFE']->fe_user->setKey( $storageType, 'baxi_time', $routeParams['time'] );
				} else {
					$error = TRUE;
					$errorMsg[] = 'Wunschzeit wurde nicht ausgew&auml;hlt';
				}

				if ( isset( $routeParams['start'] ) && $routeParams['start'] != NULL ) {
					$GLOBALS['TSFE']->fe_user->setKey( $storageType, 'baxi_start', $routeParams['start'] );
				} else {
					$error = TRUE;
					$errorMsg[] = 'Starthaltestelle w&auml;hlen';
				}

				if ( isset( $routeParams['end'] ) && $routeParams['end'] != NULL ) {
					$GLOBALS['TSFE']->fe_user->setKey( $storageType, 'baxi_end', $routeParams['end'] );
				} else {
					$error = TRUE;
					$errorMsg[] = 'Zielhaltestelle w&auml;hlen';
				}


				if ( $error ) {
					$this->redirect( 'index', 'FESuche', 'C3.C3baxi', [], 1 );
				}

				$GLOBALS["TSFE"]->fe_user->storeSessionData();

				$searchTime = new \DateTimeImmutable();
				$searchTime = $searchTime->setTimestamp( $routeParams['time'] );
				$start = $this->haltestellenRepository->findByUid( $routeParams['start'] );
				$end = $this->haltestellenRepository->findByUid( $routeParams['end'] );

				$routeFinder = GeneralUtility::makeInstance(RouteFinder::class, $start, $end, $searchTime);
				$routeFinder->setTime( $searchTime );
				$routeFinder->findLine();
				$routeFinder->findRoutes();

				if( count( $routeFinder->getFoundRoutes() ) ) {
					$fahrten = $routeFinder->getFoundRoutes();
					array_walk( $fahrten, function (&$fahrt){
						$fahrt['bookable'] = true;
					});

//					$bookedRides = false;
//					if( isset(  $GLOBALS['TSFE']->fe_user->user['uid'] ) ) {
//						$uuid = $GLOBALS['TSFE']->fe_user->user['uid'];
//						$bookedRides = $this->bookingRepository->findByUserAndDate( $uuid, $searchTime->getTimestamp() );
//					}

					$this->view->assignMultiple([
						'price' => $routeFinder->getPrice(),
						'fahrten' => $fahrten,
						'startStation' => $routeFinder->getStart(),
						'endStation' => $routeFinder->getEnd(),
						'fahrtzeit' => $routeFinder->getTime()
					]);
				}
			} else {
				$this->redirect( 'index', 'FESuche', 'C3.C3baxi', [], 1 );
			}
			$this->initiateJSSettings();
		}

		public function reserveAction()
		{
			$storageType = 'ses';
//			if ( isset( $GLOBALS["TSFE"]->fe_user->user['uid'] ) && $GLOBALS["TSFE"]->fe_user->user['uid'] !== NULL ) {
//				$storageType = 'user';
//			}

			if ( $this->request->hasArgument( 'fahrt' ) ) {

				$fahrt = $this->request->getArgument( 'fahrt' );

				$GLOBALS['TSFE']->fe_user->setKey( $storageType, 'baxi_fahrt', $fahrt );

				$this->view->assign( 'fahrt', $this->fahrtenRepository->findByUid( $fahrt ) );
				$this->view->assign( 'fahrtZeit', $GLOBALS['TSFE']->fe_user->getKey( $storageType, 'baxi_time' ) );
			}

			if ( $this->request->hasArgument( 'startStation' ) ) {

				$startStation = $this->haltestellenRepository->findByUid( $this->request->getArgument( 'startStation' ) );

				$this->view->assign( 'startStation', $startStation );
			} elseif ( $startUid = $GLOBALS['TSFE']->fe_user->getKey( $storageType, 'baxi_start' ) ) {
				$this->view->assign( 'startStation', $this->haltestellenRepository->findByUid( $startUid ) );
			}

			if ( $this->request->hasArgument( 'endStation' ) ) {

				$endStation = $this->haltestellenRepository->findByUid( $this->request->getArgument( 'endStation' ) );

				$this->view->assign( 'endStation', $endStation );
			} elseif ( $endUid = $GLOBALS['TSFE']->fe_user->getKey( $storageType, 'baxi_end' ) ) {
				$this->view->assign( 'endStation', $this->haltestellenRepository->findByUid( $endUid ) );
			}
			$this->initiateJSSettings();
			$GLOBALS["TSFE"]->fe_user->storeSessionData();
		}

		public function saveAction()
		{
			$error = FALSE;

			$arguments = $this->request->getArguments();

			$arguments['start'] = $GLOBALS['TSFE']->fe_user->getKey( 'ses', 'baxi_start' );
			$arguments['end'] = $GLOBALS['TSFE']->fe_user->getKey( 'ses', 'baxi_end' );
			$arguments['time'] = $GLOBALS['TSFE']->fe_user->getKey( 'ses', 'baxi_time' );
			$arguments['fahrt'] = $GLOBALS['TSFE']->fe_user->getKey( 'ses', 'baxi_fahrt' );
			$arguments['user'] = $GLOBALS["TSFE"]->fe_user->user['uid'];

			$booking = new Booking();
			$date = new \DateTime();
			$date->setTimestamp($arguments['time']);

			$booking->setDate( $date );
			$booking->setAdults( $arguments['adults'] );
			$booking->setChildren( $arguments['children'] );

			if ( $startStation = $this->haltestellenRepository->findByUid( $arguments['start'] ) ) {
				$booking->setStartStation( $startStation );
			} else {
				$error = TRUE;
			}

			if ( $endStation = $this->haltestellenRepository->findByUid( $arguments['end'] ) ) {
				$booking->setEndStation( $endStation );
			} else {
				$error = TRUE;
			}

			if ( $user = $this->frontendUserRepository->findByUid( $arguments['user'] ) ) {
				$booking->setUser( $user );
			} else {
				$error = TRUE;
			}

			if ( $fahrt = $this->fahrtenRepository->findByUid( $arguments['fahrt'] ) ) {
				$booking->setFahrt( $fahrt );

				foreach($fahrt->getZeiten() as $fahrtzeit){
					if($fahrtzeit->getZone() === $startStation->getZone() ) {
						$date = $this->alterDate( $fahrtzeit->getZeit(), $date );
						$booking->setDate($date);
						break;
					}
				}
//				$date
			} else {
				$error = TRUE;
			}

			$booking->setInfo( $arguments['info'] );
			$booking->setPid( 14 );

			if ( !$error ) {
				$found = $this->bookingRepository->findRideOfDay( $arguments['time'], $fahrt );

				$confirmed = false;

				if( $found->count() != 0 ) {
					foreach( $found as $prevBooking ) {
						if( $prevBooking->isConfirmed() ) {
							$confirmed = true;
							break;
						}
					}
				}
				$booking->setConfirmed( $confirmed );

				$this->bookingRepository->add( $booking );
				$this->persistenceManager->persistAll();

				$this->sendConfirmationEmail( $booking );

				$this->forward( 'list', 'Booking', 'C3baxi' );
			}
		}


		protected function getFahrtPreis( $zone1, $zone2 )
		{
			return rand( 20, 100 ) / 10;
		}

		protected function alterDate( $date, $referer )
		{
//			$offset =  $referer->getOffset();
//			$offset = $offset / (60 * 60);
			return $referer->setTime( $date->format('H') , $date->format('i'));
		}

		protected function getUserFavorites()
		{
			if ( !isset( $GLOBALS["TSFE"]->fe_user->user['uid'] ) ) return [];

			$uuid = $GLOBALS["TSFE"]->fe_user->user['uid'];

			$user = $this->frontendUserRepository->findByUid( $uuid );
			$favorites = $user->getTxC3baxiFavorites();

			if ( $favorites == '' ) $favorites = [];
			else $favorites = json_decode( $favorites );

			return $favorites;
		}

		protected function sendConfirmationEmail( Booking $booking ) {

			// build content for email

			$date =  $booking->getDate();

			$departure =  $booking->getDate();

			$arrival =  $booking->getDate();

			$times = $booking->getFahrt()->getZeiten();
			foreach( $times as $time ) {
				if( $time->getZone() === $booking->getStartStation()->getZone() ) {

					$departure->setTime( $time->getZeit()->format( 'H'), $time->getZeit()->format('i') );

				}
				elseif( $time->getZone() === $booking->getEndStation()->getZone() ) {

					$arrival->setTime( $time->getZeit()->format( 'H'), $time->getZeit()->format('i') );
				}
			}

			// create email html
			/** @var \TYPO3\CMS\Fluid\View\StandaloneView $emailView */
			$emailView = $this->objectManager->get('TYPO3\\CMS\\Fluid\\View\\StandaloneView');

			$extPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('c3baxi');

			$templateRootPath = $extPath."Resources/Private/Templates/";

			$templatePathAndFilename = $templateRootPath . 'Email/Booking/confirm.html';
			$emailView->setTemplatePathAndFilename($templatePathAndFilename);

			$emailView->assignMultiple( [ 'booking' => $booking, 'date' => $date, 'arrival' => $arrival, 'departure' => $departure ] );
			$emailHtmlBody = $emailView->render( );


			$recipient = $booking->getUser()->getEmail();
			$sender = 'info@baxi.de';
			$subject = 'BAXI - Bestaetigung';

			$message = $this->objectManager->get('TYPO3\\CMS\\Core\\Mail\\MailMessage');
			$message->setTo($recipient)
				->setFrom($sender)
				->setSubject($subject);
			// Possible attachments here
			//foreach ($attachments as $attachment) {
			//	$message->attach(\Swift_Attachment::fromPath($attachment));
			//}

			// Plain text example
			//$message->setBody($emailBody, 'text/plain');
			// HTML Email
			$message->addPart($emailHtmlBody, 'text/html');
			$message->send();
			return $message->isSent();
		}

		protected function initiateJSSettings() {
			$pageRender = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance( \TYPO3\CMS\Core\Page\PageRenderer::class );
			$this->uriBuilder->reset()
				->setCreateAbsoluteUri( TRUE )
				->setTargetPageUid( 1 )
				->setTargetPageType( 666 );

			$favorites = [];
			if( isset( $GLOBALS["TSFE"]->fe_user->user['uid'] ) ) {
				$uuid = $GLOBALS["TSFE"]->fe_user->user['uid'];

				$user = $this->frontendUserRepository->findByUid( $uuid );

				$favorites = $user->getTxC3baxiFavorites();
			}
			$baxiSettings = [
				'ticketType' => 'adult',

				'loggedIn'   => isset( $GLOBALS["TSFE"]->fe_user->user['uid'] ),
				'favorites'  => $favorites,
				'ajaxUrl'    => [
					'haltestelle'    => $this->uriBuilder->setArguments( [] )
						->uriFor( 'autocomplete', ['type' => 'haltestelle'], 'Ajax', 'C3baxi', 'BaxiSuche' ),
					'station'        => $this->uriBuilder->setArguments( [] )
						->uriFor( 'stationDetail', [], 'Ajax', 'C3baxi', 'BaxiSuche' ),
					'favorites'      => $this->uriBuilder->setArguments( [] )
						->uriFor( 'favorites', ['doAction' => 'findAll'], 'Ajax', 'C3baxi', 'BaxiSuche' ),
					'help'           => $this->uriBuilder->setArguments( [] )
						->uriFor( 'help', [], 'Ajax', 'C3baxi', 'BaxiSuche' ),
					'rating'         => $this->uriBuilder->setArguments( [] )
						->uriFor( 'rating', [], 'Ajax', 'C3baxi', 'BaxiSuche' ),
					/** deprecated */
					'addFavorite'    => $this->uriBuilder->setArguments( [] )
						->uriFor( 'addFavorite', [], 'Ajax', 'C3baxi', 'BaxiSuche' ),
					'removeFavorite' => $this->uriBuilder->setArguments( [] )
						->uriFor( 'removeFavorite', [], 'Ajax', 'C3baxi', 'BaxiSuche' ),
				],
				'mapCenter'  => [ // default is Tirschenreuth, Germany
				                  'lat' => 49.8817161,
				                  'lng' => 12.3303441
				]
			];
			$pageRender->addJsFooterInlineCode( 'baxiSearchSettings', 'var baxiSearchSettings = ' . json_encode( $baxiSettings ) );

		}
	}