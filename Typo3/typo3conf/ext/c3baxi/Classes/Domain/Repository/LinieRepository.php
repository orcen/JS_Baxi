<?php


	namespace C3\C3baxi\Domain\Repository;


	class LinieRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
	{
		protected $tableName = 'tx_c3baxi_domain_model_linie';

		public function initializeObject()
		{
			$querySettings = $this->objectManager->get( \TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings::class );
			$querySettings->setRespectStoragePage( FALSE );
			$this->setDefaultQuerySettings( $querySettings );
		}

		function findAll()
		{
			$query = $this->createQuery();

			$query->statement( 'SELECT * FROM ' . $this->tableName . ' WHERE 1=1 AND `hidden` = 0 AND `deleted` = 0 ORDER BY `nr` ASC' );

			return $query->execute();
		}

		function findByCompany( $cid )
		{
			$query = $this->createQuery();

			$query->statement( 'SELECT * FROM ' . $this->tableName
				. ' WHERE 1=1 AND `hidden` = 0 AND `deleted` = 0 '
				. ' AND `company` = ' . $cid
				. ' ORDER BY `nr` ASC' );

			return $query->execute();
		}
	}