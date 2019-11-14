<?php


	namespace C3\C3baxi\Domain\Repository;


//	use C3\C3baxi\Domain\Model\User;
	use \In2code\Femanager\Domain\Model\User;

	class UserRepository extends \In2code\Femanager\Domain\Repository\UserRepository
	{

		protected $tableName = 'fe_users';

		public function initializeObject()
		{
			$querySettings = $this->objectManager->get( \TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings::class );
			$querySettings->setRespectStoragePage( FALSE );
			$this->setDefaultQuerySettings( $querySettings );
		}
		/**
		 * Overload Find by UID to also get hidden records
		 *
		 * @param int $uid fe_users UID
		 * @return User
		 */
		public function findByUid($uid)
		{
			$query = $this->createQuery();
			$this->ignoreEnableFieldsAndStoragePage($query);
			$query->getQuerySettings()->setRespectSysLanguage(false);
			$and = [$query->equals('uid', $uid)];

			/** @var User $user */
			$user = $query->matching($query->logicalAnd($and))->execute()->getFirst();

			return $user;
		}
	}