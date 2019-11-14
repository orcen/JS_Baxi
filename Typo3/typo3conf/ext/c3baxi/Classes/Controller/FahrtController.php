<?php

	namespace C3\C3baxi\Controller;

	use C3\C3baxi\Domain\Model\Fahrt;
	use C3\C3baxi\Domain\Model\FahrtZeit;
	use C3\C3baxi\Domain\Model\Linie;
	use C3\C3baxi\Domain\Repository\FahrtRepository;
	use http\Env\Request;
	use TYPO3\CMS\Extbase\Object\ObjectManager;

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
	 * FahrtController
	 */
	class FahrtController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
	{
		/**
		 * @var \C3\C3baxi\Domain\Repository\FahrtRepository
		 * @inject
		 */
		protected $repository = NULL;
		/**
		 * @var \C3\C3baxi\Domain\Repository\LinieRepository
		 * @inject
		 */
		protected $linieRepository = NULL;
		/**
		 * @var \C3\C3baxi\Domain\Repository\ZoneRepository
		 * @inject
		 */
		protected $zoneRepository = NULL;

		/**
		 * @var \C3\C3baxi\Domain\Repository\FahrtZeitRepository
		 * @inject
		 */
		protected $fahrtZeitRepository = NULL;

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
			$fahrts = $this->repository->findAll();
//        $tmp = [];
//        foreach( $fahrts as $fahrt ) {
//        	$linie = $fahrt->getLinie()->getNr();
//        	if( ! isset( $tmp[ $linie ] ) ) $tmp[$linie] = [];
//        	$tmp[ $linie ] [] = $fahrt;
//
//        }

			$this->view->assign( 'fahrts', $fahrts );
		}

		/**
		 * action show
		 *
		 * @param \C3\C3baxi\Domain\Model\Fahrt $fahrt
		 *
		 * @return void
		 */
		public function showAction( \C3\C3baxi\Domain\Model\Fahrt $fahrt )
		{
			$this->view->assign( 'fahrt', $fahrt );
		}

		/**
		 * @return void
		 */
		public function newAction()
		{
			$linien = $this->linieRepository->findAll();
			$linienListe = [];
			foreach ( $linien as $linie ) {
				$linienListe[$linie->getUid()] = $linie->getNr() . ' ' . $linie->getName();
			}
			$this->view->assign( 'linienListe', $linienListe );

			if ( $this->request->hasArgument( 'linie' ) )
				$this->view->assign( 'linie', $this->request->getArgument( 'linie' ) );
		}

		/**
		 * @param Fahrt $fahrt
		 *
		 * @return void
		 */
		public function editAction( Fahrt $fahrt )
		{
			$this->view->assign( 'fahrt', $fahrt );

			$linien = $this->linieRepository->findAll();
			$linienListe = [];
			foreach ( $linien as $linie ) {
				$linienListe[$linie->getUid()] = $linie->getNr() . ' ' . $linie->getName();
			}
			$this->view->assign( 'linienListe', $linienListe );
		}


		/** Processing actions */

		/**
		 * @return void
		 */
		public function createAction()
		{
			$fahrt = new Fahrt();
			$fahrt->setName( $this->request->getArgument( 'name' ) );

			$linieID = $this->request->getArgument( 'linie' );
			$linie = $this->linieRepository->findByUid( $linieID );
			$fahrt->setTage( $this->request->getArgument( 'tage' ) );
			if ( $linie ) {
				$fahrt->setLinie( $linie );
				$linie->addFahrt( $fahrt );
				$this->linieRepository->update( $linie );

				$zonen = $linie->getZonen()->toArray();
				if ( $this->request->getArgument( 'reverse' ) == 1 )
					$zonen = array_reverse( $zonen );

				foreach ( $zonen as $zone ) {
					$fahrtZeit = new FahrtZeit();
					$fahrtZeit->setFahrt( $fahrt );
					$fahrtZeit->setZone( $zone );
					$fahrt->addZeit( $fahrtZeit );
				}
			}

			$buchZeit = $this->request->getArgument( 'buchbar_bis' );
			$fahrt->setBuchbarBis( strtotime( $buchZeit ) );

			$this->repository->add( $fahrt );
			$this->persistenceManager->persistAll();


			if ( $this->request->getArgument( 'action_after' ) === 'edit' )
				$this->redirect( 'edit', NULL, NULL, ['fahrt' => $fahrt] );
			elseif ( $this->request->getArgument( 'action_after' ) === 'new' )
				$this->redirect( 'new', NULL, NULL, NULL );
			elseif ( $this->request->getArgument( 'action_after' ) === 'close' )
				$this->redirect( 'index', 'Baxi', NULL, NULL );
		}

		/**
		 * @param Fahrt $fahrt
		 *
		 * @return void
		 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
		 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
		 * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
		 * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
		 */
		public function updateAction( Fahrt $fahrt )
		{
			$args = $this->request->getArguments();

			$fahrt->setName( $args['name'] );
			$fahrt->setTage( $args['tage'] );

//			foreach ( $args['zeit'] as $zid => $zeit ) {
//				if ( $zone = $this->zoneRepository->findByUid( $zid ) ) {
//					$fahrtZeit = new FahrtZeit();
//
//					$fahrtZeit->setZone( $zone );
//					$fahrtZeit->setZeit( strtotime($zeit) );
//					$fahrtZeit->setFahrt( $fahrt );
//
//					$this->fahrtZeitRepository->add( $fahrtZeit );
//					$fahrt->addZeit( $fahrtZeit );
//				}
//			}

			foreach ( $fahrt->getZeiten() as $item ) {

				$fahrtZeit = $this->fahrtZeitRepository->findByUid( $item->getUid() );
				$zeit = $args['zeit'][$fahrtZeit->getZone()->getUid()];
				echo $zeit . PHP_EOL;
				$fahrtZeit->setZeit( strtotime( $zeit ) );

				$this->fahrtZeitRepository->update( $fahrtZeit );
			}

			$buchZeit = $this->request->getArgument( 'buchbar_bis' );
			$fahrt->setBuchbarBis( strtotime( $buchZeit ) );

			$this->repository->update( $fahrt );
			$this->persistenceManager->persistAll();

			if ( $this->request->getArgument( 'action_after' ) === 'edit' )
				$this->redirect( 'edit', NULL, NULL, ['fahrt' => $fahrt] );
			elseif ( $this->request->getArgument( 'action_after' ) === 'new' )
				$this->redirect( 'new', NULL, NULL, NULL );
			elseif ( $this->request->getArgument( 'action_after' ) === 'close' )
				$this->redirect( 'index', 'Baxi', NULL, NULL );
		}

		/**
		 * @param Fahrt $fahrt
		 *
		 * @return void
		 */
		public function deleteAction( Fahrt $fahrt )
		{

			$linie = $fahrt->getLinie();
			$linie->removeFahrt( $fahrt );
			$this->linieRepository->update( $linie );
			$this->repository->remove( $fahrt );
			$this->persistenceManager->persistAll();
			$this->redirect( 'index', 'Baxi', NULL, NULL );
		}

	}
