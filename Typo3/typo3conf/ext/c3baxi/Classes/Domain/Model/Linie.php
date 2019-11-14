<?php
namespace C3\C3baxi\Domain\Model;


use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use \C3\C3baxi\Domain\Model\Zone;
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
class Linie extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
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
	 */
	protected $zonen;

	/**
	 * @var \C3\C3baxi\Domain\Model\Company
	 */
	protected $company;

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
	 * @return Company
	 */
	public function getCompany() : ?Company
	{
		return $this->company;
	}

	/**
	 * @param Company $company
	 *
	 * @return Linie
	 */
	public function setCompany( Company $company ) : Linie
	{
		$this->company = $company;
		return $this;
	}



	public function __construct()
	{
		$this->fahrten = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage;
		$this->zonen = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage;
	}
}
