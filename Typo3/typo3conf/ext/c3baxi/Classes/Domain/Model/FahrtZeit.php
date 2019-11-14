<?php
namespace C3\C3baxi\Domain\Model;


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
/**
 * FahrtZeit
 */
class FahrtZeit extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * zeit
     * 
     * @var int
     */
    protected $zeit = 0;

    /**
     * fahrt
     * 
     * @var \C3\C3baxi\Domain\Model\Fahrt
     */
    protected $fahrt = null;

    /**
     * zone
     * 
     * @var \C3\C3baxi\Domain\Model\Zone
     */
    protected $zone = null;

    /**
     * Returns the zeit
     * 
     * @return int $zeit
     */
    public function getZeit()
    {
        return $this->zeit;
    }

    /**
     * Sets the zeit
     * 
     * @param int $zeit
     * @return void
     */
    public function setZeit(int $zeit)
    {
        $this->zeit = $zeit;
    }

    /**
     * Returns the fahrt
     * 
     * @return \C3\C3baxi\Domain\Model\Fahrt $fahrt
     */
    public function getFahrt()
    {
        return $this->fahrt;
    }

    /**
     * Sets the fahrt
     * 
     * @param \C3\C3baxi\Domain\Model\Fahrt $fahrt
     * @return void
     */
    public function setFahrt(\C3\C3baxi\Domain\Model\Fahrt $fahrt)
    {
        $this->fahrt = $fahrt;
    }

    /**
     * Returns the zone
     * 
     * @return \C3\C3baxi\Domain\Model\Zone $zone
     */
    public function getZone()
    {
        return $this->zone;
    }

    /**
     * Sets the zone
     * 
     * @param \C3\C3baxi\Domain\Model\Zone $zone
     * @return void
     */
    public function setZone(\C3\C3baxi\Domain\Model\Zone $zone)
    {
        $this->zone = $zone;
    }
}
