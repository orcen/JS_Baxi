<?php

	namespace C3\C3baxi\Domain\Model;

	use \C3\C3baxi\Domain\Model\Haltestelle;
	use \TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
	use \TYPO3\CMS\Extbase\Persistence\ObjectStorage;
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
	 * Zone
	 */
	class Zone extends AbstractEntity
	{

		/**
		 * name
		 *
		 * @var string
		 * @validate NotEmpty
		 */
		protected $name = '';

		/**
		 * haltestellen
		 *
		 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\C3\C3baxi\Domain\Model\Haltestelle>
		 */
		protected $haltestellen;

		/**
		 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\C3\C3baxi\Domain\Model\FahrtZeit>
		 */
		protected $zeiten;
		/**
		 * linien
		 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\C3\C3baxi\Domain\Model\Linie>
		 */
		protected $linien;

		/**
		 * @return ObjectStorage
		 */
		public function getZeiten()
		{
			return $this->zeiten;
		}

		/**
		 * @param ObjectStorage $zeiten
		 * @return void
		 */
		public function setZeiten( ObjectStorage $zeiten ) : void
		{
			$this->zeiten = $zeiten;
		}

		/**
		 * @param FahrtZeit $zeit
		 * @return void
		 */
		public function addZeit( FahrtZeit $zeit ) {
			$this->zeiten->attach( $zeit );
		}

		/**
		 * @param FahrtZeit $zeit
		 * @return void
		 */
		public function removeZeit( FahrtZeit $zeit ) {
			$this->zeiten->detach( $zeit );
		}


		/**
		 * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\C3\C3baxi\Domain\Model\Haltestelle>
		 */
		public function getHaltestellen()
		{
			return $this->haltestellen;
		}

		/**
		 * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $haltestellen
		 */
		public function setHaltestellen( ObjectStorage $haltestellen ) : void
		{
			$this->haltestellen = $haltestellen;
		}

		/**
		 * @param \C3\C3baxi\Domain\Model\Haltestelle $haltestelle
		 */
		public function addHaltestelle( Haltestelle $haltestelle )
		{
			$this->haltestellen->attach( $haltestelle );
		}

		/**
		 * @param \C3\C3baxi\Domain\Model\Haltestelle $haltestelle
		 */
		public function removeHaltestelle( Haltestelle $haltestelle )
		{
			$this->haltestellen->detach( $haltestelle );
		}

		/**
		 * @param \C3\C3baxi\Domain\Model\Haltestelle $needle
		 */
		public function hasHaltestelle( Haltestelle $needle ) {

			foreach ( $this->haltestellen as $haltestelle ) {
				if( $haltestelle === $needle ) return true;
			}
			return false;
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

		public function __construct()
		{
			$this->haltestellen = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage;
			$this->zeiten = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage;
			$this->linien = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage;
		}

		/**
		 * @return ObjectStorage
		 */
		public function getLinien()
		{
			return $this->linien;
		}

		/**
		 * @param ObjectStorage $linien
		 */
		public function setLinien( ObjectStorage $linien ) : void
		{
			$this->linien = $linien;
		}
	}
