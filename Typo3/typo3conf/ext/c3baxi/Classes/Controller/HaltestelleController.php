<?php

namespace C3\C3baxi\Controller;


use \C3\C3baxi\Domain\Model\Haltestelle;
use C3\C3baxi\Domain\Model\Zone;
use \C3\C3baxi\Domain\Repository\HaltestelleRepository;
use \C3\C3baxi\Domain\Repository\ZoneRepository;
use Doctrine\Common\Util\Debug;
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
	public function listAction() {

		$haltestelles = $this->haltestelleRepository->findAll();
		$this->view->assign('haltestelles', $haltestelles);
	}

	/**
	 * action show
	 *
	 * @param \C3\C3baxi\Domain\Model\Haltestelle $haltestelle
	 *
	 * @return void
	 */
	public function showAction(\C3\C3baxi\Domain\Model\Haltestelle $haltestelle) {
		$this->view->assign('haltestelle', $haltestelle);
	}

	/**
	 * action edit
	 *
	 * @param \C3\C3baxi\Domain\Model\Haltestelle $haltestelle
	 *
	 * @return void
	 */
	public function editAction(\C3\C3baxi\Domain\Model\Haltestelle $haltestelle) {

		/** @var $zones */
		$zones = $this->zoneRepository->findAll();
		/** @var $zoneList */
		$zoneList = [0 => 'keine'];

		foreach ( $zones as $zone ) {
			$zoneList[ $zone->getUid() ] = $zone->getName();
		}

		$this->view->assignMultiple([
			'action' => 'update',
			'zoneList' => $zoneList,
			'haltestelle' => $haltestelle,
			'haltestellen' => $this->haltestelleRepository->findAll()
		]);
	}

	/**
	 * action update
	 *
	 * @param \C3\C3baxi\Domain\Model\Haltestelle $haltestelle
	 *
	 * @return void
	 */
	public function updateAction(\C3\C3baxi\Domain\Model\Haltestelle $haltestelle) {


		$prevZone = (int)$this->request->getArgument('prevZone');
		if ( $prevZone !== $haltestelle->getZone()->getUid() ) {
			/** @var Zone $prevZone */
			$prevZone = $this->zoneRepository->findByUid($prevZone);
			$prevZone->removeHaltestelle($haltestelle);
			$this->zoneRepository->update($prevZone);

			$haltestelle->getZone()->addHaltestelle($haltestelle);
		}

		$this->persistenceManager->update($haltestelle);
		$this->persistenceManager->persistAll();

		if ( $this->request->getArgument('action_after') === 'edit' )
			$this->redirect('edit', null, null, ['haltestelle' => $haltestelle]);
		elseif ( $this->request->getArgument('action_after') === 'new' )
			$this->redirect('new', null, null, null);
		elseif ( $this->request->getArgument('action_after') === 'close' )
			$this->redirect('list', 'Haltestelle', null, null);
	}

	/**
	 * action delete
	 *
	 * @param \C3\C3baxi\Domain\Model\Haltestelle $haltestelle
	 *
	 * @return void
	 */
	public function deleteAction(\C3\C3baxi\Domain\Model\Haltestelle $haltestelle) {
		$this->haltestelleRepository->remove($haltestelle);
		$this->redirect('list');
	}

	/**
	 * action create
	 *
	 * @param \C3\C3baxi\Domain\Model\Haltestelle $haltestelle
	 *
	 * @return void
	 */
	public function createAction(\C3\C3baxi\Domain\Model\Haltestelle $haltestelle) {

		if ( $haltestelle->hasZone() ) {
			$haltestelle->getZone()->addHaltestelle($haltestelle);
			$this->zoneRepository->update($haltestelle->getZone());
		}

		$this->haltestelleRepository->add($haltestelle);
		$this->persistenceManager->persistAll();

		if ( $this->request->getArgument('action_after') === 'edit' )
			$this->redirect('edit', null, null, ['haltestelle' => $haltestelle]);
		elseif ( $this->request->getArgument('action_after') === 'new' )
			$this->redirect('new', null, null, null);
		elseif ( $this->request->getArgument('action_after') === 'close' )
			$this->redirect('list', 'Haltestelle', null, null);
//			}
	}

	/**
	 * action new
	 *
	 * @param \C3\C3baxi\Domain\Model\Haltestelle $haltestelle
	 *
	 * @return void
	 */
	public function newAction(\C3\C3baxi\Domain\Model\Haltestelle $haltestelle = null) {
		/** @var $zones */
		$zones = $this->zoneRepository->findAll();
		/** @var $zoneList */
		$zoneList = [0 => 'keine'];

		foreach ( $zones as $zone ) {
			$zoneList[ $zone->getUid() ] = $zone->getName();
		}

		if ( $haltestelle === null )
			$haltestelle = new Haltestelle();

		$this->view->assignMultiple([
			'zoneList' => $zoneList,
			'haltestelle' => $haltestelle,
			'action' => 'create'
		]);
	}

	/**
	 * @param
	 *
	 * @return void
	 */
	public function importAction() {

//			$fileName = $_FILES['tx_c3baxi_tools_c3baxibaxi']['name']['importFile'];
		$tmpFiles = $_FILES[ 'tx_c3baxi_tools_c3baxibaxi' ][ 'tmp_name' ][ 'importFile' ];

		foreach ( $tmpFiles as $tmpFile ) {
			/** ToDo: check File params and so on */

			$f = fopen($tmpFile, 'r');
			$first = true;
			//Gemeinde
			//Haltestellennummer
			//Buchungshaltestelle
			//Abrechnungshaltestelle
			//Wabe
			//Zone
			//X-Koordinate (Geo WGS84)
			//Y-Koordinate (Geo WGS84)

			while ($row = fgetcsv($f, 1024, ',', '"', "\\")) {
				if ( $first ) {
					$first = false;
					continue;
				}
				if ( empty($row[ 1 ]) || empty($row[ 0 ]) ) continue;

				$new = true;
				$number = intval(trim($row[ 1 ]));
				if ( $h = $this->haltestelleRepository->findOneByNumber($number) ) {
					$new = false;
					$haltestelle = $h;

					if ( $haltestelle->getWabe() != $row[ 4 ] )
						$haltestelle->setWabe($row[ 4 ]);

				} else {
					$haltestelle = new Haltestelle();
					$haltestelle->setNumber($number);
					$haltestelle->setName($row[ 2 ]);
					$haltestelle->setWabe($row[ 4 ]);
					$haltestelle->setLongitude($row[ 6 ]); // 11.
					$haltestelle->setLatitude($row[ 7 ]); // 49.
				}

				if ( $calculation = $this->haltestelleRepository->findOneByName($row[ 3 ]) ) {
					$haltestelle->setCalculation($calculation);
				}
				if ( $new ) $this->haltestelleRepository->add($haltestelle); else $this->haltestelleRepository->update($haltestelle);
			}
		}
		$this->persistenceManager->persistAll();

		$this->redirect('list');
	}

	/**
	 * Inject the product repository
	 *
	 * @param \C3\C3baxi\Domain\Repository\HaltestelleRepository $haltestelleRepository
	 */
	public function injectHaltestelleRepository(HaltestelleRepository $haltestelleRepository) {
		$this->haltestelleRepository = $haltestelleRepository;
	}

	/**
	 * Inject the product repository
	 *
	 * @param \C3\C3baxi\Domain\Repository\ZoneRepository $zoneRepository
	 */
	public function injectZoneRepository(ZoneRepository $zoneRepository) {
		$this->zoneRepository = $zoneRepository;
	}
}
