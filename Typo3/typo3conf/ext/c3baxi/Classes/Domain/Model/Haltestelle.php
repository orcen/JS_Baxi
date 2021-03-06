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
	 * fullname
	 *
	 * @var string
	 */
	protected $fullname = '';

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
    protected $number = 0;

	/**
	 * @var int
	 */
	protected $wabe = 0;

	/**
	 * @var \C3\C3baxi\Domain\Model\Haltestelle
	 */
    protected $calculation = null;

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
	 * @return Zone
	 */
	public function getZone(): ?Zone {
		return $this->zone;
	}

	/**
	 * @param Zone $zone
	 * @return Haltestelle
	 */
	public function setZone(?Zone $zone): Haltestelle {
		$this->zone = $zone;
		return $this;
	}

    public function unsetZone() {
    	$this->zone = 0;
    }

    public function hasZone() {
    	return ! ($this->zone == 0);
    }

    public function getFullName() {
    	$fullname = $this->name;
    	if( $this->zone ) {
    		$fullname .= ' - ' . $this->zone->getName();
    		$linien = [];
    		foreach( $this->zone->getLinien() as $line ) {
    			$linien[] = $line->getNr();
		    }
    		if(! empty($linien)) {
			    $fullname .= ' (' . implode(', ', $linien) . ')';
		    }
	    }

    	return $fullname;
	}

	/**
	 * @return string
	 */
	public function getNumber() {
		return $this->number;
	}

	/**
	 * @return int
	 */
	public function getWabe() : ?int {
		return $this->wabe;
	}

	/**
	 * @param int $wabe
	 *
	 * @return Haltestelle
	 */
	public function setWabe( int $wabe ) : Haltestelle {
		$this->wabe = $wabe;
		return $this;
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

	/**
	 * @return Haltestelle
	 */
	public function getCalculation(): ?Haltestelle {
		return $this->calculation;
	}

	/**
	 * @param Haltestelle $calculation
	 * @return Haltestelle
	 */
	public function setCalculation(?Haltestelle $calculation): Haltestelle {
		$this->calculation = $calculation;
		return $this;
	}
	}
