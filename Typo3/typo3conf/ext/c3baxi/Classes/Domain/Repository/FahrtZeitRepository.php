<?php


	namespace C3\C3baxi\Domain\Repository;


	class FahrtZeitRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
	{
		protected $tableName = 'tx_c3baxi_domain_model_fahrtzeit';
		public function initializeObject() {
			$querySettings = $this->objectManager->get(\TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings::class);
			$querySettings->setStoragePageIds( array($GLOBALS["TSFE"]->id));
//			$querySettings->setRespectStoragePage(false);
			$this->setDefaultQuerySettings($querySettings);
		}
		function findAll()
		{
			$query = $this->createQuery();

			$query->statement(
				'SELECT * FROM ' . $this->tableName . ' '
				. 'WHERE 1=1 AND hidden = 0 AND deleted = 0'
			);

			return $query->execute();
		}
	}