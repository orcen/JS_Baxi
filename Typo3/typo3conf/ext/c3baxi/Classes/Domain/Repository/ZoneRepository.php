<?php


	namespace C3\C3baxi\Domain\Repository;

	use C3\C3baxi\Domain\Model\Zone;
	use TYPO3\CMS\Extbase\Persistence\Repository;

	class ZoneRepository extends Repository
	{
		protected $tableName = 'tx_c3baxi_domain_model_zone';

		protected $defaultOrderings = [
			'name' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING
		];
		public function initializeObject() {
			$querySettings = $this->objectManager->get(\TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings::class);
			$querySettings->setRespectStoragePage(false);
			$this->setDefaultQuerySettings($querySettings);
		}
		function findAll( ) {
			$query = $this->createQuery();

//			$query->statement('SELECT * FROM ' . $this->tableName . ' WHERE 1=1 AND hidden = 0 AND deleted = 0');
			return $query->execute();
		}

		function findStations( int $zone ) {

			$query = $this->createQuery();
			$query->statement (
				'SELECT * FROM `tx_c3baxi_domain_model_haltestelle` WHERE 1=1 AND hidden = 0 AND deleted = 0 AND `zone` = ' . $zone . ' ORDER BY `name` ASC'
			);
			return $query->execute();
		}
	}