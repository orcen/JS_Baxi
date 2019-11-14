<?php


	namespace C3\C3baxi\Controller;


	use C3\C3baxi\Domain\Model\Rating;
	use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

	class RatingController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

		/**
		 * @var \C3\C3baxi\Domain\Repository\RatingRepository
		 * @inject
		 */
		protected $repository;
		/**
		 * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
		 * @inject
		 */
		protected $persistenceManager;

		public function indexAction() {

		}

		public function createAction() {
			if ( !isset( $GLOBALS["TSFE"]->fe_user->user['uid'] ) ) {
				die();
			}
			$rateValue = $this->request->getArgument( 'rating' );
			$rateValue = intval( $rateValue );

			$newRating = new Rating();
			$newRating->setObjectId( $this->request->getArgument( 'id' ) );
			$newRating->setType( $this->request->getArgument( 'type' ) );
			$newRating->setValue( $rateValue );
			$newRating->setComment( $this->request->getArgument( 'comment' ) );
			$newRating->setPid( 14 );
			$newRating->setCruserId( $GLOBALS["TSFE"]->fe_user->user['uid'] );

			$this->repository->add( $newRating );
			$this->persistenceManager->persistAll();

			if ( $this->request->getArgument( 'controller' ) == 'Ajax' ) {
				$result = [
					'status' => TRUE,
					'id'     => $newRating->getUid()
				];
				echo json_encode( $result );
				exit;
			}

			$this->redirect( 'index', 'Baxi', 'C3baxi' );
		}
	}