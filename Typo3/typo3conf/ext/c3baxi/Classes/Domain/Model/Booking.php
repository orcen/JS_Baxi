<?php


	namespace C3\C3baxi\Domain\Model;


	class Booking extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
	{
		/**
		 * adults
		 * @var integer
		 */
		protected $adults;

		/**
		 * children
		 * @var integer
		 */
		protected $children;

		/**
		 * @var \C3\C3baxi\Domain\Model\Fahrt
		 */
		protected $fahrt;

		/**
		 * @var \DateTime
		 */
		protected $date;

		/**
		 * @var \TYPO3\CMS\Extbase\Domain\Model\FrontendUser
		 */
		protected $user;

		/**
		 * @var \C3\C3baxi\Domain\Model\Haltestelle
		 */
		protected $startStation;

		/**
		 * @var \C3\C3baxi\Domain\Model\Haltestelle
		 */
		protected $endStation;

		/**
		 * @var string
		 */
		protected $info;

		/**
		 * @var int
		 */
		protected $deleted = 0;

		/**
		 * @return int
		 */
		public function getAdults() : int
		{
			return $this->adults;
		}

		/**
		 * @param int $adults
		 */
		public function setAdults( int $adults ) : void
		{
			$this->adults = $adults;
		}

		/**
		 * @return int
		 */
		public function getChildren() : int
		{
			return (int) $this->children;
		}

		/**
		 * @param int $children
		 */
		public function setChildren( int $children ) : void
		{
			$this->children = $children;
		}

		/**
		 * @return Fahrt
		 */
		public function getFahrt() : Fahrt
		{
			return $this->fahrt;
		}

		/**
		 * @param Fahrt $fahrt
		 */
		public function setFahrt( Fahrt $fahrt ) : void
		{
			$this->fahrt = $fahrt;
		}

		/**
		 * @return int
		 */
		public function getDate()
		{
			return $this->date;
		}

		/**
		 * @param int $date
		 */
		public function setDate( int $date ) : void
		{
			$this->date = $date;
		}

		/**
		 * @return \TYPO3\CMS\Extbase\Domain\Model\FrontendUser
		 */
		public function getUser() : \TYPO3\CMS\Extbase\Domain\Model\FrontendUser
		{
			return $this->user;
		}

		/**
		 * @param \TYPO3\CMS\Extbase\Domain\Model\FrontendUser $user
		 */
		public function setUser( \TYPO3\CMS\Extbase\Domain\Model\FrontendUser $user ) : void
		{
			$this->user = $user;
		}

		/**
		 * @return Haltestelle
		 */
		public function getStartStation() : Haltestelle
		{
			return $this->startStation;
		}

		/**
		 * @param Haltestelle $startStation
		 */
		public function setStartStation( Haltestelle $startStation ) : void
		{
			$this->startStation = $startStation;
		}

		/**
		 * @return Haltestelle
		 */
		public function getEndStation() : Haltestelle
		{
			return $this->endStation;
		}

		/**
		 * @param Haltestelle $endStation
		 */
		public function setEndStation( Haltestelle $endStation ) : void
		{
			$this->endStation = $endStation;
		}

		/**
		 * @return string
		 */
		public function getInfo() : ?string
		{
			return $this->info;
		}

		/**
		 * @param string $info
		 */
		public function setInfo( string $info ) : void
		{
			$this->info = $info;
		}

		/**
		 * @return int
		 */
		public function getDeleted() : int {
			return $this->deleted;
		}

		/**
		 * @param int $deleted
		 *
		 * @return Booking
		 */
		public function setDeleted( int $deleted ) : Booking {
			$this->deleted = $deleted;
			return $this;
		}



	}