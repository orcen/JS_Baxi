<?php


	namespace C3\C3baxi\Domain\Repository;


	use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

	class RatingRepository extends \TYPO3\CMS\Extbase\Persistence\Repository {
		protected $tableName = 'tx_c3baxi_domain_model_rating';

		public function findByUid( $uid, $type = 'false' ) {
			if( $type ) {
				$query = $this->createQuery();
				$query->matching( $query->equals( 'type', $type ) );
				return $query->execute();
			}

			return false;
		}

		public function findForUser( $cruserId, $objectId ){
			$query = $this->createQuery();

			$query->matching(
				$query->logicalAnd( [
					$query->equals( 'cruserId', $cruserId ),
					$query->equals( 'object_id', $objectId ) ])
			);
//			$queryParser = $this->objectManager->get(\TYPO3\CMS\Extbase\Persistence\Generic\Storage\Typo3DbQueryParser::class);
//			\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($queryParser->convertQueryToDoctrineQueryBuilder($query)->getSQL());
			return $query->execute();
		}

	}