<?php


	namespace C3\C3baxi\Domain\Model;


	use TYPO3\CMS\Beuser\Domain\Model\BackendUser;

	class Company extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
	{
		/**
		 * @var string
		 */
		protected $name = '';

		/**
		 * @var string
		 */
		protected $info = '';

		/**
		 * @var int
		 */
		protected $carCount = 0;

		/**
		 * @var string
		 */
		protected $street = '';
		/**
		 * @var string
		 */
		protected $city = '';
		/**
		 * @var string
		 */
		protected $zip;
		/**
		 * @var string
		 */
		protected $telefon = '';
		/**
		 * @var string
		 */
		protected $email = '';
		/**
		 * @var string
		 */
		protected $contactPerson = '';

		/**
		 * @var float
		 */
		protected $flatrateBase = 10;

		/**
		 * @var float
		 */
		protected $flatrateExtra = 1.4;

		/**
		 * @var \TYPO3\CMS\Beuser\Domain\Model\BackendUser
		 */
		protected $user;


		public function __construct()
		{
			$this->routes = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage;
		}


		/**
		 * @return \TYPO3\CMS\Beuser\Domain\Model\BackendUser
		 */
		public function getUser() {
			return $this->user;
		}

		/**
		 * @param \TYPO3\CMS\Beuser\Domain\Model\BackendUser $user
		 *
		 * @return Company
		 */
		public function setUser( \TYPO3\CMS\Beuser\Domain\Model\BackendUser $user ) : Company {
			$this->user = $user;
			return $this;
		}

		/**
		 * routes
		 *
		 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\C3\C3baxi\Domain\Model\Linie>
		 */
		protected $routes = null;

		/**
		 * @return string
		 */
		public function getName() : string
		{
			return $this->name;
		}

		/**
		 * @param string $name
		 *
		 * @return Company
		 */
		public function setName( string $name ) : Company
		{
			$this->name = $name;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getInfo() : string
		{
			return $this->info;
		}

		/**
		 * @param string $info
		 *
		 * @return Company
		 */
		public function setInfo( string $info ) : Company
		{
			$this->info = $info;
			return $this;
		}

		/**
		 * @return int
		 */
		public function getCarCount() : int
		{
			return $this->carCount;
		}

		/**
		 * @param int $carCount
		 *
		 * @return Company
		 */
		public function setCarCount( int $carCount ) : Company
		{
			$this->carCount = $carCount;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getStreet() : string
		{
			return $this->street;
		}

		/**
		 * @param string $street
		 *
		 * @return Company
		 */
		public function setStreet( string $street ) : Company
		{
			$this->street = $street;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getCity() : string
		{
			return $this->city;
		}

		/**
		 * @param string $city
		 *
		 * @return Company
		 */
		public function setCity( string $city ) : Company
		{
			$this->city = $city;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getZip() : ?string
		{
			return $this->zip;
		}

		/**
		 * @param string $zip
		 *
		 * @return Company
		 */
		public function setZip( string $zip ) : Company
		{
			$this->zip = $zip;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getTelefon() : string
		{
			return $this->telefon;
		}

		/**
		 * @param string $telefon
		 *
		 * @return Company
		 */
		public function setTelefon( string $telefon ) : Company
		{
			$this->telefon = $telefon;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getEmail() : string
		{
			return $this->email;
		}

		/**
		 * @param string $email
		 *
		 * @return Company
		 */
		public function setEmail( string $email ) : Company
		{
			$this->email = $email;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getContactPerson() : string
		{
			return $this->contactPerson;
		}

		/**
		 * @param string $contactPerson
		 *
		 * @return Company
		 */
		public function setContactPerson( string $contactPerson ) : Company
		{
			$this->contactPerson = $contactPerson;
			return $this;
		}

		/**
		 * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage
		 */
		public function getRoutes(): ?\TYPO3\CMS\Extbase\Persistence\ObjectStorage {
			return $this->routes;
		}

		/**
		 * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $routes
		 * @return Company
		 */
		public function setRoutes(?\TYPO3\CMS\Extbase\Persistence\ObjectStorage $routes): Company {
			$this->routes = $routes;
			return $this;
		}

		/**
		 * @param \C3\C3baxi\Domain\Model\Linie $route
		 */
		public function addRoute( Linie $route ) : Company
		{
			$this->routes->attach( $route );
			return $this;
		}

		/**
		 * @param \C3\C3baxi\Domain\Model\Linie $route
		 */
		public function removeRoute( Linie $route ) : Company
		{
			$this->routes->detach( $route );
			return $this;
		}

		/**
		 * @return float
		 */
		public function getFlatrateBase(): float {
			return $this->flatrateBase;
		}

		/**
		 * @param float $flatrateBase
		 * @return Company
		 */
		public function setFlatrateBase(float $flatrateBase): Company {
			$this->flatrateBase = $flatrateBase;
			return $this;
		}

		/**
		 * @return float
		 */
		public function getFlatrateExtra(): float {
			return $this->flatrateExtra;
		}

		/**
		 * @param float $flatrateExtra
		 * @return Company
		 */
		public function setFlatrateExtra(float $flatrateExtra): Company {
			$this->flatrateExtra = $flatrateExtra;
			return $this;
		}


	}