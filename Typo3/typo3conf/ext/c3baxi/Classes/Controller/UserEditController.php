<?php


	namespace C3\C3baxi\Controller;


	use In2code\Femanager\Domain\Model\User;
//	use In2code\Femanager\Domain\Model\Log;
//	use In2code\Femanager\Utility\LocalizationUtility;
//	use In2code\Femanager\Utility\LogUtility;
//	use TYPO3\CMS\Core\Utility\GeneralUtility;

	class UserEditController extends \In2code\Femanager\Controller\EditController {
		/**
		 * action create
		 *
		 * @param In2code\Femanager\Domain\Model\User $user
		 * @validate $user In2code\Femanager\Domain\Validator\ServersideValidator
		 * @validate $user In2code\Femanager\Domain\Validator\PasswordValidator
		 * @return void
		 */
		public function updateAction( User $user) {
			parent::updateAction($user);
		}

		public function initializeUpdateAction() {
			if ( $this->arguments->hasArgument( 'user' ) ) {
				// Workaround to avoid php7 warnings of wrong type hint.
				/** @var \C3\C3baxi\Xclass\Extbase\Mvc\Controller\Argument $user */
				$user = $this->arguments['user'];
				$user->setDataType( \C3\C3baxi\Domain\Model\User::class );
			}
		}
	}