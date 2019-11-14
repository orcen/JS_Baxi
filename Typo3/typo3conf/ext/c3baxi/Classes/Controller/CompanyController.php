<?php


	namespace C3\C3baxi\Controller;

	use C3\C3baxi\Domain\Model\Company;
	use TYPO3\CMS\Core\Error\DebugExceptionHandler;
	use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
	use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

	class CompanyController extends ActionController
	{
		/**
		 * @var \C3\C3baxi\Domain\Repository\CompanyRepository
		 * @inject
		 */
		protected $repository = NULL;

		/**
		 * @var \C3\C3baxi\Domain\Repository\LinieRepository
		 * @inject
		 */
		protected $routes = NULL;

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
		function listAction(){}

		/**
		 * @return void
		 */
		function newAction(){

			$this->view->assign( 'company', new Company() );
			$this->view->assign( 'assigned_routes', [] );
			$this->view->assign( 'free_routes',  $this->routes->findByCompany( 0 ) );
		}

		/**
		 * @return void
		 */
		function createAction(){

			$company = new Company();

			$args = $this->request->getArguments();


			$company->setName( $args['name'] );
			$company->setInfo( $args['info'] );
			$company->setTelefon( $args['telefon'] );
			$company->setEmail( $args['email'] );
			$company->setStreet( $args['street'] );
			$company->setCity( $args['city'] );
			$company->setZip( $args['zip'] );
			$company->setCarCount( 0 );
			$company->setContactPerson( $args['contact_person'] );

			$this->repository->add( $company );
			$this->persistenceManager->persistAll();

			if( $this->request->getArgument('action_after' ) === 'edit' )
				$this->redirect( 'edit',NULL, NULL, ['company' => $company ] );
			elseif( $this->request->getArgument('action_after' ) === 'new' )
				$this->redirect( 'new', null, null,null );
			elseif( $this->request->getArgument('action_after' ) === 'close' )
				$this->redirect( 'index', 'Baxi', null,null );
		}

		/**
		 * @param \C3\C3baxi\Domain\Model\Company $company
		 */
		function editAction(\C3\C3baxi\Domain\Model\Company $company){

			$this->view->assign( 'company', $company );
			$this->view->assign( 'assigned_routes', $company->getRoutes() );

			$this->view->assign( 'free_routes', $this->routes->findByCompany( 0 ) );
		}

		/**
		 * @param \C3\C3baxi\Domain\Model\Company $company
		 */
		function updateAction(\C3\C3baxi\Domain\Model\Company $company){

			$args = $this->request->getArguments();


			$company->setName( $args['name'] );
			$company->setInfo( $args['info'] );
			$company->setTelefon( $args['telefon'] );
			$company->setEmail( $args['email'] );
			$company->setStreet( $args['street'] );
			$company->setCity( $args['city'] );
			$company->setZip( $args['zip'] );
			$company->setCarCount( 0 );
			$company->setContactPerson( $args['contact_person'] );

			$this->repository->update( $company );
			$this->persistenceManager->persistAll();

			if( $this->request->getArgument('action_after' ) === 'edit' )
				$this->redirect( 'edit',NULL, NULL, ['company' => $company ] );
			elseif( $this->request->getArgument('action_after' ) === 'new' )
				$this->redirect( 'new', null, null,null );
			elseif( $this->request->getArgument('action_after' ) === 'close' )
				$this->redirect( 'index', 'Baxi', null,null );


		}


		function importAction() {

		}

	}