<?php


	namespace C3\C3baxi\Controller;


	class BaxiuserController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

		/**
		 * @var \C3\C3baxi\Domain\Repository\UserRepository
		 * @inject
		 */
		protected $frontendUserRepository;

		public function indexAction() {


			$uuid = $GLOBALS["TSFE"]->fe_user->user['uid'];
			$user = $this->frontendUserRepository->findByUid( $uuid );

			$this->view->assign( 'user', $user );
			$this->view->assign( 'infoText', $this->settings['text'] );
			$links = [];
			if( isset( $this->settings['pages'] ) ) {
				$links = $this->settings['pages'];
			}

			$this->view->assign( 'links', $links );
		}
	}