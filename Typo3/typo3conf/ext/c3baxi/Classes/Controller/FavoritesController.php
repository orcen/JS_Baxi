<?php


	namespace C3\C3baxi\Controller;


	use C3\C3baxi\Domain\Model\Haltestelle;
	use TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository;
	use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

	class FavoritesController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

		/**
		 * @var \C3\C3baxi\Domain\Repository\HaltestelleRepository
		 * @inject
		 */
		protected $haltestellenRepository = NULL;

		/**
		 * @var \C3\C3baxi\Domain\Repository\UserRepository
		 * @inject
		 */
		protected $frontendUserRepository;

		protected function listAction() {
			$this->initiateJSSettings();
//			$pageRender = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance( \TYPO3\CMS\Core\Page\PageRenderer::class );
//			$baxiSettings = [
//				'ticketType'       => 'adult',
//				'loggedIn' => isset($GLOBALS["TSFE"]->fe_user->user['uid']),
//				'ajaxAutocomplete' => [
//					'haltestelle' => $this->uriBuilder->reset()
//						->setCreateAbsoluteUri( TRUE )
//						->setTargetPageUid( 1 )
//						->setTargetPageType( 666 )
//						->uriFor( 'autocomplete', ['type' => 'haltestelle'], 'Ajax', 'C3baxi', 'BaxiSuche' ),
//					'station'     => $this->uriBuilder->reset()
//						->setCreateAbsoluteUri( TRUE )
//						->setTargetPageUid( 1 )
//						->setTargetPageType( 666 )
//						->uriFor( 'stationDetail', [], 'Ajax', 'C3baxi', 'BaxiSuche' ),
//					'addFavorite' => $this->uriBuilder->reset()
//						->setCreateAbsoluteUri( TRUE )
//						->setTargetPageUid( 1 )
//						->setTargetPageType( 666 )
//						->uriFor( 'addFavorite', [], 'Ajax', 'C3baxi', 'BaxiSuche' ),
//					'removeFavorite' => $this->uriBuilder->reset()
//						->setCreateAbsoluteUri( TRUE )
//						->setTargetPageUid( 1 )
//						->setTargetPageType( 666 )
//						->uriFor( 'removeFavorite', [], 'Ajax', 'C3baxi', 'BaxiSuche' ),
//				]
//			];

			$uuid = $GLOBALS["TSFE"]->fe_user->user['uid'];
			if ( !$uuid ) return FALSE;
			$user = $this->frontendUserRepository->findByUid( $uuid );

			$favorites = $user->getTxC3baxiFavorites();

			if ( $favorites == '' ) $favorites = [];
			else $favorites = json_decode( $favorites );

			array_walk( $favorites, function ( $fav ) {
				$station = $this->haltestellenRepository->findByUid( $fav->station );
				if ( $station instanceof Haltestelle ) {
					$fav->station = $station;
				} else {
					$fav->station = FALSE;
//					unset( $fav );
					/** ToDo: delete Favorite */
				}
			} );

			$favorites = array_filter( $favorites, function ( $fav ) {
				return $fav->station;
			} );


			$this->view->assign( 'favorites', $favorites );
			$this->view->assign( 'station', $this->haltestellenRepository->findByUid( 1 ) );
			$this->view->assign( 'canAddFavorite', count( $favorites ) < 5 );
//			$pageRender->addJsFooterInlineCode( 'baxiSearchSettings', 'var baxiSearchSettings = ' . json_encode( $baxiSettings ) );
		}

		protected function addAction() {
			$uuid = $GLOBALS["TSFE"]->fe_user->user['uid'];
			$user = $this->frontendUserRepository->findByUid( $uuid );

			return json_encode( ['status' => 'success'] );
		}

		protected function initiateJSSettings() {
			$pageRender = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance( \TYPO3\CMS\Core\Page\PageRenderer::class );
			$this->uriBuilder->reset()
				->setCreateAbsoluteUri( TRUE )
				->setTargetPageUid( 1 )
				->setTargetPageType( 666 );

			$favorites = [];


			$uuid = $GLOBALS["TSFE"]->fe_user->user['uid'];

			if ( $uuid ) {
				$user = $this->frontendUserRepository->findByUid( $uuid );
				$favorites = $user->getTxC3baxiFavorites();
			}
			$baxiSettings = [
				'ticketType' => 'adult',
				'loggedIn'   => isset( $GLOBALS["TSFE"]->fe_user->user['uid'] ),
				'favorites'  => $favorites,
				'ajaxUrl'    => [
					'haltestelle'    => $this->uriBuilder->setArguments( [] )
						->uriFor( 'autocomplete', ['type' => 'haltestelle'], 'Ajax', 'C3baxi', 'BaxiSuche' ),
					'station'        => $this->uriBuilder->setArguments( [] )
						->uriFor( 'stationDetail', [], 'Ajax', 'C3baxi', 'BaxiSuche' ),
					'favorites'      => $this->uriBuilder->setArguments( [] )
						->uriFor( 'favorites', ['doAction' => 'findAll'], 'Ajax', 'C3baxi', 'BaxiSuche' ),
					'help'           => $this->uriBuilder->setArguments( [] )
						->uriFor( 'help', [], 'Ajax', 'C3baxi', 'BaxiSuche' ),
					'rating'         => $this->uriBuilder->setArguments( [] )
						->uriFor( 'rating', [], 'Ajax', 'C3baxi', 'BaxiSuche' ),
					/** deprecated */
					'addFavorite'    => $this->uriBuilder->setArguments( [] )
						->uriFor( 'addFavorite', [], 'Ajax', 'C3baxi', 'BaxiSuche' ),
					'removeFavorite' => $this->uriBuilder->setArguments( [] )
						->uriFor( 'removeFavorite', [], 'Ajax', 'C3baxi', 'BaxiSuche' ),
				],
				'mapCenter'  => [ // default is Tirschenreuth, Germany
				                  'lat' => 49.8817161,
				                  'lng' => 12.3303441
				]
			];
			$pageRender->addJsFooterInlineCode( 'baxiSearchSettings', 'var baxiSearchSettings = ' . json_encode( $baxiSettings ) );
		}

	}