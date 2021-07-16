<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:c3baxi/Resources/Private/Language/locallang_db.xlf:tx_c3baxi_domain_model_company',
        'label' => 'name',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'versioningWS' => true,
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => 'name',
        'iconfile' => 'EXT:c3baxi/Resources/Public/Icons/tx_c3baxi_domain_model_company.png'
    ],
    'interface' => [
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, name',
    ],
    'types' => [
        '1' => ['showitem' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, name, telefon,email,contact_person, user'
        . '--palette--;LLL:EXT:c3baxi/Resources/Private/Language/locallang_db.xlf:tx_c3baxi_domain_model_zone.address;address,street,city,ort,zip,routes, --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, starttime, endtime'],
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'special' => 'languages',
                'items' => [
                    [
                        'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.allLanguages',
                        -1,
                        'flags-multiple'
                    ]
                ],
                'default' => 0,
            ],
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'default' => 0,
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_c3baxi_domain_model_zone',
                'foreign_table_where' => 'AND {#tx_c3baxi_domain_model_zone}.{#pid}=###CURRENT_PID### AND {#tx_c3baxi_domain_model_zone}.{#sys_language_uid} IN (-1,0)',
            ],
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        't3ver_label' => [
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.versionLabel',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255,
            ],
        ],
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.visible',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [
                    [
                        0 => '',
                        1 => '',
                        'invertStateDisplay' => true
                    ]
                ],
            ],
        ],
        'starttime' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'datetime,int',
                'default' => 0,
                'behaviour' => [
                    'allowLanguageSynchronization' => true
                ]
            ],
        ],
        'endtime' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'datetime,int',
                'default' => 0,
                'range' => [
                    'upper' => mktime(0, 0, 0, 1, 1, 2038)
                ],
                'behaviour' => [
                    'allowLanguageSynchronization' => true
                ]
            ],
        ],

        'name' => [
            'exclude' => true,
            'label' => 'LLL:EXT:c3baxi/Resources/Private/Language/locallang_db.xlf:tx_c3baxi_domain_model_zone.name',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required'
            ],
        ],
        'info' => [
	        'exclude' => true,
	        'label' => 'LLL:EXT:c3baxi/Resources/Private/Language/locallang_db.xlf:tx_c3baxi_domain_model_company.name',
	        'config' => [
		        'type' => 'input',
		        'size' => 30,
		        'eval' => 'trim,required'
	        ],
        ],
        'car_count' => [
	        'exclude' => true,
	        'label' => 'LLL:EXT:c3baxi/Resources/Private/Language/locallang_db.xlf:tx_c3baxi_domain_model_company.name',
	        'config' => [
		        'type' => 'input',
		        'size' => 4,
		        'eval' => 'trim,int'
	        ],
        ],
		'street' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:c3baxi/Resources/Private/Language/locallang_db.xlf:tx_c3baxi_domain_model_company.street',
			'config' => [
				'type' => 'input',
				'size' => 30,
				'trim' => 'trim'
			],
		],
		'city' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:c3baxi/Resources/Private/Language/locallang_db.xlf:tx_c3baxi_domain_model_company.city',
			'config' => [
				'type' => 'input',
				'size' => 30,
				'trim' => 'trim'
			],
		],
		'zip' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:c3baxi/Resources/Private/Language/locallang_db.xlf:tx_c3baxi_domain_model_company.zip',
			'config' => [
				'type' => 'input',
				'size' => 30,
				'trim' => 'trim'
			],
		],
		'telefon' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:c3baxi/Resources/Private/Language/locallang_db.xlf:tx_c3baxi_domain_model_company.telefon',
			'config' => [
				'type' => 'input',
				'size' => 30,
				'trim' => 'trim'
			],
		],
		'email' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:c3baxi/Resources/Private/Language/locallang_db.xlf:tx_c3baxi_domain_model_company.email',
			'config' => [
				'type' => 'input',
				'size' => 30,
				'trim' => 'trim'
			],
		],
		'contact_person' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:c3baxi/Resources/Private/Language/locallang_db.xlf:tx_c3baxi_domain_model_company.contact_person',
			'config' => [
				'type' => 'input',
				'size' => 30,
				'trim' => 'trim'
			],
		],
	    'flatrate_base' => [
		    'exclude' => 0,
		    'label' => 'Flatrate Base',
		    'config' => [
			    'type' => 'input',
			    'size' => 30,
			    'eval' => 'double2'
		    ],
	    ],
	    'flatrate_extra' => [
		    'exclude' => 0,
		    'label' => 'Flatrate Extra',
		    'config' => [
			    'type' => 'input',
			    'size' => 30,
			    'eval' => 'double2'
		    ],
	    ],
        'user' => [
        	'exclude' => 1,
	        'label' => 'LLL:EXT:c3baxi/Resources/Private/Language/locallang_db.xlf:tx_c3baxi_domain_model_company.user',
	        'config' => [
	        	'type' => 'select',
		        'renderType' => 'selectSingle',
		        'items' => [],
		        'foreign_table' => 'be_users',
		        'foreign_where' => 'AND usergroup = 1'
	        ]
        ],
        'routes' => [
        	'exclude' => 1,
	        'label' => 'LLL:EXT:c3baxi/Resources/Private/Language/locallang_db.xlf:tx_c3baxi_domain_model_company.routes',
	        'config' =>[
        	    'type' => 'inline',
	            'foreign_table' => 'tx_c3baxi_domain_model_linie',
	            'foreign_field' => 'company'
	        ]
        ]
    ],
];
