<?php

	namespace C3\C3baxi\Controller;


	use \C3\C3baxi\Domain\Model\Haltestelle;
	use \C3\C3baxi\Domain\Repository\HaltestelleRepository;
	use \C3\C3baxi\Domain\Repository\ZoneRepository;

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
	 * HaltestelleController
	 */
	class HaltestelleController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
	{
		protected $haltestelleRepository;
		protected $zoneRepository;

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

			$haltestelles = $this->haltestelleRepository->findAll();
			$this->view->assign( 'haltestelles', $haltestelles );
		}

		/**
		 * action show
		 *
		 * @param \C3\C3baxi\Domain\Model\Haltestelle $haltestelle
		 *
		 * @return void
		 */
		public function showAction( \C3\C3baxi\Domain\Model\Haltestelle $haltestelle )
		{
			$this->view->assign( 'haltestelle', $haltestelle );
		}

		/**
		 * action edit
		 *
		 * @param \C3\C3baxi\Domain\Model\Haltestelle $haltestelle
		 *
		 * @return void
		 */
		public function editAction( \C3\C3baxi\Domain\Model\Haltestelle $haltestelle )
		{
			$this->view->assign( 'haltestelle', $haltestelle );
			/** @var $zones */
			$zones = $this->zoneRepository->findAll();
			/** @var $zoneList */
			$zoneList = [0 => 'keine'];

			foreach ( $zones as $zone ) {
				$zoneList[$zone->getUid()] = $zone->getName();
			}

			$this->view->assign( 'zoneList', $zoneList );
		}

		/**
		 * action update
		 *
		 * @param \C3\C3baxi\Domain\Model\Haltestelle $haltestelle
		 *
		 * @return void
		 */
		public function updateAction( \C3\C3baxi\Domain\Model\Haltestelle $haltestelle )
		{
			$request = $_REQUEST['tx_c3baxi_web_c3baxibaxi'];
			$update = FALSE;
			if ( $haltestelle->getName() != $request['name'] ) {
				$haltestelle->setName( $request['name'] );
				$update = TRUE;
			}

			if ( $haltestelle->getNumber() != $request['number'] ) {
				$haltestelle->setNumber( $request['number'] );
				$update = TRUE;
			}

			if ( $haltestelle->getLatitude() != $request['latitude'] ) {
				$haltestelle->setLatitude( $request['latitude'] );
				$update = TRUE;
			}

			if ( $haltestelle->getLongitude() != $request['longitude'] ) {
				$haltestelle->setLongitude( $request['longitude'] );
				$update = TRUE;
			}


			$zone = $this->zoneRepository->findByUid( is_array( $request['zone'] ) ? $request['zone']['__identity'] :$request['zone'] );

			if ( $haltestelle->getZone() != $zone ) {
				$haltestelle->setZone( $zone );
				$update = TRUE;
			}
			if ( $update ) {
				$this->persistenceManager->update( $haltestelle );
				$this->persistenceManager->persistAll();
			}

			$this->redirect( 'list' );
		}

		/**
		 * action delete
		 *
		 * @param \C3\C3baxi\Domain\Model\Haltestelle $haltestelle
		 *
		 * @return void
		 */
		public function deleteAction( \C3\C3baxi\Domain\Model\Haltestelle $haltestelle )
		{
			$this->haltestelleRepository->remove( $haltestelle );
			$this->redirect( 'list' );
		}

		/**
		 * action create
		 *
		 * @param \C3\C3baxi\Domain\Model\Zone $zone
		 *
		 * @return void
		 */
		public function createAction( \C3\C3baxi\Domain\Model\Zone $zone )
		{
			$request = $_REQUEST['tx_c3baxi_web_c3baxibaxi'];

			if ( filter_var( $request['name'], FILTER_SANITIZE_STRING ) ) {

				$haltestelle = new Haltestelle();
				$haltestelle->setName( $request['name'] );
				$haltestelle->setNumber( $request['number'] );
				$haltestelle->setLatitude( $request['latitude'] );
				$haltestelle->setLongitude( $request['longitude'] );

				$haltestelle->setZone( $zone );

				$this->haltestelleRepository->add( $haltestelle );
				$this->persistenceManager->persistAll();

				$this->redirect( 'list' );
			}
		}

		/**
		 * action new
		 *
		 * @param \C3\C3baxi\Domain\Model\Haltestelle $haltestelle
		 *
		 * @return void
		 */
		public function newAction()
		{
			/** @var $zones */
			$zones = $this->zoneRepository->findAll();
			/** @var $zoneList */
			$zoneList = [0 => 'keine'];

			foreach ( $zones as $zone ) {
				$zoneList[$zone->getUid()] = $zone->getName();
			}
			$this->view->assign( 'zoneList', $zoneList );
		}

		/**
		 * @param
		 *
		 * @return void
		 */
		public function importAction()
		{
			echo '<pre>';
			$fileName = $_FILES['tx_c3baxi_web_c3baxibaxi']['name']['importFile'];
			$tmpFile = $_FILES['tx_c3baxi_web_c3baxibaxi']['tmp_name']['importFile'];

			/** ToDo: check File params and so on */

			$f = fopen( $tmpFile, 'r' );
			while ( $row = fgetcsv( $f, 1024, ';', '"', "\\" ) ) {
				$haltestelle = new Haltestelle();
				$haltestelle->setName( $row[1] );
				$haltestelle->setLongitude( $row[2] );
				$haltestelle->setLatitude( $row[3] );
				$this->haltestelleRepository->add( $haltestelle );
			}
			$this->persistenceManager->persistAll();

			$this->redirect( 'list' );
		}

		/**
		 * Inject the product repository
		 *
		 * @param \C3\C3baxi\Domain\Repository\HaltestelleRepository $haltestelleRepository
		 */
		public function injectHaltestelleRepository( HaltestelleRepository $haltestelleRepository )
		{
			$this->haltestelleRepository = $haltestelleRepository;
		}

		/**
		 * Inject the product repository
		 *
		 * @param \C3\C3baxi\Domain\Repository\ZoneRepository $zoneRepository
		 */
		public function injectZoneRepository( ZoneRepository $zoneRepository )
		{
			$this->zoneRepository = $zoneRepository;
		}
	}
