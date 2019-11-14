<?php
	return [
		'ctrl'      => [
			'title'                    => 'LLL:EXT:c3baxi/Resources/Private/Language/locallang_db.xlf:tx_c3baxi_domain_model_rating',
			'label'                    => 'crdate',
			'tstamp'                   => 'tstamp',
			'crdate'                   => 'crdate',
			'cruser_id'                => 'cruser_id',
			'versioningWS'             => TRUE,
			'languageField'            => 'sys_language_uid',
			'transOrigPointerField'    => 'l10_parent',
			'transOrigDiffSourceField' => 'l10n_diffsource',
			'delete'                   => 'deleted',
			'enablecolumns'            => [
				'crdate' => 'crdate'
			],
			'searchFields'             => '',
			'iconfile'                 => 'EXT:c3baxi/Resources/Public/Icons/tx_c3baxi_domain_model_rating.gif'
		],
		'interface' => [
			'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, name',
		],
		'types'     => [
			1 => ['showitem' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, type, value, comment']
		],
		'columns'   => [
			'crdate' => [
				'config' => [
					'type' => 'passthrough'
				]
			],
			'sys_language_uid' => [
				'exclude' => TRUE,
				'label'   => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
				'config'  => [
					'type'       => 'select',
					'renderType' => 'selectSingle',
					'special'    => 'languages',
					'items'      => [
						[
							'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.allLanguages',
							-1,
							'flags-multiple'
						]
					],
					'default'    => 0,
				],
			],
			'l10n_parent'      => [
				'displayCond' => 'FIELD:sys_language_uid:>:0',
				'exclude'     => TRUE,
				'label'       => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
				'config'      => [
					'type'                => 'select',
					'renderType'          => 'selectSingle',
					'default'             => 0,
					'items'               => [
						['', 0],
					],
					'foreign_table'       => 'tx_c3baxi_domain_model_zone',
					'foreign_table_where' => 'AND {#tx_c3baxi_domain_model_zone}.{#pid}=###CURRENT_PID### AND {#tx_c3baxi_domain_model_zone}.{#sys_language_uid} IN (-1,0)',
				],
			],
			'l10n_diffsource'  => [
				'config' => [
					'type' => 'passthrough',
				],
			],
			't3ver_label'      => [
				'label'  => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.versionLabel',
				'config' => [
					'type' => 'input',
					'size' => 30,
					'max'  => 255,
				],
			],
			'hidden'           => [
				'exclude' => TRUE,
				'label'   => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.visible',
				'config'  => [
					'type'       => 'check',
					'renderType' => 'checkboxToggle',
					'items'      => [
						[
							0                    => '',
							1                    => '',
							'invertStateDisplay' => TRUE
						]
					],
				],
			],
			'cruser_id'        => [
				'exclude' => 0,
				'label'   => 'cruser',
				'config'  => [
					'type'          => 'select',
					'foreign_table' => 'fe_users',
					'foreign_class' => '\TYPO3\CMS\Extbase\Domain\Model\FrontendUser',
					'maxitems'      => 1
				]
			],
			'object_id'        => [
				'exclude' => FALSE,
				'label'   => '',
				'config'  => [
					'type'     => 'input',
					'eval'     => 'trim,int',
					'size'     => 4,
					'readOnly' => TRUE,
				]
			],
			'type'             => [
				'exclude' => FALSE,
				'label'   => '',
				'config'  => [
					'type'       => 'select',
					'renderType' => 'selectSingleBox',
					'items'      => [
						['Booking', 'booking']
					]
				]
			],
			'value'            => [
				'exclude' => FALSE,
				'label'   => '',
				'config'  => [
					'type'    => 'input',
					'size'    => 10,
					'eval'    => 'trim,int',
					'range'   => [
						'lower' => 0,
						'upper' => 100
					],
					'default' => 0,
					'slider'  => [
						'step'  => 1,
						'width' => 200
					]
				]
			],
			'comment'          => [
				'exclude' => FALSE,
				'label'   => '',
				'config'  => [
					'type' => 'text',
					'eval' => 'trim',
					'cols' => 40,
					'rows' => 5
				]
			]
		]
	];
