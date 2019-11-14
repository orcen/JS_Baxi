<?php


	namespace C3\C3baxi\Domain\Repository;


	class HaltestelleRepository extends \TYPO3\CMS\Extbase\Persistence\Repository {
		protected $tableName = 'tx_c3baxi_domain_model_haltestelle';

		protected $defaultOrderings = [
			'name' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING
		];

		public function initializeObject() {
			$querySettings = $this->objectManager->get( \TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings::class );
			$querySettings->setRespectStoragePage( FALSE );
			$this->setDefaultQuerySettings( $querySettings );
		}

		function findAll() {
			$query = $this->createQuery();
			return $query->execute();
		}

		public function findByName( $name ) {
			$query = $this->createQuery();
			$query->matching( $query->like( 'name', '%' . $name . '%' ) );
			return $query->execute();
		}

		function findAllAssigned( $zone = false) {
			$query = $this->createQuery();
			if( !$zone ) {
				$query->matching( $query->logicalNot( $query->equals( 'zone', 0 ) ) );
			}
			else {
				$query->matching( $query->logicalAnd( $query->in( 'zone', $zone ) ) );
			}
			return $query->execute();
		}
	}