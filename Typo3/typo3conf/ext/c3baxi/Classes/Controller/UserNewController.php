<?php

	namespace C3\C3baxi\Controller;

	use In2code\Femanager\Domain\Model\User;
	use C3\C3baxi\Xclass\Extbase\Mvc\Controller\Argument;
	use In2code\Femanager\Domain\Model\Log;
	use In2code\Femanager\Utility\LocalizationUtility;
	use In2code\Femanager\Utility\LogUtility;
	use TYPO3\CMS\Core\Utility\GeneralUtility;
	use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

	class UserNewController extends \In2code\Femanager\Controller\NewController {

		public function initializeCreateAction() {
			if ( $this->arguments->hasArgument( 'user' ) ) {
				// Workaround to avoid php7 warnings of wrong type hint.
				/** @var \C3\C3baxi\Xclass\Extbase\Mvc\Controller\Argument $user */
				$user = $this->arguments['user'];
				$user->setDataType( \C3\C3baxi\Domain\Model\User::class );
			}
		}

		/**
		 * action create
		 *
		 * @param In2code\Femanager\Domain\Model\User $user
		 * @validate $user In2code\Femanager\Domain\Validator\ServersideValidator
		 * @validate $user In2code\Femanager\Domain\Validator\PasswordValidator
		 *
		 * @return void
		 */
		public function createAction( $user ) {
			parent::createAction( $user );
		}

		public function silentAction() {
			return '';
		}

		public function createStatusAction() {
			$selectedFahrt = $GLOBALS['TSFE']->fe_user->getKey( 'ses', 'baxi_fahrt' );

			if ( $selectedFahrt ) {

//				$objectManager = GeneralUtility::makeInstance( \TYPO3\CMS\Extbase\Object\ObjectManager::class );
				$uriBuilder = GeneralUtility::makeInstance( \TYPO3\CMS\Extbase\Object\ObjectManager::class )->get( \TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder::class );

				$url = $uriBuilder->reset()
					->setCreateAbsoluteUri( TRUE )
					->setTargetPageUid( 13 )
					->uriFor(
						'reserve',
						[
							'fahrt' => $selectedFahrt,
						],
						'FESuche',
						'C3baxi',
						'BaxiSuche'
					);
				$this->view->assign( 'returnTo', $url );
			}
		}

		public function initializeCreateRequest() {
			if ( $this->arguments->hasArgument( 'user' ) ) {
				// Workaround to avoid php7 warnings of wrong type hint.
				/** @var \C3\C3baxi\Xclass\Extbase\Mvc\Controller\Argument $user */
				$user = $this->arguments['user'];
				$user->setDataType( \C3\C3baxi\Domain\Model\User::class );
			}
		}

		/**
		 * Postfix method to createAction(): Create must be confirmed by Admin or User
		 *
		 * @param In2code\Femanager\Domain\Model\User $user
		 *
		 * @return void
		 */
		protected function createRequest( $user ) {
			$user->setDisable( TRUE );
			$this->userRepository->add( $user );
			$this->persistenceManager->persistAll();
			LogUtility::log( Log::STATUS_PROFILECREATIONREQUEST, $user );
			if ( !empty( $this->settings['new']['confirmByUser'] ) ) {
				$this->sendCreateUserConfirmationMail( $user );
				echo json_encode( [
					'status'  => 'SUCCESS',
					'message' => LocalizationUtility::translate( 'createRequestWaitingForUserConfirm' )
				] );
				$this->response->setHeader( 'Content-type', 'application/json' );
				die();
//				$this->createUserConfirmationRequest($user);
//				$this->redirect('new');
			}
			if ( !empty( $this->settings['new']['confirmByAdmin'] ) ) {
				$this->sendCreateUserConfirmationMail( $user );
//				$this->createAdminConfirmationRequest($user);
//				$this->redirect('new');
			}
		}
	}