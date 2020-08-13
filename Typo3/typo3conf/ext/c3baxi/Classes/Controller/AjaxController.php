<?php


	namespace C3\C3baxi\Controller;


	use C3\C3baxi\Domain\Model\Haltestelle;
	use C3\C3baxi\Helper\RouteFinder;
	use TYPO3\CMS\Core\Utility\GeneralUtility;
	use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
	use C3\Faq\Domain\Repository\QuestionRepository;
	use C3\C3baxi\Domain\Repository\HaltestelleRepository;
	use C3\C3baxi\Domain\Repository\ZoneRepository;
//	use C3\C3baxi\Controller\FESucheController;
	use \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
	use Psr\Http\Message\ResponseInterface;
	use Psr\Http\Message\ServerRequestInterface;
	use TYPO3\CMS\Extbase\Utility\DebuggerUtility;


	class AjaxController extends ActionController {
		/**
		 * @var \TYPO3\CMS\Extbase\Mvc\View\JsonView
		 */
		protected $view;

		/**
		 * @var string
		 */
		protected $defaultViewObjectName = \TYPO3\CMS\Extbase\Mvc\View\JsonView::class;

		/**
		 * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
		 * @inject
		 */
		protected $persistenceManager;

		/**
		 * @var \C3\C3baxi\Domain\Repository\HaltestelleRepository
		 * @inject
		 */
		protected $haltestellenRepository = NULL;
		/**
		 * @var \C3\C3baxi\Domain\Repository\ZoneRepository
		 * @inject
		 */
		protected $zonenRepository = NULL;
		/**
		 * @var \C3\Faq\Domain\Repository\QuestionRepository
		 * @inject
		 */
		protected $questionRepository;

		/**
		 * @var \C3\C3baxi\Domain\Repository\UserRepository
		 * @inject
		 */
		protected $frontendUserRepository;

		/**
		 * @inject
		 * @var \C3\C3baxi\Utility\GeneralUtility
		 */
		protected $generalUtility;

		public function searchAction() {

		}

		public function autocompleteAction() {
			$result = [];

			if ( $this->request->hasArgument( 'search' ) ) {
				$searchTerm = $this->request->getArgument( 'search' );
				if ( $found = $this->haltestellenRepository->findByName( $searchTerm ) ) {

					foreach ( $found as $haltestelle ) {
						$result[$haltestelle->getUid()] = [
							'name'   => $haltestelle->getName(),
							'zone'   => $haltestelle->getZone()->getUid(),
							'latLng' => $haltestelle->getLatitude() . ',' . $haltestelle->getLongitude()
						];
					}
				}
			} else {
				if ( $found = $this->haltestellenRepository->findAllAssigned() ) {

					$zoneId = FALSE;
					if ( $this->request->hasArgument( 'ignoreZone' ) ) {
						$zoneId = (int) $this->request->getArgument( 'ignoreZone' );
					}

					foreach ( $found as $haltestelle ) {
						if( ! ($haltestelle instanceof Haltestelle) ) continue;
						if ( $haltestelle->getZone()->getUid() === $zoneId || $haltestelle->getZone()->getLinien()->count() == 0) continue;


						$result[$haltestelle->getUid()] = [
							'name'   => $haltestelle->getName(),
							'zone'   => $haltestelle->getZone()->getUid(),
							'latLng' => $haltestelle->getLatitude() . ',' . $haltestelle->getLongitude()
						];
					}
				}
			}

			return json_encode( $result );
		}

		public function saveZone( ServerRequestInterface $request, ResponseInterface $response ) {

			$params = $request->getQueryParams();
			$params = $params['tx_c3baxi_baxisuche'];
			$pid = (int) $params['parentUid'];
			$action = 'add';

			if ( $pid < 0 ) { // remove station
				$pid = $pid * -1;
				$action = 'remove';
			}
			$hid = (int) $params['childUid'];

			$persistenceManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance( 'TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager' );
			$objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance( 'TYPO3\\CMS\\Extbase\\Object\\ObjectManager' );

			$zoneRepository = $objectManager->get( 'C3\\C3baxi\\Domain\\Repository\\ZoneRepository' );
			$haltestelleRepository = $objectManager->get( 'C3\\C3baxi\\Domain\\Repository\\HaltestelleRepository' );
			$output = [
				'pid' => $pid,
				'hid' => $hid
			];
			$haltestelle = $haltestelleRepository->findByUid( $hid );
			$zone = $zoneRepository->findByUid( $pid );

			if ( $zone ) {
				$output['action'] = $action;
				if ( $action === 'add' ) {
					$zone->addHaltestelle( $haltestelle );
					$haltestelle->setZone( $zone );
				} else {
					$zone->removeHaltestelle( $haltestelle );
					$haltestelle->unsetZone();
				}
				$output['zoneCount'] = count( $zone->getHaltestellen() );

				$zoneRepository->update( $zone );
				$haltestelleRepository->update( $haltestelle );
				$persistenceManager->persistAll();
				$response->getBody()->write( json_encode( ['success' => TRUE, 'output' => $output] ) );
			}
			return $response;
		}

		public function saveLinie( ServerRequestInterface $request, ResponseInterface $response ) {

			$params = $request->getQueryParams();
			$params = $params['tx_c3baxi_baxisuche'];
			$pid = (int) $params['parentUid'];
			$action = 'add';

			if ( $pid < 0 ) { // remove station
				$pid = $pid * -1;
				$action = 'remove';
			}
			$hid = (int) $params['childUid'];


			$persistenceManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance( 'TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager' );
			$objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance( 'TYPO3\\CMS\\Extbase\\Object\\ObjectManager' );

			$linieRepo = $objectManager->get( 'C3\\C3baxi\\Domain\\Repository\\LinieRepository' );
			$zoneRepo = $objectManager->get( 'C3\\C3baxi\\Domain\\Repository\\ZoneRepository' );
			$linie = $linieRepo->findByUid( $pid );
			$output = [];
			if ( $linie ) {
				if ( $zone = $zoneRepo->findByUid( $hid ) ) {
					if ( $action === 'add' ) {
						$linie->addZone( $zone );
					} else {
						$linie->removeZone( $zone );
					}
					$linieRepo->update( $linie );

					$persistenceManager->persistAll();

					$output = [$pid, $hid];

					$response->getBody()->write( json_encode( ['success' => TRUE, 'output' => $output] ) );
				}
			}

			return $response;
		}

		public function stationDetailAction() {

			if ( $this->request->hasArgument( 'uid' ) ) {
				$uid = $this->request->getArgument( 'uid' );

				$station = $this->haltestellenRepository->findByUid( $uid );
				$linienListe = [];
				foreach ( $station->getZone()->getLinien() as $linie ) {
					$linienListe[] = $linie->getNr();
				}
				$result = [
					'uid'    => $station->getUid(),
					'name'   => $station->getName(),
					'zone'   => $station->getZone()->getName(),
					'zoneId' => $station->getZone()->getUid(),
					'linie'  => $linienListe,
					'latLng' => $station->getLatitude() . ',' . $station->getLongitude()
				];
			} else {
				$result = ['error' => 'no UID'];
			}
			return json_encode( $result );
		}

		public function helpAction() {
			$result = FALSE;
//			DebuggerUtility::var_dump( $this->request );
//			exit;
			if ( $this->request->hasArgument( 'question' ) ) {
				$quid = $this->request->getArgument( 'question' );
				$object = $this->questionRepository->findByUid( $quid );
				$media = $this->generalUtility->generateMediaList( $object->getAssets(), $this->request );

				$result = [
					'question' => $object->getQuestion(),
					'answer'   => $object->getAnswer(),
					'media'    => $media
				];
			}

			return json_encode( $result );
		}

		public function favoritesAction() {
			if ( !isset( $GLOBALS["TSFE"]->fe_user->user['uid'] ) ) return json_encode( ['status' => 'not_logged_in'] );

			$result = [];

			switch ( $this->request->getArgument( 'doAction' ) ) {
				case 'add' :
					$result = $this->addFavorite();
					break;
				case 'remove' || 'delete' :
					$result = $this->removeFavorite();
					break;

				case 'findAll' :
				default :
					if ( !($favorites = $this->getFavorites()) ) {
						$result = FALSE;
						break;
					}

					array_walk( $favorites, function ( $item ) {
						$station = $this->haltestellenRepository->findByUid( $item->station );
						$item->latLng = $station->getLatitude() . ',' . $station->getLongitude();
						return $item;
					} );
					$result['data'] = $favorites;
			}

			return json_encode( $result );
		}

		protected function addFavorite() {

			if ( !($favorites = $this->getFavorites()) || !$this->request->hasArgument( 'uid' ) ) return FALSE;

			if ( $favorites == '' ) $favorites = [];
			elseif ( is_string( $favorites ) ) $favorites = json_decode( $favorites );


			if ( $this->request->hasArgument( 'uid' ) ) {
				$uid = $this->request->getArgument( 'uid' );
				$name = $this->request->getArgument( 'name' );
				$favorites[] = ['station' => $uid, 'name' => $name];

				$this->setFavorites( $favorites );

				return (['status' => 'added']);
			}
		}

		protected function removeFavorite() {
			if ( !($favorites = $this->getFavorites()) || !$this->request->hasArgument( 'uid' ) ) return FALSE;

			$sid = $this->request->getArgument( 'uid' ); // station id (sid)

			$favorites = array_filter( $favorites, function ( $fav ) use ( $sid ) {
				return $fav->station != $sid;
			} );
			$favorites = array_values( $favorites );

			$this->setFavorites( $favorites );

			return (['status' => 'removed']);
		}

		protected function getFavorites() {
			if ( !isset( $GLOBALS["TSFE"]->fe_user->user['uid'] ) )
				return FALSE;

			$uuid = $GLOBALS["TSFE"]->fe_user->user['uid'];
			$user = $this->frontendUserRepository->findByUid( $uuid );
			$favorites = $user->getTxC3baxiFavorites();

			if ( $favorites == '' ) $favorites = [];
			else $favorites = json_decode( $favorites );

			return $favorites;
		}

		protected function setFavorites( $favorites ) {
			if ( !isset( $GLOBALS["TSFE"]->fe_user->user['uid'] ) )
				return FALSE;

			$uuid = $GLOBALS["TSFE"]->fe_user->user['uid'];
			$user = $this->frontendUserRepository->findByUid( $uuid );

			$user->setTxC3baxiFavorites( json_encode( $favorites ) );
			$this->frontendUserRepository->update( $user );
			$this->persistenceManager->persistAll();

			return TRUE;
		}

		public function rideAction() {
			$action = $this->request->hasArgument( 'doAction' ) ? $this->request->getArgument( 'doAction' ) : 'find';
			$result = ['status' => FALSE, 'data' => NULL];

			switch ( $action ) {

				case 'cancel':

					break;

				case 'find':
				default :
					if ( $this->request->hasArgument( 'startStation' )
						&& $this->request->hasArgument( 'endStation' ) ) {

						$startUid = $this->request->getArgument( 'startStation' );
						$start = $this->haltestellenRepository->findByUid( $startUid );

						$endUid = $this->request->getArgument( 'endStation' );
						$end = $this->haltestellenRepository->findByUid( $endUid );

						$date = $this->request->getArgument( 'date' );
						$time = $this->request->getArgument( 'time' );
						$searchTime = new \DateTime( $date . ' ' . $time, new \DateTimeZone( 'EUROPE/BERLIN' ) );

						$routeFinder = GeneralUtility::makeInstance( RouteFinder::class, $start, $end, $searchTime );
						$routeFinder->findLine();
						$routeFinder->findRoutes();
						if ( $routeFinder->getRouteCount() ) {
							$result['status'] = TRUE;
							$result['data'] = [
								'linie'  => $routeFinder->getFoundLine(),
								'start'  => $startUid,
								'end'    => $endUid,
								'date'   => $searchTime,
								'routes' => $routeFinder->getFoundRoutes()
							];
						} else {
							$result['status'] = 'not_found';
						}
					}
			}

			return json_encode( $result );
		}

		public function ratingAction() {
			$arguments = $this->request->getArguments( );

			switch( $arguments['doAction'] ) {

				case 'addRating' :
					$this->forward( 'create', 'Rating', 'C3baxi', $arguments );
					break;
			}

		}
	}