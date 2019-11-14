<?php

	namespace C3\C3baxi\Controller;

	use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

	class BaxiController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

		/**
		 * @var \C3\C3baxi\Domain\Repository\ZoneRepository
		 * @inject
		 */
		protected $zonenRepository = NULL;
		/**
		 * @var \C3\C3baxi\Domain\Repository\LinieRepository
		 * @inject
		 */
		protected $linieRepository = NULL;

		/**
		 * @var \C3\C3baxi\Domain\Repository\FahrtRepository
		 * @inject
		 */
		protected $rideRepository = NULL;

		/**
		 * @var \C3\C3baxi\Domain\Repository\BookingRepository
		 * @inject
		 */
		protected $bookingRepository = NULL;

		/**
		 * @var \C3\C3baxi\Domain\Repository\CompanyRepository
		 * @inject
		 */
		protected $companies = NULL;

		/**
		 * @var \C3\C3baxi\Domain\Repository\RatingRepository
		 * @inject
		 */
		protected $ratings = NULL;

		/**
		 * action index
		 *
		 * @return void
		 */
		function indexAction() {
			$this->view->assign( 'linien', $this->linieRepository->findAll() );
			$this->view->assign( 'zonen', $this->zonenRepository->findAll() );
			$this->view->assign( 'companies', $this->companies->findAll() );
			$this->view->assign( 'ratings', $this->ratings->findAll() );
			$this->getBookings();
		}

		function detailBookingAction() {
			$date = $this->request->getArgument( 'date' );
			$ride = $this->request->getArgument( 'ride' );
			$found = $this->bookingRepository->findRideOfDay( $date, $ride );

			$dateObject = new \DateTime();
			$dateObject->setTimezone( new \DateTimeZone( 'EUROPE/BERLIN' ) );
			$dateObject->setTimestamp( $date );
			$this->view->assign( 'date', $dateObject );
			$this->view->assign( 'ride', $this->rideRepository->findByUid( $ride ) );
			$this->view->assign( 'bookings', $found );
		}

		function showMapBookingAction() {
			$date = $this->request->getArgument( 'date' );
			$rideUid = $this->request->getArgument( 'ride' );
			$found = $this->bookingRepository->findRideOfDay( $date, $rideUid );

			$ride = $this->rideRepository->findByUid( $rideUid );

			$waypoints = [];

			foreach ( $ride->getZeiten() as $part ) {
				$zone = $part->getZone();

				foreach ( $found as $booking ) {

					if ( $booking->getStartStation()->getZone() === $zone ) {
						$waypoints[] = $booking->getStartStation();
					}
					if ( $booking->getEndStation()->getZone() === $zone ) {
						$waypoints[] = $booking->getEndStation();
					}
				}
			}
			array_walk( $waypoints, 'json_encode');
			$waypoints = array_unique( $waypoints );
			array_walk( $waypoints, 'json_decode');
			$waypoints = array_values( $waypoints );

			$this->view->assign( 'waypoints', $waypoints );

			$this->view->assign( 'ride', $ride);
		}

		protected function getBookings() {

			$bookings = $this->bookingRepository->findAll()->toArray();

			$now = new \DateTime();
			$list = [];

			foreach ( $bookings as $item ) {

				$ts = $item->getDate();
				$ts->setTime( 0, 0 );
				$tstamp = $ts->getTimestamp();

				if ( !isset( $list[$tstamp] ) ) {
					$list[$tstamp] = [];
				}

				$fahrt = $item->getFahrt()->getUid();

				if ( !isset( $list[$tstamp][$fahrt] ) ) {
					$list[$tstamp][$fahrt] = [
						'data'  => $item->getFahrt(),
						'zonen' => []
					];
				}

				foreach ( $item->getFahrt()->getZeiten() as $zeit ) {

					if ( !isset( $list[$tstamp][$fahrt]['zonen'][$zeit->getZone()->getUid()] ) ) {
						$list[$tstamp][$fahrt]['zonen'][$zeit->getZone()->getUid()] = [
							'data'  => $zeit->getZone(),
							'plus'  => 0,
							'minus' => 0
						];
					}

					if ( $zeit->getZone()->getUid() == $item->getStartStation()->getZone()->getUid() ) {
						$list[$tstamp][$fahrt]['zonen'][$zeit->getZone()->getUid()]['plus'] += $item->getAdults();
						$list[$tstamp][$fahrt]['zonen'][$zeit->getZone()->getUid()]['plus'] += $item->getChildren();
					}
					if ( $zeit->getZone()->getUid() == $item->getEndStation()->getZone()->getUid() ) {
						$list[$tstamp][$fahrt]['zonen'][$zeit->getZone()->getUid()]['minus'] += $item->getAdults();
						$list[$tstamp][$fahrt]['zonen'][$zeit->getZone()->getUid()]['minus'] += $item->getChildren();
					}
				}
//				$list[ $tstamp ][$fahrt]['zonen'][] = $item;


			}

			$comming = array_filter( $list, function ( $tstamp ) {
				return ($tstamp > time());
			}, ARRAY_FILTER_USE_KEY );

			$past = array_diff_assoc( $list, $comming );
			$this->view->assign( 'bookings', $comming );
			$this->view->assign( 'booking_past', $past );
		}
	}
