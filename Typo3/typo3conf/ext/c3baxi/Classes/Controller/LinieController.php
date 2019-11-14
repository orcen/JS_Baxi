<?php

	namespace C3\C3baxi\Controller;


	use C3\C3baxi\Domain\Model\Linie;
	use \C3\C3baxi\Domain\Repository\LinieRepository;
	use \C3\C3baxi\Domain\Repository\ZoneRepository;
	use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

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
	 * LinieController
	 */
	class LinieController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
	{
		/**
		 * @var \C3\C3baxi\Domain\Repository\LinieRepository
		 * @inject
		 */
		protected $repository = NULL;
		/**
		 * @var \C3\C3baxi\Domain\Repository\ZoneRepository
		 * @inject
		 */
		protected $zonenRepository = NULL;
		/**
		 * @var \C3\C3baxi\Domain\Repository\CompanyRepository
		 * @inject
		 */
		protected $companies = NULL;
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
			$linies = $this->repository->findAll();
			$this->view->assign( 'linies', $linies );
		}

		/**
		 * action new
		 *
		 * @return void
		 */
		function newAction()
		{
			$this->view->assign( 'companies', $this->companies->findAll() );
			$this->view->assign( 'zonen', $this->zonenRepository->findAll() );
		}

		/**
		 * action edit
		 *
		 * @param Linie $linie
		 *
		 * @return void
		 */
		function editAction( Linie $linie )
		{
			$this->view->assign( 'linie', $linie );

			$this->view->assign( 'companies', $this->companies->findAll() );
			$this->view->assign( 'zonen', $this->zonenRepository->findAll() );

//			$this->view->assign( 'zugewiesenen_zonen', $linie->getZonen() );
		}

		/**
		 * action create
		 * @return void
		 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException
		 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
		 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
		 * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
		 */
		function createAction()
		{
			$name = $this->request->getArgument( 'name' );
			$name = filter_var( $name, FILTER_SANITIZE_STRING );

			$nummer = $this->request->getArgument( 'nummer' );
			$nummer = filter_var( $nummer, FILTER_VALIDATE_INT );

			$linie = new Linie();
			$linie->setName( $name );
			$linie->setNr( $nummer );

			if( $this->request->hasArgument( 'liste' ) ) {
				$zonen = $this->request->getArgument( 'liste' );
				$zonen = explode( ',', $zonen );
				$zonen = array_filter( $zonen );

				if( count( $zonen ) ) {
					foreach( $zonen as $id ) {
						$zone = $this->zonenRepository->findByUid( $id );
//						$zone->setLinie( $linie );
//						$this->zonenRepository->update( $zone );

						$linie->addZone( $zone );
					}
				}
			}

			$this->repository->add( $linie );
			$this->persistenceManager->persistAll();

			if( $this->request->getArgument('action_after' ) === 'edit' )
				$this->redirect( 'edit',NULL, NULL, ['linie' => $linie ] );
			elseif( $this->request->getArgument('action_after' ) === 'new' )
				$this->redirect( 'new', null, null,null );
			elseif( $this->request->getArgument('action_after' ) === 'close' )
				$this->redirect( 'index', 'Baxi', null,null );

		}

		/**
		 * action update
		 *
		 * @param \C3\C3baxi\Domain\Model\Linie $linie
		 *
		 * @return void
		 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException
		 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
		 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
		 * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
		 * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
		 */
		public function updateAction( $linie )
		{

			if ( $this->request->hasArgument( 'zonen' ) ) {
				$zonen = $this->request->getArgument( 'zonen' );
				if ( is_array( $zonen ) ) {
					foreach ( $zonen as $zoneID ) {
						$zone = $this->zonenRepository->findByUid( $zoneID );
						$linie->addZone( $zone );
					}
				}
			}

			if ( $this->request->hasArgument( 'removeZone' ) ) {
				$zonen = $this->request->getArgument( 'removeZone' );
				if ( is_array( $zonen ) ) {
					foreach ( $zonen as $zoneID ) {
						$zone = $this->zonenRepository->findByUid( $zoneID );
						$linie->removeZone( $zone );
					}
				}
			}

			$linie->setName( $this->request->getArgument( 'name' ) );

			$this->repository->update( $linie );
			$this->persistenceManager->persistAll();

			$this->redirect( 'edit', NULL, NULL, ['linie' => $linie] );
		}

		/**
		 * action delete
		 *
		 * @param \C3\C3baxi\Domain\Model\Linie $linie
		 *
		 * @return void
		 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
		 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
		 * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
		 */
		public function deleteAction( Linie $linie )
		{
			$this->repository->remove( $linie );
			$this->persistenceManager->persistAll();
			$this->redirect( 'list' );
		}

	}
