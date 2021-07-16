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
		protected $fahrt = null;

		/**
		 * @var \DateTime
		 */
		protected $date;

		/**
		 * @var \C3\C3baxi\Domain\Model\User
		 */
		protected $user;

		/**
		 * @var \C3\C3baxi\Domain\Model\Haltestelle
		 */
		protected $startStation = null;

		/**
		 * @var \C3\C3baxi\Domain\Model\Haltestelle
		 */
		protected $endStation = null;

		/**
		 * @var string
		 */
		protected $info;

		/**
		 * @var int
		 */
		protected $deleted = 0;

		/**
		 * @var bool
		 */
		protected $confirmed = false;

		/**
		 * @var bool
		 */
		protected $approved = false;

		/**
		 * @var int
		 */
		protected $cruserId = 0;


		/**
		 * @var int
		 */
		protected $tstamp = 0;


		/**
		 * @var bool
		 */
		protected $reminderSend = false;

		/**
		 * @var int
		 */
		protected $sms = 0;

		/**
		 * @var bool
		 */
		protected $export = false;

		/**
		 * @var int
		 */
		protected $subscriptionID = 0;

		/**
		 * @return int
		 */
		public function getCruserId(): int {
			return $this->cruserId;
		}

		/**
		 * @param int $cruserId
		 */
		public function setCruserId(int $cruserId): void {
			$this->cruserId = $cruserId;
		}

		/**
		 * @return int
		 */
		public function getTstamp(): int {
			return $this->tstamp;
		}

		/**
		 * @param int $tstamp
		 */
		public function setTstamp(int $tstamp): void {
			$this->tstamp = $tstamp;
		}

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
		public function getFahrt(): ?Fahrt {
			return $this->fahrt;
		}

		/**
		 * @param Fahrt $fahrt
		 */
		public function setFahrt(Fahrt $fahrt): void {
			$this->fahrt = $fahrt;
		}



		/**
		 * @return \DateTime
		 */
		public function getDate()
		{
			return $this->date;
		}

		/**
		 * @param \DateTime $date
		 */
		public function setDate( \DateTime $date ) : void
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
		public function getStartStation(): ?Haltestelle {
			return $this->startStation;
		}

		/**
		 * @param Haltestelle $startStation
		 * @return Booking
		 */
		public function setStartStation(?Haltestelle $startStation): Booking {
			$this->startStation = $startStation;
			return $this;
		}

		/**
		 * @return Haltestelle
		 */
		public function getEndStation(): ?Haltestelle {
			return $this->endStation;
		}

		/**
		 * @param Haltestelle $endStation
		 * @return Booking
		 */
		public function setEndStation(?Haltestelle $endStation): Booking {
			$this->endStation = $endStation;
			return $this;
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

		public function getTime( ) {
			foreach( $this->getFahrt()->getZeiten() as $zeit ) {
				if( $zeit->getZone() === $this->getStartStation()->getZone() ) {
					return $zeit->getZeit();
				}
			}
		}

		/**
		 * @return bool
		 */
		public function isConfirmed(): bool {
			return (bool) $this->confirmed;
		}


		/**
		 * @param bool $confirmed
		 */
		public function setConfirmed(bool $confirmed): void {
			$this->confirmed = $confirmed;
		}

		/**
		 * @return bool
		 */
		public function isApproved(): bool {
			return $this->approved;
		}

		/**
		 * @param bool $approved
		 */
		public function setApproved(bool $approved): void {
			$this->approved = $approved;
		}

		/**
		 * @return bool
		 */
		public function isReminderSend(): bool {
			return $this->reminderSend;
		}

		/**
		 * @param bool $reminderSend
		 */
		public function setReminderSend(bool $reminderSend): void {
			$this->reminderSend = $reminderSend;
		}

		/**
		 * @return bool
		 */
		public function isExport(): bool {
			return $this->export;
		}

		/**
		 * @param bool $export
		 */
		public function setExport(bool $export): void {
			$this->export = $export;
		}

		/**
		 * @return int
		 */
		public function getSms() : int {
			return $this->sms;
		}

		/**
		 * @param int $sms
		 */
		public function setSms( int $sms ) : void {
			$this->sms = $sms;
		}

		/**
		 * @return int
		 */
		public function getSubscriptionID() : int {
			return $this->subscriptionID;
		}

		/**
		 * @param int $subscriptionID
		 */
		public function setSubscriptionID( int $subscriptionID ) : void {
			$this->subscriptionID = $subscriptionID;
		}

	}