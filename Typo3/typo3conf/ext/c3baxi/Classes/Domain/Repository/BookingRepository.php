<?php


	namespace C3\C3baxi\Domain\Repository;


	use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

	class BookingRepository extends \TYPO3\CMS\Extbase\Persistence\Repository {
		protected $tableName = 'tx_c3baxi_domain_model_booking';

		protected $defaultOrderings = [
			'date' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING
		];

		public function initializeObject() {
			$querySettings = $this->objectManager->get( \TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings::class );
//			$querySettings->setStoragePageIds( );
			$querySettings->setRespectStoragePage( FALSE );
			$this->setDefaultQuerySettings( $querySettings );
		}

		function findByUser( $userId ) {
			$query = $this->createQuery();

			$query->matching( $query->like( 'user', $userId ) );
//			var_dump( $query->execute() );
			return $query->execute();
		}

		function findByUserAndDate( $userId, $date = 0 ) {
			$query = $this->createQuery();
			$constraints = [
				$query->equals( 'user', $userId ),
			];

			$datum = new \DateTime();
			$datum->setTimezone( new \DateTimeZone( 'EUROPE/BERLIN' ) );
			$datum->setTimestamp( $date );

			$datum->setTime( 0, 0, 0 );
			$constraints[] = $query->greaterThanOrEqual( 'date', $datum->getTimestamp() ); //format( 'Y-m-d H:i:s' )  );

			$datum->setTime( 23, 59, 59 );
			$constraints[] = $query->lessThanOrEqual( 'date', $datum->getTimestamp() ); //format( 'Y-m-d H:i:s' ) );

			$query->matching( $query->logicalAnd( $constraints ) );
			return $query->execute();
		}

		/**
		 * @param int $date
		 * @param int $ride
		 */
		function findRideOfDay( $date = 0, $ride = 0 ) {
			$query = $this->createQuery();

			$constraints = [
				$query->equals( 'fahrt', $ride ),
			];

			$datum = new \DateTime();
			$datum->setTimezone( new \DateTimeZone( 'EUROPE/BERLIN' ) );
			$datum->setTimestamp( $date );

			$datum->setTime( 0, 0, 0 );
			$constraints[] = $query->greaterThanOrEqual( 'date', $datum->getTimestamp() ); //format( 'Y-m-d H:i:s' )  );

			$datum->setTime( 23, 59, 59 );
			$constraints[] = $query->lessThanOrEqual( 'date', $datum->getTimestamp() ); //format( 'Y-m-d H:i:s' ) );

			$query->matching( $query->logicalAnd( $constraints ) );

			return $query->execute();
		}

	}