<?php


	namespace C3\C3baxi\Domain\Repository;


	class CompanyRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
	{
		protected $tableName = 'tx_c3baxi_domain_model_company';
		protected $defaultOrderings = [
			'name' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING
		];

		public function initializeObject()
		{
			$querySettings = $this->objectManager->get( \TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings::class );
			$querySettings->setRespectStoragePage( FALSE );
			$this->setDefaultQuerySettings( $querySettings );
		}

		public function findAll()
		{
			$query = $this->createQuery();

			$query->statement( 'SELECT * FROM ' . $this->tableName . ' WHERE 1=1 AND `hidden` = 0 AND `deleted` = 0 ORDER BY `name` ASC' );

			return $query->execute();

//			return parent::findAll(); // TODO: Change the autogenerated stub
		}

	}