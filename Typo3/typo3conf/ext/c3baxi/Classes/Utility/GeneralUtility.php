<?php
	/**
	 * Created by PhpStorm.
	 * User: schuessler
	 * Date: 09.01.2019
	 * Time: 12:07
	 */

	namespace C3\C3baxi\Utility;

	class GeneralUtility
	{
		/**
		 * @var \TYPO3\CMS\Extbase\Service\ImageService
		 * @inject
		 */
		protected $imageService;

		/**
		 * @param $image
		 * @param $request
		 * @param $settings
		 * @return string
		 */
		public function createProcessedImage($image, $request, $settings = null)
		{
			if ($settings == null) {
				$settings['width'] = "500";
				$settings['height'] = "333c";
			}
			$imagePath = $image->getOriginalResource()->getOriginalFile()->getPublicUrl();
			$processedImage = $this->imageService->applyProcessingInstructions(
				$this->imageService->getImage(
					$imagePath,
					null,
					false),
				[
					'width' => $settings['width'],
					'height' => $settings['height']
				]
			);
			// $imageUri = $request->getBaseUri().trim($this->imageService->getImageUri($processedImage),'/');
			$imageUri = trim($this->imageService->getImageUri($processedImage),'/');

			return $imageUri;
		}

		public function generateMediaList( $data, $request = false ) {

			if( empty($data) ) return false;
			$result = array( 'images' => array(), 'downloads' => array(), 'youtube' => array() );
			foreach ($data as $newsMedia) {

				switch ($newsMedia->getOriginalResource()->getType()) {
					case 2: //Images
						$imgUrl = $this->createProcessedImage($newsMedia, $this->request,null);
						$props = $newsMedia->getOriginalResource()->getProperties();
						$result['images'][] = array(
							'url' => $imgUrl,
							'title' => $newsMedia->getOriginalResource()->getTitle() ?: $newsMedia->getOriginalResource()->getName(),
							//						'props' => $props,
							'size' => array( $props['width'], $props['height'] )
						);
						break;

					case 4: // Youtube
						$result['youtube'][] = $newsMedia->getOriginalResource()->getContents();
						break;

					case 5:  // PDF
						if( $request )
							$imgUrl = $request->getBaseUri().trim( $newsMedia->getOriginalResource()->getPublicUrl(), '/' );
						else
							$imgUrl = $newsMedia->getOriginalResource()->getPublicUrl();

						$result['downloads'][] = array(
							'url' => $imgUrl,
							'title' => $newsMedia->getOriginalResource()->getTitle() ?: $newsMedia->getOriginalResource()->getName(),
							'size' => $newsMedia->getOriginalResource()->getSize()
						);
						break;
				}

			}

			return $result;
		}

		static function combineDate( \DateTime $time, \DateTime $date) {
			return $time->setDate( $date->format( 'Y'), $date->format('m'),$date->format( 'd') );
		}
	}