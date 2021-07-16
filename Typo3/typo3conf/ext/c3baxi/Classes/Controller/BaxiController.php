<?php

namespace C3\C3baxi\Controller;

use C3\C3baxi\Domain\Model\Booking;
use TYPO3\CMS\Beuser\Domain\Repository\BackendUserRepository;
use TYPO3\CMS\Core\Error\DebugExceptionHandler;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

class BaxiController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

	/**
	 * @var \C3\C3baxi\Domain\Repository\ZoneRepository
	 * @inject
	 */
	protected $zonenRepository = null;
	/**
	 * @var \C3\C3baxi\Domain\Repository\LinieRepository
	 * @inject
	 */
	protected $linieRepository = null;

	/**
	 * @var \C3\C3baxi\Domain\Repository\FahrtRepository
	 * @inject
	 */
	protected $rideRepository = null;

	/**
	 * @var \C3\C3baxi\Domain\Repository\BookingRepository
	 * @inject
	 */
	protected $bookingRepository = null;

	/**
	 * @var \C3\C3baxi\Domain\Repository\CompanyRepository
	 * @inject
	 */
	protected $companies = null;

	/**
	 * @var \C3\C3baxi\Domain\Repository\HolidayRepository
	 * @inject
	 */
	protected $holidays = null;

	/**
	 * @var \C3\C3baxi\Domain\Repository\RatingRepository
	 * @inject
	 */
	protected $ratings = null;

	/**
	 * @var \C3\C3baxi\Domain\Repository\SubscriptionRepository
	 * @inject
	 */
	protected $subscriptions = null;

	/**
	 * @var TYPO3\CMS\Beuser\Domain\Repository\BackendUserRepository
	 * @inject
	 */
	protected $beUsers = null;

	/**
	 * action index
	 *
	 * @return void
	 */
	function indexAction() {

		$assigns = [];

		if ( $GLOBALS[ 'BE_USER' ]->isMemberOfGroup(1) ) { // Unternehmen
			$assigns['ratings'] = $this->ratings->findAll();

			$userId = $GLOBALS[ 'BE_USER' ]->user[ 'uid' ];
			$company = $this->companies->findOneByUser($userId);
			$assigns['linien'] = $this->linieRepository->findByCompany($company->getUid());

			$this->getBookings($company->getRoutes());
		}
		else { // Fahrtwunschzentrale, LRA
			$assigns['linien'] = $this->linieRepository->findAll();

			$assigns['companies'] = $this->companies->findAll();
			$assigns['ratings'] = $this->ratings->findAll();
			$assigns['subscriptions'] = $this->subscriptions->findAll();
			$this->getBookings();
		}

		$assigns['holidays'] = $this->holidays->findAll();
		$assigns['datenow'] = new \DateTime();
		$assigns['zonen'] = $this->zonenRepository->findAll();
		$assigns['BE_USER'] = $GLOBALS[ 'BE_USER' ]->user[ 'uid' ];

		$assigns['rights'] = [
			'booking' => [
				'read'=>$GLOBALS['BE_USER']->check('tables_select','tx_c3baxi_domain_model_booking'),
				'modify'=>$GLOBALS['BE_USER']->check('tables_modify','tx_c3baxi_domain_model_booking')],
			'subscription' => [
				'read'=>$GLOBALS['BE_USER']->check('tables_select','tx_c3baxi_domain_model_subscription'),
				'modify'=>$GLOBALS['BE_USER']->check('tables_modify','tx_c3baxi_domain_model_subscription')],
			'fahrt' => [
				'read'=>$GLOBALS['BE_USER']->check('tables_select','tx_c3baxi_domain_model_fahrt'),
				'modify'=>$GLOBALS['BE_USER']->check('tables_modify','tx_c3baxi_domain_model_fahrt')],
			'linie' => [
				'read'=>$GLOBALS['BE_USER']->check('tables_select','tx_c3baxi_domain_model_linie'),
				'modify'=>$GLOBALS['BE_USER']->check('tables_modify','tx_c3baxi_domain_model_linie')],
			'zone' => [
				'read'=>$GLOBALS['BE_USER']->check('tables_select','tx_c3baxi_domain_model_zone'),
				'modify'=>$GLOBALS['BE_USER']->check('tables_modify','tx_c3baxi_domain_model_zone')],
			'company' => [
				'read'=>$GLOBALS['BE_USER']->check('tables_select','tx_c3baxi_domain_model_company'),
				'modify'=>$GLOBALS['BE_USER']->check('tables_modify','tx_c3baxi_domain_model_company')],
			'holiday' => [
				'read'=>$GLOBALS['BE_USER']->check('tables_select','tx_c3baxi_domain_model_holiday'),
				'modify'=>$GLOBALS['BE_USER']->check('tables_modify','tx_c3baxi_domain_model_holiday')],
			'rating' => [
				'read'=>$GLOBALS['BE_USER']->check('tables_select','tx_c3baxi_domain_model_rating'),
				'modify'=>$GLOBALS['BE_USER']->check('tables_modify','tx_c3baxi_domain_model_rating')],
			'users' => [
				'read'=>$GLOBALS['BE_USER']->check('tables_select','fe_users'),
				'modify'=>$GLOBALS['BE_USER']->check('tables_modify','fe_users')
			],
		];

		$this->view->assignMultiple( $assigns );

	}

	protected function getBookings($routes = false) {
		$dateFrom = new \DateTime();
		$dateFrom->modify('-3 days');

		$bookings = $this->bookingRepository->findByDemand([
			'datefrom' => $dateFrom,
			'showHidden' => true
		]);

		if ( $routes ) {
			$tmp = [];
			foreach ( $bookings as $booking ) {

				if( $booking->getFahrt() === null ) continue;

				if ( $routes->contains($booking->getFahrt()->getLinie()) ) {
					$tmp[] = $booking;
				}
			}

			$bookings = $tmp;
		} else {
			$bookings = $bookings->toArray();
		}

		$now = new \DateTime();
		$comming = $past = $canceled = [];

		foreach ( $bookings as $item ) {

			if( $item->getFahrt() === null ) continue;

			/**
			 * @var Booking  $item
			 * @var DateTime $ts
			 * @var String   $target
			 */
			$target = 'comming';
			$ts = $item->getDate();
			$diff = $now->modify(date('Y-m-d'))->diff($ts);

			if ( $diff->format('%R') == '-' ) {
				$target = 'past';
				$cancelAllowed = false;
			}

			if ( $item->isDeleted() ) {
				$target = 'canceled';
			}

			if ( $target == 'comming' ) {
				$cancelAllowed = true;
				$bb = $now->diff( new \DateTimeImmutable($item->getDate()->format('Y-m-d') . ' ' .$item->getFahrt()->getBuchbarBis()->format('H:i')) );
				list($d, $h, $m) = explode('|', $bb->format('%R|%h|%i'));

				if ( $d == '-' && $h <= 1 ) {
					$cancelAllowed = false;
				}
			}

			$tstamp = new \DateTime();
			$tstamp->setTimestamp($ts->getTimestamp());
			$tstamp->setTime(0, 0);
			$tstamp = $tstamp->getTimestamp();

			if ( !isset($$target[ $tstamp ]) ) {
				$$target[ $tstamp ] = [];
			}

			$fahrt = $item->getFahrt()->getUid();

			if ( !isset($$target[ $tstamp ][ $fahrt ]) ) {
				$$target[ $tstamp ][ $fahrt ] = [
					'data' => $item->getFahrt(),
					'zonen' => [],
					'confirmed' => false,
					'bookings' => [],
					'cancelAllowed' => $cancelAllowed
				];
			}

			$$target[ $tstamp ][ $fahrt ][ 'confirmed' ] = $item->isConfirmed();
			$$target[ $tstamp ][ $fahrt ][ 'approved' ] = $item->isApproved();
			$$target[ $tstamp ][ $fahrt ][ 'reminderSend' ] = $item->isReminderSend();
			array_push($$target[ $tstamp ][ $fahrt ][ 'bookings' ], $item);

			foreach ( $item->getFahrt()->getZeiten() as $zeit ) {
				if ( !isset($$target[ $tstamp ][ $fahrt ][ 'zonen' ][ $zeit->getZone()->getUid() ]) ) {
					$$target[ $tstamp ][ $fahrt ][ 'zonen' ][ $zeit->getZone()->getUid() ] = [
						'data' => $zeit->getZone(),
						'plus' => 0,
						'minus' => 0,
					];
				}
				if( $item->getStartStation())
				if ( $zeit->getZone()->getUid() == $item->getStartStation()->getZone()->getUid() ) {
					$$target[ $tstamp ][ $fahrt ][ 'zonen' ][ $zeit->getZone()->getUid() ][ 'plus' ] += $item->getAdults();
					$$target[ $tstamp ][ $fahrt ][ 'zonen' ][ $zeit->getZone()->getUid() ][ 'plus' ] += $item->getChildren();
				}
				if( $item->getEndStation())
				if ( $zeit->getZone()->getUid() == $item->getEndStation()->getZone()->getUid() ) {
					$$target[ $tstamp ][ $fahrt ][ 'zonen' ][ $zeit->getZone()->getUid() ][ 'minus' ] += $item->getAdults();
					$$target[ $tstamp ][ $fahrt ][ 'zonen' ][ $zeit->getZone()->getUid() ][ 'minus' ] += $item->getChildren();
				}
			}
		}

		$past = $this->_sortBookingList($past);
		$canceled = $this->_sortBookingList($canceled);


		$this->view->assignMultiple(
			[
				'bookings' => $comming,
				'booking_past' => $past,
				'booking_canceled' => $canceled
			]
		);
	}

	protected function _sortBookingList($list) {
		$list = array_reverse($list, true);
		array_walk($list, function (&$l) {
			uasort($l, function ($b, $a) {
				$a[ 'data' ]->getBuchbarBis()->setDate(2000, 1, 1);
				$b[ 'data' ]->getBuchbarBis()->setDate(2000, 1, 1);
				if( $a[ 'data' ]->getBuchbarBis() == $b[ 'data' ]->getBuchbarBis() ) return 0;
				if ( $a[ 'data' ]->getBuchbarBis() < $b[ 'data' ]->getBuchbarBis() ) return -1;
				return 1;
			});
		});
		return $list;
	}

	function detailBookingAction() {
		$date = $this->request->getArgument('date');
		$ride = $this->request->getArgument('ride');
		$found = $this->bookingRepository->findRideOfDay($date, $ride);

		$dateObject = new \DateTime();
		$dateObject->setTimezone(new \DateTimeZone('EUROPE/BERLIN'));
		$dateObject->setTimestamp($date);
		$this->view->assign('date', $dateObject);
		$this->view->assign('ride', $this->rideRepository->findByUid($ride));
		$this->view->assign('bookings', $found);
	}

	function showMapBookingAction() {
		$date = $this->request->getArgument('date');
		$rideUid = $this->request->getArgument('ride');
		$found = $this->bookingRepository->findRideOfDay($date, $rideUid);

		$ride = $this->rideRepository->findByUid($rideUid);

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
		array_walk($waypoints, 'json_encode');
		$waypoints = array_unique($waypoints);
		array_walk($waypoints, 'json_decode');
		$waypoints = array_values($waypoints);

		$this->view->assign('waypoints', $waypoints);

		$this->view->assign('ride', $ride);
	}

	function confirmRideAction() {

		if ( $GLOBALS[ 'BE_USER' ]->isMemberOfGroup(1) ) {
			$date = $this->request->getArgument('date');
			$ride = $this->request->getArgument('ride');
			$found = $this->bookingRepository->findRideOfDay($date, $ride);

			foreach ( $found as $booking ) {
				$booking->setConfirmed(true);
				$this->bookingRepository->update($booking);
			}
		}

		$this->redirect('index');
	}

	function approveRideAction() {
		if ( $GLOBALS[ 'BE_USER' ]->isMemberOfGroup(2) ) {

			$date = $this->request->getArgument('date');
			$ride = $this->request->getArgument('ride');
			$found = $this->bookingRepository->findRideOfDay($date, $ride);

			foreach ( $found as $booking ) {
				$booking->setApproved(true);
				$this->bookingRepository->update($booking);
			}
		}

		$this->redirect('index');
	}

	protected function _sortBookings(&$list) {

	}
}
