<?php


	namespace C3\C3baxi\Helper;


	use TYPO3\CMS\Core\Utility\ArrayUtility;
	use TYPO3\CMS\Core\Utility\GeneralUtility;
	use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

	class RouteFinder
	{
		/**
		 * @var \C3\C3baxi\Domain\Model\Haltestelle $start
		 */
		protected $start = NULL;
		/**
		 * @var \C3\C3baxi\Domain\Model\Haltestelle $end
		 */
		protected $end = NULL;

		/**
		 * @var \DateTime
		 * @inject
		 */
		protected $time;

		/**
		 * @var \C3\C3baxi\Domain\Model\Linie $foundLine
		 */
		protected $foundLine = NULL;

		/**
		 * @var array
		 */
		protected $foundRoutes = [];

		protected $timeZone = null;

		/**
		 * RouteFinder constructor.
		 *
		 * @param \C3\C3baxi\Domain\Model\Haltestelle $start
		 * @param \C3\C3baxi\Domain\Model\Haltestelle $end
		 * @param \DateTime|NULL $time
		 */
		function __construct( \C3\C3baxi\Domain\Model\Haltestelle $start, \C3\C3baxi\Domain\Model\Haltestelle $end, \DateTime $time = NULL )
		{
			$this->start = $start;
			$this->end = $end;

			if ( $time )
				$this->time = $time;

			$this->timeZone = new \DateTimeZone( 'Europe/Berlin' );
		}

		/**
		 * @param \C3\C3baxi\Domain\Model\Haltestelle $start
		 * @param \C3\C3baxi\Domain\Model\Haltestelle $end
		 *
		 * @return bool|object
		 */
		public function findLine()
		{

			if ( $this->end !== NULL && $this->start !== NULL ) {

				$startLines = $this->start->getZone()->getLinien();
				$endLines = $this->end->getZone()->getLinien();
				if ( $startLines->count() == 1 && $endLines->count() == 1 ) {
					$startLine = $startLines->current();
					$endLine = $endLines->current();
					if ( $startLine == $endLine ) {
						$this->foundLine = $startLine;
					}
				}
			}
		}

		public function findRoutes()
		{

			$fahrten = array_filter(
				$this->foundLine->getFahrten()->toArray(),
				function ( $fahrt ) {

					$weekDays = ['So', 'Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa', 'So'];
					$weekday = $weekDays[$this->time->format( 'w' )];
//
					$endFound = FALSE;
					foreach ( $fahrt->getZeiten() as $zeit ) {
						// fahrt findet nicht am richtigen Wochentag statt
						if ( !in_array( $weekday, (array) $fahrt->getTage() ) ) return FALSE;

						if ( $zeit->getZone() == $this->end->getZone() ) {
							$endFound = TRUE;
						}

						// richtige richtung
						if ( $zeit->getZone() == $this->start->getZone() && !$endFound ) {
							$fahrtZeit = new \DateTime(  );
							$fahrtZeit->setTimezone( $this->timeZone );
							$fahrtZeit->setTimestamp( $zeit->getZeit() );
							$fahrtZeit->setDate( $this->time->format( 'Y' ), $this->time->format( 'm' ), $this->time->format( 'd' ) );
							$timeDiff = $this->time->diff( $fahrtZeit );

							if ( $timeDiff->format( '%R' ) === '+' ) {
								return TRUE;
							}

							return FALSE;
						} elseif ( $zeit->getZone() == $this->start->getZone() && $endFound ) {
							return FALSE;
						}
					}
				}
			);

//			if ( empty( $fahrten ) ) {
//				if ( $this->request->hasArgument( 'shift' ) ) {
//					if ( $this->request->getArgument( 'shift' ) == 'prev' )
//						$params['time'] -= (60 * 60) * 6;
//					else
//						$params['time'] += (60 * 60) * 6;
//				}
////				$params['time'] += (60*60) * 6;
////				return $this->findeFahrten( $linie, $params );
//			}


			foreach ( $fahrten as $fahrt ) {

				$tmp = [
					'uid'      => $fahrt->getUid(),
					'abfahrt'  => 0,
					'ankunft'  => 0,
					//					'preis'    => $this->getFahrtPreis( $this->start->getZone(), $this->end->getZone() ),
					'linie'    => $this->foundLine->getNr(),
					'umstieg'  => FALSE,
					'dauer'    => rand( 10, 50 ),
					'sameZone' => FALSE,
					'allowed'  => TRUE
				];


				foreach ( $fahrt->getZeiten() as $zeit ) {

					if ( $zeit->getZone() == $this->start->getZone() ) {
						$tmp['abfahrt'] = $zeit;
					} elseif ( $zeit->getZone() == $this->end->getZone() ) {
						$tmp['ankunft'] = $zeit;
					}
				}

				$tmp['abfahrt']->setZeit( $this->alterDate( $tmp['abfahrt']->getZeit(), $this->time ) );

				$tmp['ankunft']->setZeit( $this->alterDate( $tmp['ankunft']->getZeit(), $this->time ) );


				$buchbarBis = \DateTime::createFromFormat( 'U', ( $this->alterDate( $fahrt->getBuchbarBis(), $this->time ) ) );
			
				$buchbarBis->setTimezone( $this->timeZone );
//
				if ( $buchbarBis->diff( \DateTime::createFromFormat( 'U', $tmp['abfahrt']->getZeit() ) )->format( '%R' ) == '-' ) {
					$buchbarBis->modify( '-1 day' );
				}

				$fahrt->setBuchbarBis( $buchbarBis->getTimestamp() );

				$now = new \DateTime();

				if ( $buchbarBis < $now ) {
					$tmp['allowed'] = FALSE;
				}
				$tmp['dauer'] = ($tmp['ankunft']->getZeit() - $tmp['abfahrt']->getZeit()) / 60;

				$tmp['abfahrt'] = \DateTime::createFromFormat( 'U', $tmp['abfahrt']->getZeit() );
				$tmp['abfahrt']->setTimezone( $this->timeZone );
				$tmp['ankunft'] = \DateTime::createFromFormat( 'U', $tmp['ankunft']->getZeit() );
				$tmp['ankunft']->setTimezone( $this->timeZone );

				$tmp['buchbarBis'] = $buchbarBis;

				$this->foundRoutes[] = $tmp;
			}
		}

		protected function alterDate( $date, $referer )
		{

			$date = \DateTime::createFromFormat( 'U', $date );

			if( is_string( $referer ) )
				$referer = \DateTime::createFromFormat( 'U', $referer );

			$date->setDate( $referer->format( 'Y' ), $referer->format( 'm' ), $referer->format( 'd' ) );

			return (int) $date->getTimestamp();
		}

		/**
		 * @return \C3\C3baxi\Domain\Model\Linie
		 */
		public function getFoundLine() : \C3\C3baxi\Domain\Model\Linie
		{
			return $this->foundLine;
		}

		/**
		 * @return array
		 */
		public function getFoundRoutes() : array
		{
			return $this->foundRoutes;
		}

		public function getRouteCount() : int
		{
			return count( $this->foundRoutes );
		}



	}