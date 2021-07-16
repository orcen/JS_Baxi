<?php


	namespace C3\C3local\Domain\Validator;


	class PasswordValidator extends \In2code\Femanager\Domain\Validator\PasswordValidator {


		/**
		 * Validation of given Params
		 *
		 * @param $user
		 * @return bool
		 */
		public function isValid($user)
		{
			$this->init();

			// if password fields are not active or if keep function active
			if (!$this->passwordFieldsAdded() || $this->keepPasswordIfEmpty()) {
				return true;
			}

			$password = $user->getPassword();
//			$passwordRepeat = isset($this->piVars['password_repeat']) ? $this->piVars['password_repeat'] : '';
//
//			if ($password !== $passwordRepeat) {
//				$this->addError('validationErrorPasswordRepeat', 'password');
//				return false;
//			}

			return true;
		}

	}