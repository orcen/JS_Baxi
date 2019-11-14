<?php

$EM_CONF[$_EXTKEY] = array(
    'title' => 'C3 Local Configuration',
    'description' => 'This extension provides the full customization for this particular TYPO3 installation. ',
    'category' => 'plugin',
    'author' => 'C3 marketing agentur GmbH',
    'author_company' => 'C3 marketing agentur GmbH',
    'author_email' => 'dev@myc3.com',
    'dependencies' => 'extbase,fluid',
    'state' => 'stable',
    'clearCacheOnLoad' => '1',
    'version' => '9.5.5',
    'constraints' => array(
        'depends' => array(
            'c3base' => '9.5.5-',
        ),
    )
);