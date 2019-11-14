<?php
	return [
		'ctrl'      => [
			'title'                    => 'LLL:EXT:c3baxi/Resources/Private/Language/locallang_db.xlf:tx_c3baxi_domain_model_booking',
			'label'                    => 'date',
			'tstamp'                   => 'tstamp',
			'crdate'                   => 'crdate',
			'cruser_id'                => 'cruser_id',
			'versioningWS'             => TRUE,
			'languageField'            => 'sys_language_uid',
			'transOrigPointerField'    => 'l10n_parent',
			'transOrigDiffSourceField' => 'l10n_diffsource',
			'delete'                   => 'deleted',
			'enablecolumns'            => [
				'disabled'  => 'hidden',
				'starttime' => 'starttime',
				'endtime'   => 'endtime',
			],
			'searchFields'             => 'date,user,start_station,end_station',
			'iconfile'                 => 'EXT:c3baxi/Resources/Public/Icons/Auto@2x.png'
		],
		'interface' => [
			'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, fahrt, adults, children, user, date, start_station, end_station',
		],
		'types'     => [
			'1' => ['showitem' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, fahrt, adults, children, user, date, start_station, end_station, --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, starttime, endtime'],
		],
		'columns'   => [
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
					'foreign_table'       => 'tx_c3baxi_domain_model_booking',
					'foreign_table_where' => 'AND {#tx_c3baxi_domain_model_booking}.{#pid}=###CURRENT_PID### AND {#tx_c3baxi_domain_model_booking}.{#sys_language_uid} IN (-1,0)',
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
			'deleted' => [
				'config' => [
					'type' => 'passthrough'
				]
			],

			'starttime' => [
				'exclude' => TRUE,
				'label'   => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
				'config'  => [
					'type'       => 'input',
					'renderType' => 'inputDateTime',
					'eval'       => 'datetime,int',
					'default'    => 0,
					'behaviour'  => [
						'allowLanguageSynchronization' => TRUE
					]
				],
			],
			'endtime'   => [
				'exclude' => TRUE,
				'label'   => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
				'config'  => [
					'type'       => 'input',
					'renderType' => 'inputDateTime',
					'eval'       => 'datetime,int',
					'default'    => 0,
					'range'      => [
						'upper' => mktime( 0, 0, 0, 1, 1, 2038 )
					],
					'behaviour'  => [
						'allowLanguageSynchronization' => TRUE
					]
				],
			],

			'adults'        => [
				'exclude' => TRUE,
				'label'   => 'LLL:EXT:c3baxi/Resources/Private/Language/locallang_db.xlf:tx_c3baxi_domain_model_booking.adults',
				'config'  => [
					'type' => 'input',
					'size' => 30,
					'eval' => 'trim,int',
					'range' => ['lower'=>0,'upper'=>10],
					'default' => 1
				]
			],
			'children'      => [
				'exclude' => TRUE,
				'label'   => 'LLL:EXT:c3baxi/Resources/Private/Language/locallang_db.xlf:tx_c3baxi_domain_model_booking.children',
				'config'  => [
					'type' => 'input',
					'size' => 30,
					'eval' => 'trim,int',
					'range' => ['lower'=>0,'upper'=>10],
					'default' => 0
				]
			],
			'fahrt'         => [
				'exclude' => TRUE,
				'label'   => 'LLL:EXT:c3baxi/Resources/Private/Language/locallang_db.xlf:tx_c3baxi_domain_model_booking.fahrt',
				'config'  => [
					'type'          => 'select',
					'renderType'    => 'selectSingle',
					'foreign_table' => 'tx_c3baxi_domain_model_fahrt',
//					'foreign_table_where' => 'tx_c3baxi_domain_model_fahrt.pid = 0'
				]
			],
			'user'          => [
				'exclude' => TRUE,
				'label'   => 'LLL:EXT:c3baxi/Resources/Private/Language/locallang_db.xlf:tx_c3baxi_domain_model_booking.user',
				'config'  => [
					'type'          => 'select',
					'renderType'    => 'selectSingle',
					'foreign_table' => 'fe_users',
					'minitems'      => 0,
					'maxitems'      => 1,
				]
			],
			'date'          => [
				'exclude' => TRUE,
				'label'   => 'LLL:EXT:c3baxi/Resources/Private/Language/locallang_db.xlf:tx_c3baxi_domain_model_booking.date',
				'config'  => [
					'type'       => 'input',
					'renderType' => 'inputDateTime',
					'size'       => 13,
					'max' => 20,
					'eval'       => 'datetime',
//					'default'    => time()
				]
			],
			'start_station' => [
				'exclude' => TRUE,
				'label'   => 'LLL:EXT:c3baxi/Resources/Private/Language/locallang_db.xlf:tx_c3baxi_domain_model_booking.start_station',
				'config'  => [
					'type'          => 'select',
					'renderType'    => 'selectSingle',
					'foreign_table' => 'tx_c3baxi_domain_model_haltestelle',
					'foreign_table_where' => ' AND zone != 0',
					'minitems'      => 0,
					'maxitems'      => 1,
				]
			],

			'end_station'   => [
				'exclude' => TRUE,
				'label'   => 'LLL:EXT:c3baxi/Resources/Private/Language/locallang_db.xlf:tx_c3baxi_domain_model_booking.end_station',
				'config'  => [
					'type'          => 'select',
					'renderType'    => 'selectSingle',
					'foreign_table' => 'tx_c3baxi_domain_model_haltestelle',
					'foreign_table_where' => ' AND zone != 0',
//					'foreign_table_field' => 'name',
//					'minitems'      => 0,
//					'maxitems'      => 1,
				]
			],

			/*
					'name' => [
						'exclude' => true,
						'label' => 'LLL:EXT:c3baxi/Resources/Private/Language/locallang_db.xlf:tx_c3baxi_domain_model_booking.name',
						'config' => [
							'type' => 'input',
							'size' => 30,
							'eval' => 'trim'
						],
					],
					'linie' => [
						'exclude' => true,
						'label' => 'LLL:EXT:c3baxi/Resources/Private/Language/locallang_db.xlf:tx_c3baxi_domain_model_booking.linie',
						'config' => [
							'type' => 'select',
							'renderType' => 'selectSingle',
							'foreign_table' => 'tx_c3baxi_domain_model_linie',
							'minitems' => 0,
							'maxitems' => 1,
						],
					],
					'zeiten' => [
						'exclude' => true,
						'config' => [
							'type' => 'inline',
							'foreign_table' => 'tx_c3baxi_domain_model_fahrtzeit'
						]
					],
					'buchbar_bis' => [
						'exclude' => true,
						'label' => 'LLL:EXT:c3baxi/Resources/Private/Language/locallang_db.xlf:tx_c3baxi_domain_model_booking.zeit',
						'config' => [
							'type' => 'input',
							'renderType' => 'inputDateTime',
							'size' => 4,
							'eval' => 'time',
							'default' => time()
						]
					],
					'tage' => [
						'exclude' => true,
						'config' => [
							'type' => 'check',
							'items' => [
								['Mo', ''],
								['Di', ''],
								['Mi', ''],
								['Do', ''],
								['Fr', ''],
								['Sa', ''],
								['So', ''],
							],
							'cols' => 7
						]
					]*/

		],
	];
