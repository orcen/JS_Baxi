<?php

namespace C3\C3baxi\Controller;


use C3\C3baxi\Domain\Model\Fahrt;
use C3\C3baxi\Domain\Model\FahrtZeit;
use C3\C3baxi\Domain\Model\Linie;
use \C3\C3baxi\Domain\Repository\LinieRepository;
use \C3\C3baxi\Domain\Repository\ZoneRepository;
use Doctrine\Common\Util\Debug;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
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
	protected $repository = null;
	/**
	 * @var \C3\C3baxi\Domain\Repository\ZoneRepository
	 * @inject
	 */
	protected $zonenRepository = null;
	/**
	 * @var \C3\C3baxi\Domain\Repository\CompanyRepository
	 * @inject
	 */
	protected $companies = null;

	/**
	 * @var \C3\C3baxi\Domain\Repository\FahrtRepository
	 * @inject
	 */
	protected $fahrten = null;

	/**
	 * @var \C3\C3baxi\Domain\Repository\FahrtZeitRepository
	 * @inject
	 */
	protected $fahrtzeiten = null;

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
	public function listAction() {
		$linies = $this->repository->findAll();
		$this->view->assign('linies', $linies);
	}

	/**
	 * @param Linie|null $linie
	 */
	function newAction(Linie $linie = null) {
		$this->view->assignMultiple([
			'action' => 'create',
			'linie' => $linie,
			'companies' => $this->companies->findAll(),
			'zonen' => $this->zonenRepository->findAll()
		]);
	}

	/**
	 * @param Linie|null $linie
	 */
	function editAction(Linie $linie = null) {
		if ( $linie ) {

			$this->view->assignMultiple([
				'action' => 'update',
				'linie' => $linie,
				'companies' => $this->companies->findAll(),
				'zonen' => $this->zonenRepository->findAll()
			]);
		}
	}

	/**
	 * action create
	 * @return void
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
	 * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
	 */
	function createAction(Linie $linie = null) {


		if ( $this->request->hasArgument('liste') ) {
			$zonen = $this->request->getArgument('liste');
			$zonen = explode(',', $zonen);
			$zonen = array_filter($zonen);

			if ( count($zonen) ) {
				foreach ( $zonen as $id ) {
					$zone = $this->zonenRepository->findByUid($id);
					$linie->addZone($zone);
				}
			}
		}

		$this->repository->add($linie);
		$this->persistenceManager->persistAll();

		if ( $this->request->getArgument('action_after') === 'edit' )
			$this->redirect('edit', null, null, ['linie' => $linie]);
		elseif ( $this->request->getArgument('action_after') === 'new' )
			$this->redirect('new', null, null, null);
		elseif ( $this->request->getArgument('action_after') === 'close' )
			$this->redirect('index', 'Baxi', null, null);

	}

	/**
	 * action update
	 *
	 * @return void
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
	 * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
	 * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
	 * @var Linie $linie
	 *
	 */
	public function updateAction(Linie $linie = null) {
		if ( $linie ) {

			if ( $this->request->hasArgument('zonen') ) {
				$zonen = $this->request->getArgument('zonen');
				if ( !is_array($zonen) ) {
					$zonen = explode(',', $zonen);
				}

				$linie->setZonen(new ObjectStorage());

				$this->repository->update($linie);
				$this->persistenceManager->persistAll();
				foreach ( $zonen as $zoneID ) {
					$zone = $this->zonenRepository->findByUid($zoneID);
					$linie->addZone($zone);
				}
			}

			$this->repository->update($linie);
			$this->persistenceManager->persistAll();
		}

		if ( $this->request->getArgument('action_after') === 'edit' )
			$this->redirect('edit', null, null, ['linie' => $linie]);
		elseif ( $this->request->getArgument('action_after') === 'new' )
			$this->redirect('new', null, null, null);
		elseif ( $this->request->getArgument('action_after') === 'close' )
			$this->redirect('index', 'Baxi', null, null);

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
	public function deleteAction(Linie $linie) {
//			if( $linie === null ) {
//				$uid = intval( $this->request->getArgument( 'linie' ) );
//				$linie = $this->repository->findHiddenByUid( $uid );
//			}
		if ( $linie ) {
			foreach ( $linie->getFahrten() as $fahrt ) {
				foreach ( $fahrt->getZeiten() as $fahrtzeit ) {
					$this->fahrtzeiten->remove($fahrtzeit);
				}
				$this->fahrten->remove($fahrt);
			}

			$this->repository->remove($linie);
			$this->persistenceManager->persistAll();
		}
		$this->redirect('index', 'Baxi');
	}

	public function exportAction(Linie $linie) {

	}


	public function toggleAction(Linie $linie) {
		if ( $linie === null ) {
			$uid = intval($this->request->getArgument('linie'));
			$linie = $this->repository->findHiddenByUid($uid);
		}
		if ( $linie ) {

			$linie->setHidden(!$linie->isHidden());
			$this->repository->update($linie);
			$this->persistenceManager->persistAll();
		}
		$this->redirect('index', 'Baxi');
	}

	public function showAction(Linie $linie = null ) {

		/**
		 * @var Fahrt $fahrt
		 * @var FahrtZeit $zeit
		 */

		$table1 = $table2 = [
			'head' => [' '],
			'rows' => [
			]
		];

		foreach ( $linie->getZonen() as $zone ) {
			$table1['rows'][$zone->getName()] = [];
		}
		$table2['rows'] = array_reverse( $table1['rows']);

		$first = true;
		foreach( $linie->getFahrten() as $fahrt ) {
			$first = false;

			$target = 'table1';
			if( $fahrt->isRueckfahrt() ) {
				$target = 'table2';
			}
			$$target['head'][] = $fahrt->getName();

			$row = [];

			foreach($fahrt->getZeiten() as $zeit) {
				if( false === isset( $$target['rows'][$zeit->getZone()->getName()])) {
					$zeit->setHidden(1);
					$this->fahrtzeiten->update($zeit);
					continue;
//					$$target['rows'][$zeit->getZone()->getName()] = [];
				}

				$$target['rows'][$zeit->getZone()->getName()][] = $zeit->getZeit();
			}
		}

		$this->persistenceManager->persistAll();

		$this->view->assignMultiple([
			'linie' => $linie,
			'hinfahrten' => $table1,
			'ruckfahrten' => $table2
		]);
	}
}
