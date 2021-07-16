<?php
namespace C3\C3baxi\Domain\Model;


use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

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
 * Linie
 */
class Linie extends AbstractEntity
{

    /**
     * nr
     * 
     * @var int
     */
    protected $nr = 0;

    /**
     * name
     * 
     * @var string
     */
    protected $name = '';

	/**
	 * fahrten
	 *
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\C3\C3baxi\Domain\Model\Fahrt>
	 */
	protected $fahrten;

	/**
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\C3\C3baxi\Domain\Model\Zone>
	 *
	 */
	protected $zonen;

	/**
	 * @var \C3\C3baxi\Domain\Model\Company|null
	 */
	protected $company = null;

	/**
	 * @var bool
	 */
	protected $hidden;

	/**
	 * @var bool
	 */
	protected $cityLine = false;

	/**
	 * @var \DateTime
	 */
	protected $starttime = null;

	/**
	 * @var \DateTime
	 */
	protected $endtime = null;

	/**
	 * @var float
	 */
	protected $flatrateBasePrice = 0.0;
	/**
	 * @var float
	 */
	protected $flatrateUnitPrice = 0.0;
	/**
	 * @var float
	 */
	protected $flatrateSpecialBasePrice = 0.0;
	/**
	 * @var float
	 */
	protected $flatrateSpecialUnitPrice = 0.0;


	/**
	 * @return ObjectStorage
	 */
	public function getZonen() : ObjectStorage
	{
		return $this->zonen;
	}

	/**
	 * @param ObjectStorage $zonen
	 */
	public function setZonen( ObjectStorage $zonen ) : void
	{
		$this->zonen = $zonen;
	}
	/**
	 * @param \C3\C3baxi\Domain\Model\Zone $zone
	 * @return void
	 */
	public function addZone( Zone $zone ) {
		$this->zonen->attach( $zone);
	}
	/**
	 * @param \C3\C3baxi\Domain\Model\Zone $zone
	 * @return void
	 */
	public function removeZone( Zone $zone ) {
		$this->zonen->detach( $zone );
	}

	/**
	 * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\C3\C3baxi\Domain\Model\Fahrt>
	 */
	public function getFahrten()
	{
		return $this->fahrten;
	}

	/**
	 * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $fahrten
	 * @return void
	 */
	public function setFahrten( \TYPO3\CMS\Extbase\Persistence\ObjectStorage $fahrten ) {
		$this->fahrten = $fahrten;
	}

	/**
	 * @param  \C3\C3baxi\Domain\Model\Fahrt $fahrt
	 * @return void
	 */
	public function addFahrt( \C3\C3baxi\Domain\Model\Fahrt $fahrt )
	{
		$this->fahrten->attach( $fahrt );
	}

	/**
	 * @param \C3\C3baxi\Domain\Model\Fahrt $fahrt
	 * @return void
	 */
	public function removeFahrt( \C3\C3baxi\Domain\Model\Fahrt $fahrt) {
		$this->fahrten->detach( $fahrt );
	}

	/**
	 * @return bool
	 */
	public function isHidden() : bool {
		return $this->hidden;
	}

	/**
	 * @param bool $hidden
	 *
	 * @return Linie
	 */
	public function setHidden( bool $hidden ) : Linie {
		$this->hidden = $hidden;
		return $this;
	}



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
     * Returns the nr
     * 
     * @return int $nr
     */
    public function getNr()
    {
        return $this->nr;
    }

    /**
     * Sets the nr
     * 
     * @param int $nr
     * @return void
     */
    public function setNr($nr)
    {
        $this->nr = $nr;
    }

	/**
	 * @return Company|null
	 */
	public function getCompany(): ?Company {
		return $this->company;
	}

	/**
	 * @param Company|null $company
	 * @return Linie
	 */
	public function setCompany(?Company $company): Linie {
		$this->company = $company;
		return $this;
	}



	public function removeCompany(){
		$this->company = null;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isCityLine(): bool {
		return $this->cityLine;
	}

	/**
	 * @param bool $cityLine
	 */
	public function setCityLine(bool $cityLine): void {
		$this->cityLine = $cityLine;
	}




	public function __construct()
	{
		$this->fahrten = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage;
		$this->zonen = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage;
	}

	/**
	 * @return \DateTime
	 */
	public function getStarttime(): ?\DateTime {
		return $this->starttime;
	}

	/**
	 * @param \DateTime $starttime
	 */
	public function setStarttime(?\DateTime $starttime): void {
		$this->starttime = $starttime;
	}

	/**
	 * @return \DateTime
	 */
	public function getEndtime(): ?\DateTime {
		return $this->endtime;
	}

	/**
	 * @param \DateTime $endtime
	 */
	public function setEndtime(?\DateTime $endtime): void {
		$this->endtime = $endtime;
	}

	/**
	 * @return float
	 */
	public function getFlatrateBasePrice(): float {
		return $this->flatrateBasePrice;
	}

	/**
	 * @param float $flatrateBasePrice
	 * @return Linie
	 */
	public function setFlatrateBasePrice(float $flatrateBasePrice): Linie {
		$this->flatrateBasePrice = $flatrateBasePrice;
		return $this;
	}

	/**
	 * @return float
	 */
	public function getFlatrateUnitPrice(): float {
		return $this->flatrateUnitPrice;
	}

	/**
	 * @param float $flatrateUnitPrice
	 * @return Linie
	 */
	public function setFlatrateUnitPrice(float $flatrateUnitPrice): Linie {
		$this->flatrateUnitPrice = $flatrateUnitPrice;
		return $this;
	}

	/**
	 * @return float
	 */
	public function getFlatrateSpecialBasePrice(): float {
		return $this->flatrateSpecialBasePrice;
	}

	/**
	 * @param float $flatrateSpecialBasePrice
	 * @return Linie
	 */
	public function setFlatrateSpecialBasePrice(float $flatrateSpecialBasePrice): Linie {
		$this->flatrateSpecialBasePrice = $flatrateSpecialBasePrice;
		return $this;
	}

	/**
	 * @return float
	 */
	public function getFlatrateSpecialUnitPrice(): float {
		return $this->flatrateSpecialUnitPrice;
	}

	/**
	 * @param float $flatrateSpecialUnitPrice
	 * @return Linie
	 */
	public function setFlatrateSpecialUnitPrice(float $flatrateSpecialUnitPrice): Linie {
		$this->flatrateSpecialUnitPrice = $flatrateSpecialUnitPrice;
		return $this;
	}


}
