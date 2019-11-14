<?php


	namespace C3\C3baxi\Domain\Model;



	class User extends \In2code\Femanager\Domain\Model\User
	{
		/**
		 * @var string
		 */
		protected $txC3baxiFavorites;

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


	}