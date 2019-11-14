<?php


	namespace C3\C3baxi\Domain\Model;


	class Rating extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

		/**
		 * object_id
		 * @var integer
		 */
		protected $objectId;
		/**
		 * type
		 * @var string
		 */
		protected $type;
		/**
		 * value
		 * @var integer
		 */
		protected $value;
		/**
		 * comment
		 * @var string
		 */
		protected $comment;

		/**
		 * cruser_id
		 * @var int
		 */
		protected $cruserId;

		/**
		 * @var \DateTime
		 */
		protected $crdate;
		/**
		 * @return int
		 */
		public function getObjectId() : int {
			return $this->objectId;
		}

		/**
		 * @param int $objectId
		 *
		 * @return Rating
		 */
		public function setObjectId( int $objectId ) : Rating {
			$this->objectId = $objectId;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getType() : string {
			return $this->type;
		}

		/**
		 * @param string $type
		 *
		 * @return Rating
		 */
		public function setType( string $type ) : Rating {
			$this->type = $type;
			return $this;
		}

		/**
		 * @return int
		 */
		public function getValue() : int {
			return $this->value;
		}

		/**
		 * @param int $value
		 *
		 * @return Rating
		 */
		public function setValue( int $value ) : Rating {
			$this->value = $value;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getComment() : string {
			return $this->comment;
		}

		/**
		 * @param string $comment
		 *
		 * @return Rating
		 */
		public function setComment( string $comment ) : Rating {
			$this->comment = $comment;
			return $this;
		}

		/**
		 * @return int
		 */
		public function getCruserId() : int {
			return $this->cruserId;
		}

		/**
		 * @param int $cruserId
		 *
		 * @return Rating
		 */
		public function setCruserId( int $cruserId ) : Rating {
			$this->cruserId = $cruserId;
			return $this;
		}

		/**
		 * @return \DateTime
		 */
		public function getCrdate() {
			return $this->crdate;
		}

		/**
		 * @param \DateTime $crdate
		 *
		 * @return Rating
		 */
		public function setCrdate( \DateTime $crdate ) {
			$this->crdate = $crdate;
			return $this;
		}


	}