<?php


namespace C3\C3baxi\Controller;

use C3\C3baxi\Domain\Model\Booking;
use C3\C3baxi\Domain\Model\Linie;
use C3\C3baxi\Domain\Model\User;
use C3\C3baxi\Utility\GeneralUtility;
use Doctrine\Common\Util\Debug;
use In2code\Femanager\Domain\Repository\UserGroupRepository;
use Psr\Log\LogLevel;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\DataHandling\Model\RecordStateFactory;
use TYPO3\CMS\Core\DataHandling\SlugHelper;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

class BookingController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
	/**
	 * @var \C3\C3baxi\Domain\Repository\BookingRepository
	 * @inject
	 */
	protected $repository = null;
	/**
	 * @var \C3\C3baxi\Domain\Repository\RatingRepository
	 * @inject
	 */
	protected $ratings = null;
	/**
	 * @var \C3\C3baxi\Domain\Repository\CompanyRepository
	 * @inject
	 */
	protected $companies = null;
	/**
	 * @var \C3\C3baxi\Domain\Repository\HaltestelleRepository
	 * @inject
	 */
	protected $stations = null;
	/**
	 * @var \C3\C3baxi\Domain\Repository\UserRepository
	 * @inject
	 */
	protected $users = null;

	/**
	 * @var \C3\C3baxi\Domain\Repository\LinieRepository
	 * @inject
	 */
	protected $lines = null;

	/**
	 * @var \In2code\Femanager\Domain\Repository\UserGroupRepository
	 * @inject
	 */
	protected $usergroups = null;

	/**
	 * @var \C3\C3baxi\Domain\Repository\FahrtRepository
	 * @inject
	 */
	protected $fahrten = null;
	/**
	 * @inject
	 * @var \C3\C3baxi\Domain\Repository\TtContentRepository
	 */
	protected $ttContentRepository;

	/**
	 * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
	 * @inject
	 */
	protected $persistenceManager;

	protected $uriBuilder;

	public function __construct() {
		$objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);
		$this->uriBuilder = $objectManager->get(\TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder::class);
		$this->uriBuilder
			->setUseCacheHash(false)
			->setNoCache(true)
			->setTargetPageUid(1)
			->setTargetPageType(666);
		parent::__construct();
	}

	function indexAction() {

	}

	function listAction() {
		if ( TYPO3_MODE == 'BE' ) {
			$bookings = $this->repository->findAll()->toArray();
		} else {

			if ( !isset($GLOBALS[ 'TSFE' ]->fe_user) ) return;

			$userUid = $GLOBALS[ 'TSFE' ]->fe_user->user[ 'uid' ];
			if ( !$userUid ) return;
			$bookings = $this->repository->findByUser($userUid)->toArray();
			$this->initiateJSSettings();
		}

		// Get Content values
		if( $GLOBALS[ 'TSFE' ]->currentRecord ) {
			list( $CType, $currentContentId ) = explode( ':', $GLOBALS['TSFE']->currentRecord );
			$content = $this->ttContentRepository->findByUid( $currentContentId )->getFirst();
			$this->view->assign( 'content', $content );

			// FAQ Link
			$headerLink = $content->getHeaderLink();
			if ( $headerLink != '' ) {
				preg_match( '/&uid=([0-9]{1,})/', $headerLink, $faqUid );
				$this->view->assign( 'helpUid', $faqUid[1] );
			}
		}

		if ( count($bookings) > 0 ) {

			$now = new \DateTime();
			$comming = $past = [];

			array_walk($bookings, function ($item, $idx, $now) use (&$comming, &$past) {
				if ( $item->getDate() > $now ) {
					$comming[] = $item;
				} else {
					$past[] = $item;
				}
			}, $now);

			$this->view->assign('booking_comming', $comming);
			$this->view->assign('booking_past', $past);
		}
	}

	protected function initiateJSSettings() {
		$pageRender = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Page\PageRenderer::class);
		$this->uriBuilder->reset()
			->setCreateAbsoluteUri(true)
			->setTargetPageUid(1)
			->setTargetPageType(666);

		$baxiSettings = [
			'ticketType' => 'adult',
			'loggedIn' => isset($GLOBALS[ "TSFE" ]->fe_user->user[ 'uid' ]),
			//				'favorites'  => isset( $GLOBALS["TSFE"]->fe_user->user['uid'] ) ? $this->getUserFavorites() : [],
			'ajaxUrl' => [
				'haltestelle' => $this->uriBuilder->setArguments([])
					->uriFor('autocomplete', ['type' => 'haltestelle'], 'Ajax', 'C3baxi', 'BaxiSuche'),
				'station' => $this->uriBuilder->setArguments([])
					->uriFor('stationDetail', [], 'Ajax', 'C3baxi', 'BaxiSuche'),
				'favorites' => $this->uriBuilder->setArguments([])
					->uriFor('favorites', ['doAction' => 'findAll'], 'Ajax', 'C3baxi', 'BaxiSuche'),
				'help' => $this->uriBuilder->setArguments([])
					->uriFor('help', [], 'Ajax', 'C3baxi', 'BaxiSuche'),
				'rating' => $this->uriBuilder->setArguments([])
					->uriFor('rating', [], 'Ajax', 'C3baxi', 'BaxiSuche'),
				/** deprecated */
				'addFavorite' => $this->uriBuilder->setArguments([])
					->uriFor('addFavorite', [], 'Ajax', 'C3baxi', 'BaxiSuche'),
				'removeFavorite' => $this->uriBuilder->setArguments([])
					->uriFor('removeFavorite', [], 'Ajax', 'C3baxi', 'BaxiSuche'),
			],
			'mapCenter' => [ // default is Tirschenreuth, Germany
				'lat' => 49.8817161,
				'lng' => 12.3303441
			]
		];
		$pageRender->addJsFooterInlineCode('baxiSearchSettings', 'var baxiSearchSettings = ' . json_encode($baxiSettings));
	}

	function listBEAction() {
		$bookings = $this->repository->findAll();
	}

	function newAction() {

		$zoneList = false;
		$timeList = [];
		$interval = new \DateInterval('PT30M');
		$period = new \DatePeriod(new \DateTime('2019-1-1 05:00:00'), $interval, new \DateTime('2019-1-1 21:00:00'));
		foreach ( $period as $p ) {
			$timeList[ $p->format("H:i") ] = $p->format("H:i");
		}
		$this->view->assign('timeList', $timeList);

		if ( $this->request->hasArgument('date') && $this->request->hasArgument('ride') ) {
			$datum = new \DateTime();
			$datum->setTimestamp($this->request->getArgument('date'));
			$datum->setTimezone(new \DateTimeZone('EUROPE/BERLIN'));
			$this->view->assign('datum', $datum);

			$ride = $this->fahrten->findByUid($this->request->getArgument('ride'));

			$time = $ride->getBuchbarBis();
//				$time->setTimezone( new \DateTimeZone( 'EUROPE/BERLIN' ) );
			$minutes = $time->format('i');

			$diff = $minutes;
			if ( $minutes >= 30 ) {
				$diff = $minutes - 30;
			}
			$time->sub(new \DateInterval('PT' . abs($diff) . 'M'));

			$this->view->assign('time', $time->format('H:i'));

			$zoneList = [];
			foreach ( $ride->getLinie()->getZonen() as $zone ) {
				$zoneList[] = $zone->getUid();
			}
		}

		$this->view->assign('users', $this->users->findAll());

		$stations = $this->_getAvailableStations($zoneList);

		$date = new \DateTimeImmutable();
		$this->view->assign('datum', $date);
		$this->view->assign('minDate', $date->modify('-2 Days')->format('Y-m-d'));
		$this->view->assign('stations', $stations);
		/**
		 * @var BackendUserAuthentication $GLOBALS ['BE_USER']
		 */
		$this->view->assign('beUser', $GLOBALS[ 'BE_USER' ]->user[ 'uid' ]);
	}

	protected function _getAvailableStations($zoneList = false, $cityLineOnly = false) {
		$lines = $this->lines->findAll()->toArray();

		if( isset($GLOBALS['BE_USER']) ) {
			if ( $GLOBALS[ 'BE_USER' ]->isMemberOfGroup(1) ) {
				$userId = $GLOBALS[ 'BE_USER' ]->user[ 'uid' ];
				$company = $this->companies->findOneByUser($userId);
				$lines = $company->getRoutes()->toArray();
			}
		}

		$stations = [];
		if($lines) {
			foreach ( $lines as $linie ) {
				if ( !$linie->isHidden() ) {

					$zonen = $linie->getZonen()->toArray();

					foreach ( $zonen as $zone ) {
						foreach ( $zone->getHaltestellen() as $haltestelle ) {
							if ( !isset($stations[ $haltestelle->getUid() ]) ) {
								$stations[ $haltestelle->getUid() ] = [
									'data' => $haltestelle,
									'linien' => []
								];
							}
							$stations[ $haltestelle->getUid() ][ 'linien' ][] = $linie->getNr();
						}
					}
				}
			}
		}
		array_walk($stations, function (&$station) {
			$station[ 'linien' ] = array_unique($station[ 'linien' ]);
		});

		return $stations;
	}

	function newCityLineAction() {
		$stations = [];

		$timeList = [];
		$interval = new \DateInterval('PT30M');
		$period = new \DatePeriod(new \DateTime('2019-1-1 05:00:00'), $interval, new \DateTime('2019-1-1 21:00:00'));
		foreach ( $period as $p ) {
			$timeList[ $p->format("H:i") ] = $p->format("H:i");
		}
		$this->view->assign('timeList', $timeList);

		$lines = $this->lines->findByCityLine(1);

		$zoneList = [];
		/**
		 * @var Linie $line
		 */
		foreach ( $lines as $line ) {
			foreach ( $line->getZonen() as $zone ) {
				$zoneList[] = $zone->getUid();
			}
		}

		$stations = $this->_getAvailableStations($zoneList, true);

		$date = new \DateTimeImmutable();

		$this->view->assign('users', $this->users->findAll());

		$this->view->assign('datum', $date);
		$this->view->assign('minDate', $date->modify('-2 Days')->format('Y-m-d'));
		$this->view->assign('stations', $stations);
	}

	function createAction() {

		$arguments = $this->request->getArguments();

		if ( isset($arguments[ 'fahrt' ][ 'to' ]) && is_array($arguments[ 'fahrt' ]) ) {

			if ( $arguments[ 'new_user' ] == 1 ) {
				$user = $this->_createUser($arguments[ 'newUser' ]);
				unset( $arguments['new_user'], $arguments['newUser'] );
				$arguments['user'] = $user->getUid();
			}

			$rideTo = $arguments;
			$rideTo[ 'fahrt' ] = $rideTo[ 'fahrt' ][ 'from' ];
			$rideTo[ 'date' ] = floor($rideTo[ 'date' ][ $rideTo[ 'fahrt' ] ]);
			$rideTo['startStation'] = $arguments['endStation'];
			$rideTo['endStation'] = $arguments['startStation'];

			$rideFrom = $arguments;
			$rideFrom[ 'fahrt' ] = $rideFrom[ 'fahrt' ][ 'to' ];
			$rideFrom[ 'date' ] = floor($rideFrom[ 'date' ][ $rideFrom[ 'fahrt' ] ]);

			 $this->_createBooking($rideTo);
			 $this->_createBooking($rideFrom);
		}
		else {
			$this->_createBooking($arguments);
		}


//		if ( !$error ) {

//				/** @var \TYPO3\CMS\Core\Messaging\FlashMessageService $flashMessageService */
//				$flashMessageService = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Messaging\FlashMessageService::class);
//				/** @var \TYPO3\CMS\Core\Messaging\FlashMessageQueue $defaultFlashMessageQueue */
//				$defaultFlashMessageQueue = $flashMessageService->getMessageQueueByIdentifier();
//				$defaultFlashMessageQueue->enqueue($flashMessage);

		$this->redirect('index', 'Baxi', null, ['bookedRide' => $arguments[ 'fahrt' ], 'date' => $arguments[ 'date' ]]);
//		} else {
//			$this->redirect('index', 'Baxi', null, ['bookedRide' => $arguments[ 'fahrt' ], 'date' => $arguments[ 'date' ]]);
//		}
	}

	private function _createBooking($data) {

		$booking = new Booking();
		$date = new \DateTime();

//		if(is_string($data['date']))
//			$data['date'] = strtotime($data[ 'date' ]);

		$date->setTimestamp( $data[ 'date' ] );
		$booking->setDate($date);

		$booking->setAdults(intval($data[ 'adults' ]));
		$booking->setChildren(intval($data[ 'children' ]));
		$booking->setInfo($data[ 'info' ]);

		$error = false;

		if ( $startStation = $this->stations->findByUid($data[ 'startStation' ]) ) {
			$booking->setStartStation($startStation);
		} else {
			$error = true;
		}

		if ( $endStation = $this->stations->findByUid($data[ 'endStation' ]) ) {
			$booking->setEndStation($endStation);
		} else {
			$error = true;
		}

		if ( isset($GLOBALS[ 'BE_USER' ]) ) {
			$booking->setCruserId($GLOBALS[ 'BE_USER' ]->user[ 'uid' ]);
		}

		if ( $data[ 'new_user' ] == 1 ) {

			$user = $this->_createUser($data[ 'newUser' ]);
			$booking->setUser($user);
		} else {
			if ( $user = $this->users->findByUid($data[ 'user' ]) ) {
				$booking->setUser($user);
			} else {
				$error = true;
			}
		}

		if ( $fahrt = $this->fahrten->findByUid($data[ 'fahrt' ]) ) {
			$booking->setFahrt($fahrt);
		} else {
			$error = true;
		}

		list($confirmed, $approved) = $this->_checkStatus($data[ 'date' ], $fahrt);

		if ( $GLOBALS[ 'BE_USER' ]->isMemberOfGroup(1) ) { // Unternehmen
			$booking->setApproved($approved);
			$booking->setConfirmed(true);
		} elseif ( $GLOBALS[ 'BE_USER' ]->isMemberOfGroup(2) ) { // Fahrtwunschzentrale
			$booking->setApproved(true);
			$booking->setConfirmed($confirmed);
		}

		$booking->setInfo($data[ 'info' ]);
		$booking->setPid(14);

		if ( !$error ) {
			$this->repository->add($booking);
			return true;
		}

		return false;
	}

	private function _createUser($data = []) {
		$user = new User();
		$pid = 14;

		$username = $data[ 'email' ];
		if ( empty($username) || filter_var($username, FILTER_VALIDATE_EMAIL) ) {
			$username = $data[ 'name' ];
			$username = strtolower($username);
			$username = str_replace(' ', '.', $username);
		}

		$user->setUsername($username);
		$user->setPasswordAutoGenerated($username);
		$user->setEmail($data[ 'email' ]);
		$user->setName($data[ 'name' ]);
		$user->setTelephone($data[ 'telephone' ]);
		$user->setPid($pid);
		$group = $this->usergroups->findByUid(1);
		$user->addUsergroup($group);

		if ( $data[ 'telephoneNotify' ] == 1 )
			$user->setTxC3baxiNotificationTelephone(true);

		if ( $data[ 'emailNotify' ] == 1 )
			$user->setTxC3baxiNotificationEmail(true);

		$this->users->add($user);
		$this->persistenceManager->persistAll();
// UPDATE fe_users SET username = LOWER( REPLACE(name,' ','.')) WHERE username='' OR username IN ('x','xx')
// UPDATE fe_users SET email = '' WHERE email IN ('x','xx')
		$slugHelper = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
			SlugHelper::class,
			'fe_users',
			'username',
			[
				'generatorOptions' => [
					'fields' => ['name'],
					'fieldSeparator' => '/',
					'replacements' => [
						'/' => '',
					],
				],
				'fallbackCharacter' => '.',
				'eval' => 'unique',
				'default' => '',
			]
		);

		$evalInfo = ['unique'];
		$slug = $slugHelper->generate($data, $pid);
		$state = RecordStateFactory::forName('fe_users')
			->fromArray($data, $pid, $user->getUid());

		if ( in_array('uniqueInSite', $evalInfo) ) {
			$slug = $slugHelper->buildSlugForUniqueInSite($slug, $state);
		} elseif ( in_array('uniqueInPid', $evalInfo) ) {
			$slug = $slugHelper->buildSlugForUniqueInPid($slug, $state);
		} elseif ( in_array('unique', $evalInfo) ) {
			$slug = $slugHelper->buildSlugForUniqueInTable($slug, $state);
		}

		$user->setUsername($slug);
		$this->users->update($user);
		return $user;
	}

	private function _checkStatus($date, $fahrt) {
		$found = $this->repository->findRideOfDay($date, $fahrt);
		$confirmed = $approved = false;

		if ( $found->count() != 0 ) {
			/**
			 * @var Booking $booking
			 */
			$booking = $found->getFirst();
			$approved = $booking->isApproved();
			$confirmed = $booking->isConfirmed();
			foreach ( $found as $booking ) {
				if ( $GLOBALS[ 'BE_USER' ]->isMemberOfGroup(1) ) { // unternehmen
					if ( false === $booking->isConfirmed() ) {
						$booking->setConfirmed(true);
					}
				} elseif ( $GLOBALS[ 'BE_USER' ]->isMemberOfGroup(2) ) { // fahrtwunschzentrale
					if ( false === $booking->isApproved() ) {
						$booking->setApproved(true);
					}
				}
				$this->repository->update($booking);
			}
		} else {
			if ( $GLOBALS[ 'BE_USER' ]->isMemberOfGroup(1) ) { // unternehmen
				$approved = false;
				$confirmed = true;
			} elseif ( $GLOBALS[ 'BE_USER' ]->isMemberOfGroup(2) ) { // fahrtwunschzentrale
				$approved = true;
				$confirmed = false;
			}
		}

		return [$confirmed, $approved];
	}

	/**
	 * @param Booking $booking
	 * @throws \Exception
	 */
	function editAction(Booking $booking) {
		$this->initiateJSSettings();
		$timeList = [];
		$interval = new \DateInterval('PT30M');
		$period = new \DatePeriod(new \DateTime('2019-1-1 05:00:00'), $interval, new \DateTime('2019-1-1 21:00:00'));
		foreach ( $period as $p ) {
			$timeList[ $p->format("H:i") ] = $p->format("H:i");
		}

		$startStations = $booking->getStartStation()->getZone()->getHaltestellen();
		$endStations = $booking->getEndStation()->getZone()->getHaltestellen();

		$date = new \DateTimeImmutable();

		$this->view->assignMultiple([
			'timeList' => $timeList,
			'booking' => $booking,
			'datum' => $date,
			'minDate' => $date->modify('-2 Days'),
			'startStations' => $startStations,
			'endStations' => $endStations
		]);
	}

	/**
	 * @param Booking|null $booking
	 */
	function updateAction(Booking $booking = null) {
		$date = new \DateTimeImmutable($this->request->getArgument('date'));
		$date = $this->alterDate($booking->getDate(), $date, true);
		$booking->setDate($date);
//			$date = $booking->getDate()
//
		$this->persistenceManager->update($booking);
		$this->redirect('index', 'Baxi');
	}

	protected function alterDate($time, $referer, $asObject = false) {
		if ( is_int($time) ) $time = \DateTime::createFromFormat('U', $time);
		if ( is_int($referer) ) $referer = \DateTime::createFromFormat('U', $referer);

		$time->setDate($referer->format('Y'), $referer->format('m'), $referer->format('d'));
		return $asObject ? $time : $time->getTimestamp();
	}

	/**
	 * @param Booking $booking
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
	 * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
	 * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
	 */
	function cancelAction(Booking $booking) {

		$booking->setDeleted(1);
		$this->repository->update($booking);

//			$this->sendEmailCanceled( $booking );

		if ( isset($GLOBALS[ 'BE_USER' ]) ) {
			$this->redirect('index', 'Baxi', null, null);
		} else {
			$this->redirectToUri('/konto/gebuchte-fahrten', 0);
		}
	}

	function detailAction(Booking $booking) {

		$this->initiateJSSettings();
		$now = new \DateTime();

		$date = $booking->getDate();

		$buchbarBis = $booking->getFahrt()->getBuchbarBis();

		$buchbarBis = GeneralUtility::combineDate($buchbarBis, $date);

		$departure = $booking->getFahrt()->getStationTime($booking->getStartStation());
		$arrival = $booking->getFahrt()->getStationTime($booking->getEndStation());

		$ratingAllowed = true;
		$foundRating = $this->ratings->findForUser($GLOBALS[ "TSFE" ]->fe_user->user[ 'uid' ], $booking->getUid());
		if ( $foundRating->count() > 0 ) $ratingAllowed = false;

		$this->view->assign('departure', $departure);
		$this->view->assign('arrival', $arrival);
		$this->view->assign('isComming', $buchbarBis > $now);
		$this->view->assign('rating_allowed', $ratingAllowed);
		$this->view->assign('now', $now);
		$this->view->assign('booking', $booking);
	}

	/**
	 * @param int $days
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
	 */
	function exportAction($days = 3) {


		if($days > 0 ) {
			$dateFrom = new \DateTime();
			$dateFrom->modify((-1 * $days) . ' days');

			$demand = [
				'datefrom' => $dateFrom
			];
		}
		elseif( $days === -1 ) {

			$date = date('Y-m', strtotime('last month'));
			$demand = [
				'datefrom' => strtotime('first day of ' . $date),
				'dateto' => strtotime('last day of ' . $date . ' 23:59')
			];
		}


		$bookings = $this->repository->findByDemand($demand)->toArray();

		if( $GLOBALS[ 'BE_USER' ]->isMemberOfGroup(1) ) {
			$userId = $GLOBALS[ 'BE_USER' ]->user[ 'uid' ];
			$company = $this->companies->findOneByUser($userId);
			$routes = $company->getRoutes();

			foreach ( $bookings as $booking ) {

				if ( $routes->contains($booking->getFahrt()->getLinie()) ) {
					$tmp[] = $booking;
				}
			}

			$bookings = $tmp;
		}

		$csvData = [
			['Linie',
				'Fahrt',
				'Abfhart',
				'Einstiegshaltestelle',
				'Austiegshaltestelle',
				'Kunde',
				'Kundennummer',
				'Email',
				'Telefon',
				'Erwachsene',
				'Kinder',
				'Notiz',
				'Unternehmen'
			]
		];
		foreach ( $bookings as $booking ) {
			// {buchung.date -> f:format.date(format:'%d.%m.%Y')} {buchung.date -> f:format.date(format:'H:i')}

			$row = [
				$booking->getFahrt()->getLinie()->getNr(),
				' ' . str_pad($booking->getFahrt()->getName(), 3, '0'),
				$booking->getDate()->format('Y-m-d H:i'),
				$booking->getStartStation()->getFullname(),
				$booking->getEndStation()->getFullname(),
				$booking->getUser() ? $booking->getUser()->getName() : null,
				$booking->getUser() ? $booking->getUser()->getUid() : null,
				$booking->getUser() ? $booking->getUser()->getEmail() : null,
				$booking->getUser() ? $booking->getUser()->getTelephone() : null,
				$booking->getAdults(),
				$booking->getChildren(),
				strval(' ' . $booking->getInfo()),
				$booking->getFahrt()->getLinie()->getCompany()->getName()
			];

			$csvData[] = $row;
		}

		/** Generate CSV */
		$fp = fopen('php://temp', 'w+');
		fputs($fp, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));
		foreach ( $csvData as $row ) {
			fputcsv($fp, $row, ';', '"', '\\');
		}
		rewind($fp);
