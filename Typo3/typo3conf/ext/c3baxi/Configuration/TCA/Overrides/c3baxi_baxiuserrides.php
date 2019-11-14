<?php
	(function ( $tablename = 'tt_content', $contentType = 'c3baxi_baxiuserrides' ) {

		\TYPO3\CMS\Core\Utility\ArrayUtility::mergeRecursiveWithOverrule( $GLOBALS['TCA'][$tablename], [
			'ctrl'    => [
				'typeicon_classes' => [
					$contentType => 'c3baxi_baxiuserrides',
				],
			],
			'types'   => [
				$contentType => [
					'showitem' => implode( ',', [
						'--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general',
						'--palette--;;general, header_link, bodytext',
						'--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.appearance,--palette--;;frames,--palette--;;appearanceLinks,',
						'--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,--palette--;;language,',
						'--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                      --palette--;;hidden,
                      --palette--;;access,
                    --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:categories,
                         categories,
                    --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes,
                         rowDescription,
                    --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended,'
					] ),
				],
			],
			'columns' => [
				'bodytext'    => [
					'exclude' => TRUE,
					'label'   => 'HeaderText',
					'config'  => [
						'type'           => 'text',
						'enableRichtext' => TRUE,
						'cols'           => 60,
						'rows'           => 5,
						'eval'           => 'trim'
					],
				],
				'header_link' => [
					'exclude' => TRUE,
					'label'   => 'FAQ Link',
					'config'  => [
						'type' => 'input',
//						'eval' => 'trim,int',
						'renderType' => 'inputLink',
						//						'foreign_table' => 'tx_faq_domain_model_question',
						//						'foreign_field' => 'uid',
						//						'foreign_label' => 'question'
					]
				]
			],
		] );
	})();