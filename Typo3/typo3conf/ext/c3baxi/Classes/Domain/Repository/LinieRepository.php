<?php


	namespace C3\C3baxi\Domain\Repository;


	use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

	class LinieRepository extends \TYPO3\CMS\Extbase\Persistence\Repository {

		protected $tableName = 'tx_c3baxi_domain_model_linie';

		public function initializeObject() {
			$querySettings = $this->objectManager->get( \TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings::class );
			$querySettings->setRespectStoragePage( FALSE );

			if( isset($GLOBALS['BE_USER'])) {
				if ( $GLOBALS[ 'BE_USER' ]->check('modules', 'tools_C3baxiBaxi') ) {
					$querySettings->setIgnoreEnableFields(true)
						->setIncludeDeleted(true);
//				$querySettings->setEnableFieldsToBeIgnored(array('starttime','endtime'));
				}
			}

			$this->setDefaultQuerySettings( $querySettings );
		}

		function findAll( $showHidden = FALSE ) {
			$query = $this->createQuery();
			$query->getQuerySettings()
				->setIgnoreEnableFields( TRUE )
				->setIncludeDeleted( FALSE );

			$query->setOrderings( [
				'nr' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING
			] );

			return $query->execute();
		}

		function findByCompany( $cid ) {
			$query = $this->createQuery();

			$query->statement( 'SELECT * FROM ' . $this->tableName
				. ' WHERE 1=1 AND `hidden` = 0 AND `deleted` = 0 '
				. ' AND `company` = ' . $cid
				. ' ORDER BY `nr` ASC' );

			return $query->execute();
		}

		public function findHiddenByUid( $uid ) {
			$query = $this->createQuery();

			// Here you enable the hidden and deleted Records
			$query->getQuerySettings()
				->setIgnoreEnableFields( TRUE )
				->setIncludeDeleted( TRUE );

			// Your query
			$query->matching( $query->equals( 'uid', $uid ) );
			return $query->execute()->getFirst();
		}
	}