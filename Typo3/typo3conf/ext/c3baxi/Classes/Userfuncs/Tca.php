<?php
	namespace C3\C3baxi\Userfuncs;
	class Tca {

		public function getFahrtInlineLabel( &$params, $parentObject ) {
			$record = \TYPO3\CMS\Backend\Utility\BackendUtility::getRecord($params['table'], $params['row']['uid']);

			$title = $record['name'];
			$params['title'] = $title . ' Test';
		}
	}