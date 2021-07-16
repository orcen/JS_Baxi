<?php

namespace C3\C3baxi\Controller;

use C3\C3baxi\Domain\Model\Fahrt;
use C3\C3baxi\Domain\Model\FahrtZeit;
use C3\C3baxi\Domain\Model\Linie;
use Doctrine\Common\Util\Debug;
use TYPO3\CMS\Core\Messaging\FlashMessage;
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
 * FahrtController
 */
class FahrtController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
	/**
	 * @var \C3\C3baxi\Domain\Repository\FahrtRepository
	 * @inject
	 */
	protected $repository = null;
	/**
	 * @var \C3\C3baxi\Domain\Repository\LinieRepository
	 * @inject
	 */
	protected $linieRepository = null;
	/**
	 * @var \C3\C3baxi\Domain\Repository\ZoneRepository
	 * @inject
	 */
	protected $zoneRepository = null;

	/**
	 * @var \C3\C3baxi\Domain\Repository\FahrtZeitRepository
	 * @inject
	 */
	protected $fahrtZeitRepository = null;

	/**
	 * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
	 *
	 */
	protected $persistenceManager;

	/**
	 * action list
	 *
	 * @return void
	 */
	public function listAction() {
		$fahrts = $this->repository->findAll();
//        $tmp = [];
//        foreach( $fahrts as $fahrt ) {
//        	$linie = $fahrt->getLinie()->getNr();
//        	if( ! isset( $tmp[ $linie ] ) ) $tmp[$linie] = [];
//        	$tmp[ $linie ] [] = $fahrt;
//
//        }

		$this->view->assign('fahrts', $fahrts);
	}

	/**
	 * action show
	 *
	 * @param \C3\C3baxi\Domain\Model\Fahrt $fahrt
	 *
	 * @return void
	 */
	public function showAction(\C3\C3baxi\Domain\Model\Fahrt $fahrt) {
		$this->view->assign('fahrt', $fahrt);
	}

	/**
	 * @return void
	 */
	public function newAction() {
		$linien = $this->linieRepository->findAll();
		$linienListe = [
			null => 'Linie auswaehlen'
		];
		foreach ( $linien as $linie ) {
			$linienListe[ $linie->getUid() ] = $linie->getNr() . ' ' . $linie->getName();
		}
		$this->view->assign('linienListe', $linienListe);

		if ( $this->request->hasArgument('linie') )
			$this->view->assign('linie', $this->request->getArgument('linie'));

		$this->view->assign('action', 'create');
	}

	/**
	 * @param Fahrt $fahrt
	 *
	 * @return void
	 */
	public function editAction(\C3\C3baxi\Domain\Model\Fahrt $fahrt) {

		$linie = $fahrt->getLinie();
		if ( $linie ) {
			$this->view->assign('linie', $linie);
			$zeitenForm = [];

			foreach ( $linie->getZonen() as $zone ) {
				$zeitenForm[ $zone->getUid() ] = [
					'zone' => [
						'name' => $zone->getName(),
						'uid' => $zone->getUid()
					],
					'zeit' => 0
				];
			}
			$prev = 0;

			foreach ( $fahrt->getZeiten() as $zeit ) {

				if ( !isset($zeitenForm[ $zeit->getZone()->getUid() ]) ) {
					$this->fahrtZeitRepository->remove($zeit);
					continue;
				}

				$zeitenForm[ $zeit->getZone()->getUid() ][ 'fahrtzeit' ] = $zeit->getUid();
				$zeitenForm[ $zeit->getZone()->getUid() ][ 'zeit' ] = $zeit->getZeit();

				if ( $zeit->getZeit() > 0 ) {
					if ( $zeitenForm[ $zeit->getZone()->getUid() ][ 'zeit' ] < $prev ) {
						$zeitenForm[ $zeit->getZone()->getUid() ][ 'zeit' ]->modify('+1 day');
					}
					$prev = $zeitenForm[ $zeit->getZone()->getUid() ][ 'zeit' ];
				}

			}
			if ( $fahrt->isRueckfahrt() ) {
				$zeitenForm = array_reverse($zeitenForm, true);
			}

			$this->view->assignMultiple([
				'fahrt' => $fahrt,
				'zeiten' => $zeitenForm,
				'action' => 'update'
			]);
		}

	}


	/** Processing actions */

	/**
	 * @return void
	 */
	public function createAction() {
		$fahrt = new Fahrt();
		$fahrt->setName($this->request->getArgument('name'));

		$linieID = $this->request->getArgument('linie');
		if ( $linieID == null ) return false;
		$linie = $this->linieRepository->findByUid($linieID);

		$fahrt->setTage($this->request->getArgument('tage'));
		if ( $linie ) {
			$fahrt->setLinie($linie);
			$linie->addFahrt($fahrt);
			$this->linieRepository->update($linie);

			$zonen = $linie->getZonen()->toArray();
			if ( $this->request->getArgument('reverse') == 1 ) {
				$zonen = array_reverse($zonen);
				$fahrt->setRueckfahrt(true);
			}

			foreach ( $zonen as $zone ) {
				$fahrtZeit = new FahrtZeit();
				$fahrtZeit->setFahrt($fahrt);
				$fahrtZeit->setZone($zone);
				$fahrt->addZeit($fahrtZeit);
			}
		}

		$buchZeit = $this->request->getArgument('buchbar_bis');
		$fahrt->setBuchbarBis(strtotime($buchZeit));

		$this->repository->add($fahrt);
		$this->persistenceManager->persistAll();


		if ( $this->request->getArgument('action_after') === 'edit' )
			$this->redirect('edit', null, null, ['fahrt' => $fahrt]);
		elseif ( $this->request->getArgument('action_after') === 'new' )
			$this->redirect('new', null, null, null);
		elseif ( $this->request->getArgument('action_after') === 'close' )
			$this->redirect('index', 'Baxi', null, null);
	}


	/**
	 * @param \C3\C3baxi\Domain\Model\Fahrt $fahrt
	 */
	public function updateAction(\C3\C3baxi\Domain\Model\Fahrt $fahrt) {

		$args = $this->request->getArguments();

		$fahrt->setTage($args[ 'tage' ]);
		$prev = null;

		foreach ( $args[ 'zeit' ] as $zoneId => $zeit ) {
			if ( isset($args[ 'zeitZone' ][ $zoneId ]) ) {
				$uid = intval($args[ 'zeitZone' ][ $zoneId ]);
				$fahrtZeit = $this->fahrtZeitRepository->findByUid($uid);
				if ( $fahrtZeit == null ) {

					$zone = $this->zoneRepository->findByUid($zoneId);
					$fahrtZeit = new FahrtZeit();
					$fahrtZeit->setFahrt($fahrt);
					$fahrtZeit->setZone($zone);
					$fahrt->addZeit($fahrtZeit);
					$this->fahrtZeitRepository->add($fahrtZeit);
					$this->persistenceManager->persistAll();
				}
			} else {
				continue;
			}

			if ( empty($args[ 'zeit' ][ $fahrtZeit->getZone()->getUid() ]) ) {
				$fahrtZeit->setZeit(null);
			} else {
				$zeitObj = new \DateTimeImmutable('2000-01-01');
				$time = explode(':', $args[ 'zeit' ][ $fahrtZeit->getZone()->getUid() ]);
				$zeitObj = $zeitObj->setTime($time[ 0 ], $time[ 1 ]);

				if ( $prev && $zeitObj < $prev ) {
					$zeitObj->modify('+1 day');
				}
				$fahrtZeit->setZeit($zeitObj);

				$this->fahrtZeitRepository->update($fahrtZeit);
				$prev = $zeitObj;
			}
		}

		$this->repository->update($fahrt);
		$this->persistenceManager->persistAll();

		$this->addFlashMessage('Fahrt wurde gespeichert', 'Fahrt gespeichert', FlashMessage::OK);

		if ( $this->request->getArgument('action_after') === 'edit' )
			$this->redirect('edit', null, null, ['fahrt' => $fahrt]);
		elseif ( $this->request->getArgument('action_after') === 'new' )
			$this->redirect('new', null, null, null);
		elseif ( $this->request->getArgument('action_after') === 'close' )
			$this->redirect('index', 'Baxi', null, null);
	}

	/**
	 * @param Fahrt $fahrt
	 *
	 * @return void
	 */
	public function deleteAction(Fahrt $fahrt) {

		$linie = $fahrt->getLinie();
		$linie->removeFahrt($fahrt);
		$this->linieRepository->update($linie);
		foreach ( $fahrt->getZeiten() as $zeit ) {
			$this->fahrtZeitRepository->remove($zeit);
		}

		$this->repository->remove($fahrt);
		$this->persistenceManager->persistAll();
		$this->redirect('index', 'Baxi', null, null);
	}

	public function exportAction(Fahrt $fahrt) {
	}

	/**
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
	 * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
	 */
	public function importAction() {
		$tmpFiles = $_FILES[ 'tx_c3baxi_tools_c3baxibaxi' ][ 'tmp_name' ][ 'importFile' ];

		$linie = $this->request->getArgument('linie');
		$reverse = ($this->request->getArgument('reverse') == 1);

		/**
		 * @var Linie
		 *
		 */
		$linie = $this->linieRepository->findByUid($linie);
		/**
		 * @var ObjectStorage<Fahrt> $fahrten
		 */
		$fahrten = $linie->getFahrten();

		foreach ( $tmpFiles as $tmpFile ) {
			$f = fopen($tmpFile, 'r');

			$fahrPlan = [
				/*0 => [
					'nummer' => '1',
					'tage' => 'Mo,Di,Mi,Do',
					'zeiten' => [
						'Bayreuth Zone 2' => '00:00',
						'Brand Zone 1' => '00:00',
						'Ebnath Zone 1' => '00:00',
						'Neusorg Zone 2' => '00:00',
						'Neusorg Bahnhof' => '00:00',
						'Neusorg Zone 1' => '00:00',
						'Pullenreuth Zone 3' => '00:00',
						'Pullenreuth Zone 4' => '00:00',
						'Pullenreuth Zone 1' => '00:00',
						'Pullenreuth Zone 2' => '00:00',
						'Pullenreuth Zone 5' => '00:00',
						'Kulmain Zone 2' => '00:00',
						'Kulmain Zone 3' => '00:00',
						'Kulmain Zone 1' => '00:00',
						'Kemnath Zone 1' => '00:00',
					]
				]*/
			];
			$rowCnt = 0;

			while ($row = fgetcsv($f, 1024, ';', '"', "\\")) {
				$idx = $row[ 0 ];
				$data = array_slice($row, 1);

				// Fahrten Liste
				if ( $rowCnt == 0 ) {
					$fahrPlan = $data;
					array_walk($fahrPlan, function (&$item) {
						$arr = [
							'nummer' => $item,
							'tage' => '',
							'zeiten' => []
						];
						$item = $arr;
					});
				} // Tage der Fahrten
				elseif ( $rowCnt == 1 ) {
					for ( $i = 0; $i < count($data); $i++ ) {
						$fahrPlan[ $i ][ 'tage' ] = $data[ $i ];
					}
				} // Zonen und Zeiten
				else {
					for ( $i = 0; $i < count($data); $i++ ) {
						$fahrPlan[ $i ][ 'zeiten' ][ $idx ] = $data[ $i ];
					}
				}

				$rowCnt++;
			}
			DebuggerUtility::var_dump( $fahrPlan); exit;

			foreach ( $fahrPlan as $fahrtData ) {

				if ( empty(trim($fahrtData[ 'nummer' ])) ) continue;

				$update = false;
				$nummer = trim($fahrtData[ 'nummer' ]);
				$nummer = str_pad($nummer, 3, '0', STR_PAD_LEFT);
				$fahrt = null;
				foreach ( $fahrten as $f ) {
					if ( $f->getName() == $nummer ) {
						$fahrt = $f;
						$update = true;
						break;
					}
				}

				if ( !$fahrt ) {
					$fahrt = new Fahrt();
				}

				$fahrt->setName($nummer);

				$fahrt->setRueckfahrt($reverse);


				$tage = explode(',', $fahrtData[ 'tage' ]);
				$fahrt->setTage($tage);
				$fahrt->setLinie($linie);
				$buchbarBis = false;
				$fahrt->getZeiten()->removeAll($fahrt->getZeiten());

				foreach ( $fahrtData[ 'zeiten' ] as $zoneName => $zeit ) {
					$zone = $this->zoneRepository->findOneByName($zoneName);

					if ( $zone ) {

						$fz = $this->fahrtZeitRepository->findByDemand(['fahrt' => $fahrt->getUid(), 'zone' => $zone->getUid()]);
						if ( $fz->count() == 1 ) {
							$fahrtZeit = $fz->current();
						} elseif ( $fz->count() > 1 ) {
							$fz = $fz->toArray();
							$fahrtZeit = array_shift($fz);
							/**
							 * @var FahrtZeit $f
							 */
							foreach ( $fz as $f ) {
								$fahrt->removeZeit($f);
								$this->fahrtZeitRepository->remove($f);
							}

						} else {
							$fahrtZeit = new FahrtZeit();
						}


						$fahrtZeit->setFahrt($fahrt);

						if ( false == $fahrt->getZeiten()->contains($fahrtZeit) ) {
							$fahrt->addZeit($fahrtZeit);
						}

						$fahrtZeit->setZone($zone);
						if ( !empty($zeit) ) {
							$zeitObj = \DateTimeImmutable::createFromFormat('H:i', $zeit);
							$zeitObj = $zeitObj->setDate(2000, 1, 1);

							$fahrtZeit->setZeit($zeitObj);

							if ( !$buchbarBis ) {
								$buchbarBis = $zeitObj->modify('-1 HOUR');
							}
						}
						$fahrt->addZeit($fahrtZeit);
					}
				}

				$fahrt->setBuchbarBis($buchbarBis);
				if ( $update ) {
					$this->repository->update($fahrt);
				} else {
					$this->repository->add($fahrt);
					$linie->addFahrt($fahrt);
				}

				$this->persistenceManager->persistAll();
			}
		}

		$this->addFlashMessage('Fahrten wurden importiert', 'Fahrt', FlashMessage::OK);
		$this->redirect('index', 'Baxi', null, null);
	}

	public function injectPersistenceManager(\TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager $persistenceManager) {
		$this->persistenceManager = $persistenceManager;
	}

	protected function initializeUpdateAction() {

		$fahrt = $this->request->getArgument('fahrt');

		$buchbarbis = new \DateTime('2000-01-01');
		$buchbarbis->modify($fahrt[ 'buchbarBis' ]);
		$fahrt[ 'buchbarBis' ] = $buchbarbis;
		$this->request->setArgument('fahrt', $fahrt);

//			DebuggerUtility::var_dump($this->request); exit;
//			$buchbarbis->setTime($buchZeit[0], $buchZeit[1]);
	}

	protected function errorAction() {
		return parent::errorAction(); // TODO: Change the autogenerated stub
	}


}
