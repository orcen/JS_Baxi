<?php


	namespace C3\C3baxi\ViewHelpers;


	use TYPO3\CMS\Core\Utility\GeneralUtility;
	use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
	use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
	use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

	class RatingViewHelper extends AbstractViewHelper {

		protected $escapeOutput = FALSE;

		function initializeArguments() {
//			$this->registerArgument( '$name', $type, $description)
			parent::initializeArguments();
		}

		public static function renderStatic( array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext ) {

			$objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance( 'TYPO3\\CMS\\Extbase\\Object\\ObjectManager' );
			$ratings = $objectManager->get( 'C3\\C3baxi\\Domain\\Repository\\RatingRepository' );
			$bookings = $objectManager->get( 'C3\\C3baxi\\Domain\\Repository\\BookingRepository' );

			$data = $ratings->findAll();
			ob_start();
			$graphData = [];
			/**
			 * @ToDo: key auf das richtige Object verlinken.
			 * Ist momentan zur Buchung, muss aber mehr zur Fahrt am Tag zeigen sein.
			 *      Also gruppieren nach Datum/Linie/Fahrt
			 */
			foreach ( $data as $rate ) {
				$booking = $bookings->findByUid( $rate->getObjectId() );
				$rid = $booking->getDate()->format( 'Y-m-d' )
					. '_' . $booking->getFahrt()->getLinie()->getNr()
					. '_' . $booking->getFahrt()->getUid();
				if ( !isset( $graphData[$rid] ) ) {
					$graphData[$rid] = [
						'value' => 0,
						'count' => 0,
						'list'  => [],
						'title' => ''
					];
				}

				$graphData[$rid]['value'] = 0; //+= $rate->getValue();
				$graphData[$rid]['count']++;
				$graphData[$rid]['list'][] = $rate->getValue();
			}

			array_walk( $graphData, function ( &$data, $idx ) {
				$data['value'] = array_sum( $data['list'] ) / $data['count'];
				return $data;
			} );

			echo '<script type="text/javascript">';
			echo 'var graphData = ' . json_encode( $graphData ) . ';';
			echo '</script>';

			echo '<div id="rating"></div>';

			return ob_get_clean();
		}
	}