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
	protected $repository = null;

	/**
	 * @var \C3\C3baxi\Domain\Repository\LinieRepository
	 * @inject
	 */
	protected $routes = null;

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
	function listAction() {
	}

	/**
	 * @param Company|null $company
	 */
	function newAction(Company $company = null) {

		$this->view->assign('company', $company);
		$this->view->assign('assigned_routes', []);
		$this->view->assign('free_routes', $this->routes->findByCompany(0));
	}

	/**
	 * @param Company|null $company
	 */
	function createAction(Company $company = null) {

		$routes = [];
		if ( $this->request->hasArgument('liste') ) {
			$routes = $this->request->getArgument('liste');
			$routes = explode(',', $routes);
			$routes = array_filter($routes);

			foreach ($routes as $uid) {
				$route = $this->routes->findByUid($uid);
				if($route) {
					$company->addRoute($route);
				}
			}
		}

		$this->repository->add($company);

			$this->persistenceManager->persistAll();
//
			if( $this->request->getArgument('action_after' ) === 'edit' )
				$this->redirect( 'edit',NULL, NULL, ['company' => $company->getUid() ] );
			elseif( $this->request->getArgument('action_after' ) === 'new' )
				$this->redirect( 'new', null, null,null );
			elseif( $this->request->getArgument('action_after' ) === 'close' )
				$this->redirect( 'index', 'Baxi', null,null );
	}

	/**
	 * @param \C3\C3baxi\Domain\Model\Company $company
	 */
	function editAction(\C3\C3baxi\Domain\Model\Company $company) {

		$this->view->assign('company', $company);
		$this->view->assign('assigned_routes', $company->getRoutes());

		$this->view->assign('free_routes', $this->routes->findByCompany(0));
	}

	/**
	 * @param \C3\C3baxi\Domain\Model\Company $company
	 */
	function updateAction(\C3\C3baxi\Domain\Model\Company $company) {

		$this->repository->update($company);
		$this->persistenceManager->persistAll();

		if ( $this->request->getArgument('action_after') === 'edit' )
			$this->redirect('edit', null, null, ['company' => $company]);
		elseif ( $this->request->getArgument('action_after') === 'new' )
			$this->redirect('new', null, null, null);
		elseif ( $this->request->getArgument('action_after') === 'close' )
			$this->redirect('index', 'Baxi', null, null);


	}


	function importAction() {

	}

}