//		echo ;

		$this->response->setHeader('Content-Type', 'text/csv', true);
		$this->response->setHeader('Content-Disposition', 'attachment; filename="buchungen_export_' . date('Y-m-d') . '.csv"');

		$this->throwStatus(200,
			'ok', stream_get_contents($fp));
		fclose($fp);
		exit;
	}


	protected function emitBeforeCallActionMethodSignal(array $preparedArguments) {
		parent::emitBeforeCallActionMethodSignal($preparedArguments); // TODO: Change the autogenerated stub
	}

	protected function sendEmailCompanyConfirm(Booking $booking) {

		$mailTo = $booking->getFahrt()->getLinie()->getCompany()->getEmail();
		if ( $mailTo === '' ) return false;

		if ( substr_count($mailTo, ';') > 0 ) {
			$mailTo = explode(';', $mailTo);
		}

		// build content for email
//			$timezone = new \DateTimeZone( 'Europe/Berlin' );
		$date = new \DateTime();

		$date->setTimestamp($booking->getDate());
//			$date->setTimezone( $timezone );

		// create email html
		/** @var \TYPO3\CMS\Fluid\View\StandaloneView $emailView */
		$emailView = $this->objectManager->get('TYPO3\\CMS\\Fluid\\View\\StandaloneView');

		$extPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('c3baxi');

		$templateRootPath = $extPath . "Resources/Private/Mail/";

		$emailView->setLayoutRootPaths([$templateRootPath . 'Layouts']);
		$emailView->setPartialRootPaths([$templateRootPath . 'Partials']);
		$emailView->setTemplateRootPaths([$templateRootPath . 'Templates']);
		$emailView->setTemplate('CompanyConfirm.html');


		$emailView->assignMultiple(['booking' => $booking, 'date' => $date/*, 'arrival' => $arrival, 'departure' => $departure*/]);
		$emailHtmlBody = $emailView->render();

		$sender = 'info@fahrmit-baxi.de';
		$subject = 'BAXI - Fahrt Bestätigung';

		/**
		 * @var MailMessage $message
		 */
		$message = $this->objectManager->get('TYPO3\\CMS\\Core\\Mail\\MailMessage');
		$message->setTo($mailTo)
			->setFrom($sender)
			->setSubject($subject);

		if ( (bool)$this->settings[ 'email' ][ 'debug' ] === true ) {
			$message->setBcc($this->settings[ 'email' ][ 'debugEmail' ]);
		}

		// HTML Email
		$message->addPart($emailHtmlBody, 'text/html');
		$message->send();
		return $message->isSent();
	}

	protected function sendEmailCanceled($booking) {

		$mailTo = $booking->getUser()->getEmail();
		if ( $mailTo === '' ) return false;

		if ( substr_count($mailTo, ';') > 0 ) {
			$mailTo = explode(';', $mailTo);
		}

		// build content for email
//			$timezone = new \DateTimeZone( 'Europe/Berlin' );
		$date = new \DateTime();
		$date->setTimestamp($booking->getDate()->getTimestamp());
//			$date->setTimezone( $timezone );

		// create email html
		/** @var \TYPO3\CMS\Fluid\View\StandaloneView $emailView */
		$emailView = $this->objectManager->get('TYPO3\\CMS\\Fluid\\View\\StandaloneView');

		$extPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('c3baxi');
		$templateRootPath = $extPath . "Resources/Private/Templates/";

		$templatePathAndFilename = $templateRootPath . 'Email/Booking/canceled.html';
		$emailView->setTemplatePathAndFilename($templatePathAndFilename);

		$emailView->assignMultiple(['booking' => $booking, 'date' => $date/*, 'arrival' => $arrival, 'departure' => $departure*/]);
		$emailHtmlBody = $emailView->render();

		$sender = 'info@fahrmit-baxi.de';
		$subject = 'BAXI - Bestaetigung';

		$message = $this->objectManager->get('TYPO3\\CMS\\Core\\Mail\\MailMessage');
		$message->setTo($mailTo)
			->setFrom($sender)
			->setSubject($subject);

		if ( (bool)$this->settings[ 'email' ][ 'debug' ] === true ) {
			$message->setBcc($this->settings[ 'email' ][ 'debugEmail' ]);
		}

		// HTML Email
		$message->addPart($emailHtmlBody, 'text/html');
		$message->send();
		return $message->isSent();
	}

}