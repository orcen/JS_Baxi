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
 * Haltestelle
 */
class Haltestelle extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * name
     * 
     * @var string
     * @validate NotEmpty
     */
    protected $name = '';

    /**
     * latitude
     * 
     * @var float
     * @validate NotEmpty
     */
    protected $latitude = 0.0;

    /**
     * longitude
     * 
     * @var float
     * @validate NotEmpty
     */
    protected $longitude = 0.0;

    /**
     * zone
     * 
     * @var \C3\C3baxi\Domain\Model\Zone
     */
    protected $zone = null;

	/**
	 * @var string
	 */
    protected $number;

    /**
     * Returns the name
     * 
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the name
     * 
     * @param string $name
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Returns the latitude
     * 
     * @return float $latitude
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Sets the latitude
     * 
     * @param float $latitude
     * @return void
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    }

    /**
     * Returns the longitude
     * 
     * @return float $longitude
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Sets the longitude
     * 
     * @param float $longitude
     * @return void
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
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

    public function unsetZone() {
    	$this->zone = 0;
    }

	/**
	 * @return string
	 */
	public function getNumber() {
		return $this->number;
	}

	/**
	 * @param string $number
	 *
	 * @return Haltestelle
	 */
	public function setNumber( string $number ) : Haltestelle {
		$this->number = $number;
		return $this;
	}




}
