<?php


	namespace C3\C3baxi\ViewHelpers;


	use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
	use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

	class StationViewHelper extends AbstractViewHelper
	{
		protected $escapeOutput = FALSE;
		public function initializeArguments()
		{
			parent::initializeArguments(); // TODO: Change the autogenerated stub
			$this->registerArgument( 'station', 'object', 'Object \Haltestelle', true);
			$this->registerArgument( 'hideZone', 'boolean', 'Object \Haltestelle', false, false );
		}

		public static function renderStatic( array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext ) {
			$result = $arguments['station']->getName();

			if( ! $arguments['hideZone'] ) {
				$result .= ' <small>'. $arguments['station']->getZone()->getName().'</small>';
			}
			return $result;
		}
	}