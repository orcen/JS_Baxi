<?php

	namespace C3\C3baxi\Controller;


	use C3\C3baxi\Domain\Model\Haltestelle;
	use C3\C3baxi\Domain\Model\Zone;
	use C3\C3baxi\Domain\Repository\ZoneRepository;
	use C3\C3baxi\Domain\Repository\HaltestelleRepository;

	/***
	 *
	 * This file is part of the "Baxi Fahrten" Extension for TYPO3 CMS.
	 *
	 * For the full copyright and license information, please read the
	 * LICENSE.txt file that was distributed with this source code.
	 *
	 *  (c) 2019
	 *
	 ***/

	/**
	 * ZoneController
	 */
	class ZoneController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
	{
		protected $repository = NULL;
		protected $haltestelleRepository = NULL;
		/**
		 * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
		 * @inject
		 */
		protected $persistenceManager;

		/**
		 * action list
		 *
		 * @return void
		 */
		public function listAction()
		{
			$zones = $this->repository->findAll();
			$this->view->assign( 'zones', $zones );
		}

		/**
		 * action show
		 *
		 * @param Zone $zone
		 *
		 * @return void
		 */
		public function showAction( Zone $zone )
		{
			$this->view->assign( 'zone', $zone );
		}

		/**
		 * action new
		 *
		 * @return void
		 */
		public function newAction()
		{

			$unAssignedStations = $this->repository->findStations( 0 );
			foreach ( $unAssignedStations as $row ) {
				$stationList[$row->getUid()] = $row->getName();
			}

			$this->view->assign( 'free_stations', $stationList );
			$this->view->assign( 'assigned_stations', [] );

			$this->view->assign( 'stations', FALSE );
		}

		/**
		 * action create
		 *
		 * @return void
		 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
		 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
		 */
		public function createAction()
		{
			$request = $_REQUEST['tx_c3baxi_web_c3baxibaxi'];
			// instantiate Zone
			$zone = new Zone();
			$zone->setName( $request['name'] );
			// bind zone to haltestellen
			$stationen = $this->request->getArgument( 'liste');
			$stationen = explode( ',', $stationen );

			$stationen = array_filter( $stationen );
			if( count( $stationen ) ) {
				foreach( $stationen as $id ) {
					$haltestelle = $this->haltestelleRepository->findByUid( $id );
					$haltestelle->setZone( $zone );
					$this->haltestelleRepository->update( $haltestelle );

					$zone->addHaltestelle( $haltestelle );
				}
			}

			// save data
			$this->repository->add( $zone );
			$this->persistenceManager->persistAll();

			if( $this->request->getArgument('action_after' ) === 'edit' )
				$this->redirect( 'edit',NULL, NULL, ['zone' => $zone ] );
			elseif( $this->request->getArgument('action_after' ) === 'new' )
				$this->redirect( 'new', null, null,null );
			elseif( $this->request->getArgument('action_after' ) === 'close' )
				$this->redirect( 'index', 'Baxi', null,null );
		}

		/**
		 * action update
		 *
		 * @param \C3\C3baxi\Domain\Model\Zone $zone
		 *
		 * @return void
		 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException
		 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
		 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
		 */
		public function updateAction( \C3\C3baxi\Domain\Model\Zone $zone )
		{
			$request = $_REQUEST['tx_c3baxi_web_c3baxibaxi'];
			$zone->setName( $request['name'] );

			if( $this->request->hasArgument( 'removeStation')) {
				$stationen = $this->request->getArgument( 'removeStation' );
				if ( is_array( $stationen ) ) {
					foreach ( $stationen as $id ) {
						$haltestelle = $this->haltestelleRepository->findByUid( $id );
						$haltestelle->unsetZone();
						$this->haltestelleRepository->update( $haltestelle );

						$zone->removeHaltestelle(  $haltestelle );
					}
				}
			}

			if( $this->request->hasArgument( 'stationen')) {
				$stationen = $this->request->getArgument( 'stationen' );
				if ( is_array( $stationen ) ) {
					foreach ( $stationen as $id ) {
						$haltestelle = $this->haltestelleRepository->findByUid( $id );
						$haltestelle->setZone( $zone );
						$this->haltestelleRepository->update( $haltestelle );

						$zone->addHaltestelle( $haltestelle );
					}
				}
			}

			$this->repository->update( $zone );
			$this->persistenceManager->persistAll();

			if( $this->request->getArgument('action_after' ) === 'edit' )
				$this->redirect( 'edit',NULL, NULL, ['zone' => $zone ] );
			elseif( $this->request->getArgument('action_after' ) === 'new' )
				$this->redirect( 'new', null, null,null );
			elseif( $this->request->getArgument('action_after' ) === 'close' )
				$this->redirect( 'index', 'Baxi', null,null );
		}

		/**
		 * @param Zone $zone
		 *
		 * @return void
		 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
		 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
		 */
		public function deleteAction( \C3\C3baxi\Domain\Model\Zone $zone ) {

			foreach( $zone->getHaltestellen() as $haltestelle ) {
				$haltestelle->unsetZone( );

				$this->haltestelleRepository->update( $haltestelle );
			}
			$this->repository->remove( $zone );
			$this->persistenceManager->persistAll();

			$this->redirect( 'list' );

		}

		/**
		 * action edit
		 *
		 * @param Zone $zone
		 *
		 * @return void
		 */
		public function editAction( Zone $zone )
		{
			$this->view->assign( 'zone', $zone );
			$unAssignedStations = $this->repository->findStations( 0 );
			foreach ( $unAssignedStations as $row ) {
				$stationList[$row->getUid()] = $row->getName();
			}
			$this->view->assign( 'free_stations', $stationList );

			$assignedStations = $zone->getHaltestellen();
			$this->view->assign( 'assigned_stations', $assignedStations );
		}

		/**
		 * Inject the product repository
		 *
		 * @param \C3\C3baxi\Domain\Repository\ZoneRepository $repository
		 */
		public function injectRepository( ZoneRepository $repository )
		{
			$this->repository = $repository;
		}

		/**
		 * Inject the product repository
		 *
		 * @param \C3\C3baxi\Domain\Repository\HaltestelleRepository $repository
		 */
		public function injectHaltestelleRepository( HaltestelleRepository $repository )
		{
			$this->haltestelleRepository = $repository;
		}
	}
