<?php

defined('TYPO3_MODE') or die();

$_LLL = 'LLL:EXT:cart/Resources/Private/Language/locallang_db.xlf';

return [
    'ctrl' => [
        'title' => $_LLL . ':tx_cart_domain_model_order_address',
        'label' => 'uid',
        'label_alt' => 'first_name, last_name, street, street_number, zip, city',
        'label_alt_force' => 1,
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'dividers2tabs' => true,

        'versioningWS' => 2,
        'versioning_followPages' => true,
        'origUid' => 't3_origuid',
        'hideTable' => true,
        'delete' => 'deleted',
        'enablecolumns' => [],
        'searchFields' => 'first_name, last_name, street, street_number, zip, city',
        'iconfile' => 'EXT:cart/Resources/Public/Icons/Order/Address.png'
    ],
    'hideTable' => 1,
    'interface' => [
        'showRecordFieldList' => 'title, salutation, first_name, last_name, email, company, street, zip, city, country, phone, fax',
    ],
    'types' => [
        '1' => [
            'showitem' => 'title, salutation, first_name, last_name, email, company, street, zip, city, country, phone, fax'
        ],
    ],
    'palettes' => [
        '1' => [
            'showitem' => ''
        ],
    ],
    'columns' => [
        'title' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_address.title',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'salutation' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_address.salutation',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'trim,required'
            ],
        ],
        'first_name' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_address.first_name',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'trim,required'
            ],
        ],
        'last_name' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_address.last_name',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'trim,required'
            ],
        ],
        'email' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_address.email',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'trim,required'
            ],
        ],
        'company' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_address.company',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'street' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_address.street',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'trim,required'
            ],
        ],
        'zip' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_address.zip',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'trim,required'
            ],
        ],
        'city' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_address.city',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'trim,required'
            ],
        ],
        'country' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_address.country',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'phone' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_address.phone',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'fax' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_address.fax',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
    ],
];
