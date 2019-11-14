<?php
/**
 * Created by PhpStorm.
 * User: schuessler
 * Date: 08.01.2019
 * Time: 14:59
 */

namespace C3\C3baxi\Domain\Repository;

use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/**
 * Repository for tt_content objects
 */
class TtContentRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    //protected $objectType = '\GeorgRinger\News\Domain\Model\Ttcontent';
	protected $tableName = 'tt_content';
	public function findByUid( $uid ) {
//		DebuggerUtility::var_dump($uid);
		$query = $this->createQuery();
		$query->getQuerySettings()->setRespectStoragePage(FALSE);
		$query->matching( $query->equals("uid", $uid) );
		return $query->execute();
	}

	public function findByPid($pid){
		$query = $this->createQuery();
		$query->getQuerySettings()->setRespectStoragePage(FALSE);
		$query->matching( $query->equals("pid", $pid) );
		return $query->execute();
	}
}