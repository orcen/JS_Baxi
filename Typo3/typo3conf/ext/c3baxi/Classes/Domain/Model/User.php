<?php


	namespace C3\C3baxi\Domain\Model;



	class User extends \In2code\Femanager\Domain\Model\User
	{
		/**
		 * @var string
		 */
		protected $txC3baxiFavorites;
		/**
		 * @var bool
		 */
		protected $txC3baxiNotificationTelephone;
		/**
		 * @var bool
		 */
		protected $txC3baxiNotificationEmail;

		/**
		 * @var string
		 */
		protected $ticketArt;
		/**
		 * @return string
		 */
		public function getTxC3baxiFavorites() : ?string
		{
			return $this->txC3baxiFavorites;
		}

		/**
		 * @param string $txC3baxiFavorites
		 */
		public function setTxC3baxiFavorites( string $txC3baxiFavorites ) : void
		{
			$this->txC3baxiFavorites = $txC3baxiFavorites;
		}

		/**
		 * @return string
		 */
		public function getTicketArt() : string {
			return $this->ticketArt;
		}

		/**
		 * @param string $ticketArt
		 *
		 * @return User
		 */
		public function setTicketArt( string $ticketArt ) : User {
			$this->ticketArt = $ticketArt;
			return $this;
		}

		public function getBename() {
			return $this->name . ' (' . $this->email . ')';
		}

		/**
		 * @return bool
		 */
		public function isTxC3baxiNotificationTelephone(): bool {
			return $this->txC3baxiNotificationTelephone;
		}

		/**
		 * @param bool $txC3baxiNotificationTelephone
		 */
		public function setTxC3baxiNotificationTelephone(bool $txC3baxiNotificationTelephone): void {
			$this->txC3baxiNotificationTelephone = $txC3baxiNotificationTelephone;
		}

		/**
		 * @return bool
		 */
		public function isTxC3baxiNotificationEmail(): bool {
			return $this->txC3baxiNotificationEmail;
		}

		/**
		 * @param bool $txC3baxiNotificationEmail
		 */
		public function setTxC3baxiNotificationEmail(bool $txC3baxiNotificationEmail): void {
			$this->txC3baxiNotificationEmail = $txC3baxiNotificationEmail;
		}


	}