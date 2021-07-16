<?php

	namespace C3\C3baxi\Domain\Model;


	use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
	use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

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
	 * Fahrt
	 */
	class Fahrt extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
	{

		/**
		 * name
		 *
		 * @var string
		 */
		protected $name = '';

		/**
		 * linie
		 *
		 * @var \C3\C3baxi\Domain\Model\Linie
		 */
		protected $linie = NULL;

		/**
		 * @var string
		 */
		protected $tage = NULL;

		/**
		 * @var \DateTime
		 */
		protected $buchbarBis;

		/**
		 * @var bool
		 */
		protected $rueckfahrt = false;

		/**
		 * @return \DateTime
		 */
		public function getBuchbarBis()
		{
			return $this->buchbarBis;
		}

		/**
		 * @param \DateTime $buchbarBis
		 */
		public function setBuchbarBis( $buchbarBis = 0) : void
		{
			$this->buchbarBis = $buchbarBis;
		}

		/**
		 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\C3\C3baxi\Domain\Model\FahrtZeit>
		 */
		protected $zeiten = NULL;

		/**
		 * @return ObjectStorage
		 */
		public function getZeiten()
		{
			return $this->zeiten;
		}

		/**
		 * @param ObjectStorage $zeiten
		 *
		 * @return void
		 */
		public function setZeiten( ObjectStorage $zeiten ) : void
		{
			$this->zeiten = $zeiten;
		}

		/**
		 * @param FahrtZeit $zeit
		 *
		 * @return void
		 */
		public function addZeit( FahrtZeit $zeit )
		{
			$this->zeiten->attach( $zeit );
		}

		/**
		 * @param FahrtZeit $zeit
		 *
		 * @return void
		 */
		public function removeZeit( FahrtZeit $zeit )
		{
			$this->zeiten->detach( $zeit );
		}


		/**
		 * @return array
		 */
		public function getTage()
		{
			return explode( ',', $this->tage );
		}

		/**
		 * @param array $tage
		 *
		 * @return void
		 */
		public function setTage( $tage = [] )
		{
			$this->tage = implode( ',', $tage );
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
		 *
		 * @return void
		 */
		public function setName( $name )
		{
			$this->name = $name;
		}

		/**
		 * __construct
		 */
		public function __construct()
		{

			//Do not remove the next line: It would break the functionality
			$this->initStorageObjects();
		}

		/**
		 * Initializes all ObjectStorage properties
		 * Do not modify this method!
		 * It will be rewritten on each save in the extension builder
		 * You may modify the constructor of this class instead
		 *
		 * @return void
		 */
		protected function initStorageObjects()
		{
			$this->zeiten = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage;
		}

		/**
		 * Returns the linie
		 *
		 * @return \C3\C3baxi\Domain\Model\Linie $linie
		 */
		public function getLinie()
		{
			return $this->linie;
		}

		/**
		 * Sets the linie
		 *
		 * @param \C3\C3baxi\Domain\Model\Linie $linie
		 *
		 * @return void
		 */
		public function setLinie( \C3\C3baxi\Domain\Model\Linie $linie )
		{
			$this->linie = $linie;
		}

		/**
		 * @return bool
		 */
		public function isRueckfahrt(): bool {
			return $this->rueckfahrt;
		}

		/**
		 * @param bool $rueckfahrt
		 */
		public function setRueckfahrt(bool $rueckfahrt): void {
			$this->rueckfahrt = $rueckfahrt;
		}



		public function getStationTime( \C3\C3baxi\Domain\Model\Haltestelle $station ){
			if( $station->getZone() === null ) return false;

			foreach ( $this->zeiten as $zeit ) {
				if( $zeit->getZone() === $station->getZone() ) {
					$stationTime = $zeit->getZeit();
					return $stationTime ;
				}
			}
		}

	}
