<?php


	namespace C3\C3baxi\Controller;


	use C3\C3baxi\Domain\Model\Booking;
	use Psr\Log\LogLevel;
	use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
	use TYPO3\CMS\Core\Utility\GeneralUtility;
	use TYPO3\CMS\Extbase\Object\ObjectManager\ObjectManager;
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

		public function findRouteAction()
		{
			$storageType = 'ses';

			if ( $this->request->hasArgument( 'route' ) || $this->request->hasArgument( 'shift' ) ) {/*return FALSE;*/

				if ( $this->request->hasArgument( 'shift' ) ) {
					$routeParams = [
						'time'  => $GLOBALS['TSFE']->fe_user->getKey( $storageType, 'baxi_time' ),
						'start' => $GLOBALS['TSFE']->fe_user->getKey( $storageType, 'baxi_start' ),
						'end'   => $GLOBALS['TSFE']->fe_user->getKey( $storageType, 'baxi_end' ),
					];
					if ( $this->request->getArgument( 'shift' ) == 'prev' )
						$routeParams['time'] -= (60 * 60) * 6;
					else
						$routeParams['time'] += (60 * 60) * 6;
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

				$routeParams['start'] = $this->haltestellenRepository->findByUid( $routeParams['start'] );
				$routeParams['end'] = $this->haltestellenRepository->findByUid( $routeParams['end'] );

				$startLinien = $routeParams['start']->getZone()->getLinien();
				$endLinien = $routeParams['end']->getZone()->getLinien();

				$selectedLinie = $this->findeLinie( $startLinien, $endLinien );

				if ( $selectedLinie ) {




					$this->view->assign( 'fahrten', $this->findeFahrten( $selectedLinie, $routeParams ) );
					$this->view->assign( 'startStation', $routeParams['start'] );
					$this->view->assign( 'endStation', $routeParams['end'] );
					$this->view->assign( 'fahrtzeit', \DateTime::createFromFormat( 'U', $routeParams['time'] ) );
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
			$booking->setDate( $arguments['time'] );
			$booking->setAdults( $arguments['tickets'] );

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
			} else {
				$error = TRUE;
			}

			$booking->setInfo( $arguments['info'] );
			$booking->setPid( 14 );

			if ( !$error ) {
				$this->bookingRepository->add( $booking );
				$this->persistenceManager->persistAll();

				$this->sendConfirmationEmail( $booking );

				$this->forward( 'list', 'Booking', 'C3baxi' );
			}
		}

		public static function findeLinie( $start, $end )
		{
			if ( $start->count() == 1 && $end->count() == 1 ) {
				$sLinie = $start->current();
				$eLinie = $end->current();
				if ( $sLinie->getNr() == $eLinie->getNr() ) {
					return $sLinie;
				}
			}
			/** Todo: finde anschliesende Linien */
			return FALSE;
		}

		protected function findeFahrten( $linie, $params )
		{

			/**
			 * ToDo:
			 * [X] Richtung bestimmen
			 * [X] Fahrten vor wunschzeit ausschliesen
			 *
			 */

//			$GLOBALS['TSFE']->fe_user->setKey( 'ses', $key, $data);
//			$GLOBALS["TSFE"]->storeSessionData();
			// detect witch storage to use
			$abfahrt = $params['time'];

			$wunschZeit = new \DateTime();
			$wunschZeit->setTimestamp( $abfahrt );

			$fahrten = array_filter(
				$linie->getFahrten()->toArray(),
				function ( $fahrt ) use ( $params, $wunschZeit ) {

					$weekDays = ['So', 'Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa', 'So'];
					$weekday = $weekDays[$wunschZeit->format( 'w' )];

					$endFound = FALSE;
					foreach ( $fahrt->getZeiten() as $zeit ) {
						// fahrt findet nicht am richtigen Wochentag statt
						if ( !in_array( $weekday, (array) $fahrt->getTage() ) ) return FALSE;


						$fahrtZeit = new \DateTime();
						$fahrtZeit->setTimestamp( $zeit->getZeit() );
						$fahrtZeit->setDate( $wunschZeit->format( 'Y' ), $wunschZeit->format( 'm' ), $wunschZeit->format( 'd' ) );
						$timeDiff = $wunschZeit->diff( $fahrtZeit );

						if ( $zeit->getZone() == $params['end']->getZone() ) {
							$endFound = TRUE;
						}

						// richtige richtung
						if ( $zeit->getZone() == $params['start']->getZone() && !$endFound ) {
							if ( $timeDiff->format( '%R' ) === '+' ) {
								return TRUE;
							}

							return FALSE;
						} elseif ( $zeit->getZone() == $params['start']->getZone() && $endFound ) {
							return FALSE;
						} elseif ( $params['start']->getZone() === $params['end']->getZone() ) {
							if ( $wunschZeit < $fahrtZeit ) {
								return TRUE;
							}
							return TRUE;
						}
					}
				}
			);

			if ( empty( $fahrten ) ) {
				if ( $this->request->hasArgument( 'shift' ) ) {
					if ( $this->request->getArgument( 'shift' ) == 'prev' )
						$params['time'] -= (60 * 60) * 6;
					else
						$params['time'] += (60 * 60) * 6;
				}
//				$params['time'] += (60*60) * 6;
//				return $this->findeFahrten( $linie, $params );
			}

			$found = [];
			$bookedRides = false;
			if( isset(  $GLOBALS['TSFE']->fe_user->user['uid'] ) ) {
				$uuid = $GLOBALS['TSFE']->fe_user->user['uid'];

				$bookedRides = $this->bookingRepository->findByUserAndDate( $uuid, $wunschZeit->getTimestamp() );

			}

			foreach ( $fahrten as $fahrt ) {

				$tmp = [
					'uid'      => $fahrt->getUid(),
					'abfahrt'  => 0,
					'ankunft'  => 0,
					'preis'    => $this->getFahrtPreis( $params['start']->getZone(), $params['end']->getZone() ),
					'linie'    => $linie->getNr(),
					'umstieg'  => FALSE,
					'dauer'    => rand( 10, 50 ),
					'sameZone' => FALSE,
					'allowed'  => TRUE,
					'bookable' => TRUE,
				];

				foreach ( $fahrt->getZeiten() as $zeit ) {

					if ( $zeit->getZone() == $params['start']->getZone() ) {
						$tmp['abfahrt'] = $zeit;
					} elseif ( $zeit->getZone() == $params['end']->getZone() ) {
						$tmp['ankunft'] = $zeit;
					} elseif ( $params['start']->getZone() == $params['end']->getZone() ) {
						$tmp['abfahrt'] = $zeit->getZeit();
						$tmp['ankunft'] = $zeit->getZeit() + (60 * 10);
						$tmp['sameZone'] = TRUE;
					}
				}

				$tmp['abfahrt']->setZeit( $this->alterDate( $tmp['abfahrt']->getZeit(), $params['time'] ) );
				$tmp['ankunft']->setZeit( $this->alterDate( $tmp['ankunft']->getZeit(), $params['time'] ) );

				$buchbarBis = \DateTime::createFromFormat( 'U', ($this->alterDate( $fahrt->getBuchbarBis(), $params['time'] )) );
				$buchbarBis->setTimezone( new \DateTimeZone( 'Europe/Berlin' ) );

				if ( $buchbarBis->diff( \DateTime::createFromFormat( 'U', $tmp['abfahrt']->getZeit() ) )->format( '%R' ) == '-' ) {
					$buchbarBis->modify( '-1 day' );
				}

				$fahrt->setBuchbarBis( $buchbarBis->getTimestamp() );

				$now = new \DateTime();

				if ( $buchbarBis < $now ) {
					$tmp['allowed'] = FALSE;
				}
				$tmp['dauer'] = ($tmp['ankunft']->getZeit() - $tmp['abfahrt']->getZeit()) / 60;

				$tmp['buchbarBis'] = $buchbarBis;

				if( $bookedRides ) {
					foreach( $bookedRides as $ride ) {

						if( $ride->getFahrt() === $fahrt ) {
							$tmp['bookable'] = false;
						}
					}
				}

				$found[] = $tmp;
			}

			$found = array_slice( $found, 0, 3 );

			return $found;
		}

		protected function getFahrtPreis( $zone1, $zone2 )
		{
			return rand( 20, 100 ) / 10;
		}

		protected function alterDate( $time, $referer )
		{

			$time = \DateTime::createFromFormat( 'U', $time );
			$referer = \DateTime::createFromFormat( 'U', $referer );
			$time->setDate( $referer->format( 'Y' ), $referer->format( 'm' ), $referer->format( 'd' ) );
//			if( $time < $referer ) $time->modify( '+1 day' );
			return $time->getTimestamp();
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
			$timezone =  new \DateTimeZone( 'Europe/Berlin' );
			$date =  new \DateTime( );
			$date->setTimestamp( $booking->getDate() );
			$date->setTimezone( $timezone );

			$departure =  new \DateTime( );
			$departure->setTimestamp( $booking->getDate() );
			$departure->setTimezone( $timezone );

			$arrival =  new \DateTime( );
			$arrival->setTimestamp( $booking->getDate() );
			$arrival->setTimezone( $timezone );


			$times = $booking->getFahrt()->getZeiten();
			foreach( $times as $time ) {
				if( $time->getZone() === $booking->getStartStation()->getZone() ) {
					$timeObj = new \DateTime();
					$timeObj->setTimezone( $timezone );
					$timeObj->setTimestamp( $time->getZeit() );

					$departure->setTime( $timeObj->format( 'H'), $timeObj->format('i') );

					unset( $timeObj );
				}
				elseif( $time->getZone() === $booking->getEndStation()->getZone() ) {
					$timeObj = new \DateTime();
					$timeObj->setTimezone( $timezone );
					$timeObj->setTimestamp( $time->getZeit() );

					$arrival->setTime( $timeObj->format( 'H'), $timeObj->format('i') );
					unset( $timeObj );
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