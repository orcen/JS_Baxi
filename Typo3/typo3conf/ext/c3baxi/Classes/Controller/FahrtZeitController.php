<?php

	namespace C3\C3baxi\Controller;

	use \C3\C3baxi\Domain\Repository\FahrtZeitRepository;

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
	 * FahrtZeitController
	 */
	class FahrtZeitController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
	{
		/**
		 * @var \C3\C3baxi\Domain\Repository\FahrtZeitRepository
		 * @inject
		 */
		protected $repository = NULL;

		/**
		 * @var \C3\C3baxi\Domain\Repository\ZoneRepository
		 * @inject
		 */
		protected $zoneRepository = NULL;

		/**
		 * @var \C3\C3baxi\Domain\Repository\FahrtRepository
		 * @inject
		 */
		protected $fahrtRepository = NULL;

		/**
		 * action list
		 *
		 * @return void
		 */
		public function listAction()
		{
			$fahrtZeits = $this->repository->findAll();
			$this->view->assign( 'fahrtZeits', $fahrtZeits );
		}

		/**
		 * action show
		 *
		 * @param \C3\C3baxi\Domain\Model\FahrtZeit $fahrtZeit
		 *
		 * @return void
		 */
		public function showAction( \C3\C3baxi\Domain\Model\FahrtZeit $fahrtZeit )
		{
			$this->view->assign( 'fahrtZeit', $fahrtZeit );
		}

		/**
		 *
		 */
		public function newAction( ) {

			$zonenListe = [];
			foreach( $this->zoneRepository->findAll() as $zone ) {
				$zonenListe[ $zone->getUid() ] = $zone->getName();
			}
			$this->view->assign( 'zonen',  $zonenListe);

			$fahrtenListe = [];
			foreach( $this->fahrtRepository->findAll() as $fahrt ) {
				$fahrtenListe[ $fahrt->getUid() ] = $fahrt->getLinie( )->getNr() . ' ' . $fahrt->getName( );
			}
			$fahrtenListe = sort( $fahrtenListe );
			$this->view->assign( 'fahrten', $fahrtenListe );


		}

		public function createAction()
		{
		}


	}
