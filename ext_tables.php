<?php

defined('TYPO3_MODE') or die();

$extPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY);
$extRelPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY);

$tcaPath = $extPath . 'Configuration/TCA/';
$iconPath = 'EXT:' . $_EXTKEY . '/Resources/Public/Icons/';

$_LLL = 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_db.xlf';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    $_EXTKEY,
    'Configuration/TypoScript',
    'Shopping Cart - Default Configuration'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    $_EXTKEY,
    'Configuration/TypoScript/Bootstrap',
    'Shopping Cart - Bootstrap Template'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    $_EXTKEY,
    'Configuration/TypoScript/Foundation',
    'Shopping Cart - Foundation Template'
);

/**
 * Register Frontend Plugins
 */

$pluginNames = [
    'MiniCart',
    'Cart',
    'Product',
    'Order'
];

foreach ($pluginNames as $pluginName) {
    $pluginSignature = strtolower(str_replace('_', '', $_EXTKEY)) . '_' . strtolower($pluginName);
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
        'Extcode.' . $_EXTKEY,
        $pluginName,
        $_LLL . ':tx_cart.plugin.' . lcfirst($pluginName)
    );
    $flexFormPath = 'EXT:' . $_EXTKEY . '/Configuration/FlexForms/' . $pluginName . 'Plugin.xml';
    if (file_exists(\TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($flexFormPath))) {
        $TCA['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
            $pluginSignature,
            'FILE:' . $flexFormPath
        );
    }
}

/**
 * Register Backend Modules
 */
if (TYPO3_MODE === 'BE') {
    if (!isset($TBE_MODULES['Cart'])) {
        $temp_TBE_MODULES = [];
        foreach ($TBE_MODULES as $key => $val) {
            if ($key == 'web') {
                $temp_TBE_MODULES[$key] = $val;
                $temp_TBE_MODULES['Cart'] = '';
            } else {
                $temp_TBE_MODULES[$key] = $val;
            }
        }

        $TBE_MODULES = $temp_TBE_MODULES;
    }

    // add Main Module
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'Extcode.' . $_EXTKEY,
        'Cart',
        '',
        '',
        [],
        [
            'access' => 'user, group',
            'icon' => $iconPath . 'module.' . (\TYPO3\CMS\Core\Utility\GeneralUtility::compat_version('7.0') ? 'svg' : 'gif'),
            'labels' => $_LLL . ':tx_cart.module.main',
            'navigationComponentId' => 'typo3-pagetree',
        ]
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'Extcode.' . $_EXTKEY,
        'Cart',
        'Orders',
        '',
        [
            'Order' => 'list, export, show, edit, update, generateInvoiceNumber, generateInvoiceDocument, downloadInvoiceDocument',
        ],
        [
            'access' => 'user, group',
            'icon' => $iconPath . 'module_orders.' . (\TYPO3\CMS\Core\Utility\GeneralUtility::compat_version('7.0') ? 'svg' : 'png'),
            'labels' => $_LLL . ':tx_cart.module.orders',
            'navigationComponentId' => 'typo3-pagetree',
        ]
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'Extcode.' . $_EXTKEY,
        'Cart',
        'OrderStatistics',
        '',
        [
            'Order' => 'statistic',
        ],
        [
            'access' => 'user, group',
            'icon' => $iconPath . 'module_order_statistics.' . (\TYPO3\CMS\Core\Utility\GeneralUtility::compat_version('7.0') ? 'svg' : 'png'),
            'labels' => $_LLL . ':tx_cart.module.order_statistics',
            'navigationComponentId' => 'typo3-pagetree',
        ]
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'Extcode.' . $_EXTKEY,
        'Cart',
        'Products',
        '',
        [
            'Product' => 'list, show,',
            'Variant' => 'list, show, edit, update',
        ],
        [
            'access' => 'user, group',
            'icon' => $iconPath . 'module_products.' . (\TYPO3\CMS\Core\Utility\GeneralUtility::compat_version('7.0') ? 'svg' : 'png'),
            'labels' => $_LLL . ':tx_cart.module.products',
            'navigationComponentId' => 'typo3-pagetree',
        ]
    );
}

$TCA['pages']['columns']['module']['config']['items'][] = [
    'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_db.xlf:tx_cart.module.orders',
    'orders',
    $iconPath . 'pages_orders_icon.png'
];

\TYPO3\CMS\Backend\Sprite\SpriteManager::addTcaTypeIcon(
    'pages',
    'contains-orders',
    $iconPath . 'pages_orders_icon.png'
);

$TCA['pages']['columns']['module']['config']['items'][] = [
    'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_db.xlf:tx_cart.module.products',
    'products',
    $iconPath . 'pages_products_icon.png'
];

\TYPO3\CMS\Backend\Sprite\SpriteManager::addTcaTypeIcon(
    'pages',
    'contains-products',
    $iconPath . 'pages_products_icon.png'
);

$tables = [
    'order_item',
    'order_address',
    'order_taxclass',
    'order_tax',
    'order_product',
    'order_productadditional',
    'order_shipping',
    'order_payment',
    'order_transaction',
    'product_coupon',
    'product_product',
    'product_specialprice',
    'product_taxclass',
    'product_fevariant',
    'product_bevariant',
    'product_bevariantattribute',
    'product_bevariantattributeoption',
];

foreach ($tables as $table) {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
        'tx_cart_domain_model_' . $table,
        'EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_csh_tx_cart_domain_model_' . $table . '.xlf'
    );
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::makeCategorizable(
    $_EXTKEY,
    'tx_cart_domain_model_product_product',
    'product_categories',
    []
);