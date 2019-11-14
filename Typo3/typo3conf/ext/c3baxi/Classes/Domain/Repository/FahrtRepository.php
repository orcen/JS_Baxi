<?php
namespace C3\C3baxi\Domain\Repository;


/***
 *
 * This file is part of the "Baxi Fahrten" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2019 
 *
 ***/
class FahrtRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    protected $tableName = 'tx_c3baxi_domain_model_fahrt';
	public function initializeObject() {
		$querySettings = $this->objectManager->get(\TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings::class);
		$querySettings->setRespectStoragePage(false);
		$this->setDefaultQuerySettings($querySettings);
	}
    function findAll()
    {
        $query = $this->createQuery();
        $query->statement('SELECT * FROM ' . $this->tableName . ' WHERE 1=1 AND hidden = 0 AND deleted = 0');
        return $query->execute();
    }
}
