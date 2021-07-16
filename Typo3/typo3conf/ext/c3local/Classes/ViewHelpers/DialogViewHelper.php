<?php


	namespace C3\Local\ViewHelpers;

	use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
	use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
	use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

	class DialogViewHelper extends AbstractViewHelper {

		protected $escapeOutput = FALSE;

		public function initializeArguments() {
			$this->registerArgument( 'id', 'string', 'ElementID' );
			$this->registerArgument( 'title', 'string', 'Title' );
			$this->registerArgument( 'auto', 'boolean', '' );

		}

		public static function renderStatic( array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext ) {
			ob_start();
			$id = isset( $arguments['id'] ) ? $arguments['id'] : FALSE;

			echo '<div class="js-modal" style="display: none;"' . ($id ? ' id="' . $id . '"' : NULL) . '>'
				. $renderChildrenClosure()
				. '</div>';
			return ob_get_clean();
		}
	}