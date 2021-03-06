<?php

namespace Extcode\Cart\Service;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Core\SingletonInterface;

/**
 * MailHandler
 *
 * @package cart
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class MailHandler implements SingletonInterface
{
    /**
     * Extension Name
     *
     * @var string
     */
    protected $extensionName = 'Cart';

    /**
     * Plugin Name
     *
     * @var string
     */
    protected $pluginName = 'Cart';

    /**
     * Object Manager
     *
     * @var \TYPO3\CMS\Extbase\Object\ObjectManager
     */
    protected $objectManager;

    /**
     * Log Manager
     *
     * @var \TYPO3\CMS\Core\Log\LogManager
     */
    protected $logManager;

    /**
     * Configuration Manager
     *
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManager
     * @inject
     */
    protected $configurationManager;

    /**
     * Plugin Settings
     *
     * @var array
     */
    protected $pluginSettings;

    /**
     * Cart
     *
     * @var \Extcode\Cart\Domain\Model\Cart\Cart
     */
    protected $cart;

    /**
     * Buyer Email From Address
     *
     * @var string
     */
    protected $buyerEmailFrom;

    /**
     * Seller Email From Address
     *
     * @var string
     */
    protected $sellerEmailFrom;

    /**
     * Seller Email To Address
     *
     * @var string
     */
    protected $sellerEmailTo;

    /**
     * MailHandler constructor
     */
    public function __construct()
    {
        $this->objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            'TYPO3\CMS\Extbase\Object\ObjectManager'
        );

        $this->logManager = $this->objectManager->get(
            \TYPO3\CMS\Core\Log\LogManager::class
        );

        $this->configurationManager = $this->objectManager->get(
            \TYPO3\CMS\Extbase\Configuration\ConfigurationManager::class
        );

        $this->setPluginSettings();

        if (!empty($this->pluginSettings['settings'])) {
            if (!empty($this->pluginSettings['settings']['buyer'])
                && !empty($this->pluginSettings['settings']['buyer']['emailFromAddress'])
            ) {
                $this->setBuyerEmailFrom($this->pluginSettings['settings']['buyer']['emailFromAddress']);
            } elseif (!empty($this->pluginSettings['mail'])
                && !empty($this->pluginSettings['mail']['buyer'])
                && !empty($this->pluginSettings['mail']['buyer']['fromAddress'])
            ) {
                $this->setBuyerEmailFrom($this->pluginSettings['mail']['buyer']['fromAddress']);
            }

            if (!empty($this->pluginSettings['settings']['seller'])
                && !empty($this->pluginSettings['settings']['seller']['emailFromAddress'])
            ) {
                $this->setSellerEmailFrom($this->pluginSettings['settings']['seller']['emailFromAddress']);
            } elseif (!empty($this->pluginSettings['mail'])
                && !empty($this->pluginSettings['mail']['seller'])
                && !empty($this->pluginSettings['mail']['seller']['fromAddress'])
            ) {
                $this->setSellerEmailFrom($this->pluginSettings['mail']['seller']['fromAddress']);
            }

            if (!empty($this->pluginSettings['settings']['seller'])
                && !empty($this->pluginSettings['settings']['seller']['emailToAddress'])
            ) {
                $this->setSellerEmailTo($this->pluginSettings['settings']['seller']['emailToAddress']);
            } elseif (!empty($this->pluginSettings['mail'])
                && !empty($this->pluginSettings['mail']['seller'])
                && !empty($this->pluginSettings['mail']['seller']['toAddress'])
            ) {
                $this->setSellerEmailTo($this->pluginSettings['mail']['seller']['toAddress']);
            }
        }
    }

    /**
     * Sets Plugin Settings
     *
     * @return void
     */
    public function setPluginSettings()
    {
        $this->pluginSettings =
            $this->configurationManager->getConfiguration(
                \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK,
                $this->extensionName,
                $this->pluginName
            );
    }

    /**
     * Sets Cart
     *
     * @param \Extcode\Cart\Domain\Model\Cart\Cart $cart
     *
     * @return void
     */
    public function setCart($cart)
    {
        $this->cart = $cart;
    }

    /**
     * @param string $email
     *
     * @return void
     */
    public function setBuyerEmailFrom($email)
    {
        $this->buyerEmailFrom = $email;
    }

    /**
     * @param string $email
     *
     * @return void
     */
    public function setSellerEmailFrom($email)
    {
        $this->sellerEmailFrom = $email;
    }

    /**
     * @param string $email
     *
     * @return void
     */
    public function setSellerEmailTo($email)
    {
        $this->sellerEmailTo = $email;
    }

    /**
     * Send a Mail to Buyer
     *
     * @param \Extcode\Cart\Domain\Model\Order\Item $orderItem Order Item
     * @param \Extcode\Cart\Domain\Model\Order\Address $billingAddress Billing Address
     * @param \Extcode\Cart\Domain\Model\Order\Address $shippingAddress Shipping Address
     *
     * @return void
     */
    public function sendBuyerMail(
        \Extcode\Cart\Domain\Model\Order\Item $orderItem,
        \Extcode\Cart\Domain\Model\Order\Address $billingAddress,
        \Extcode\Cart\Domain\Model\Order\Address $shippingAddress = null
    ) {
        if (empty($this->buyerEmailFrom)) {
            return;
        }

        $status = $orderItem->getPayment()->getStatus();
        $view = $this->getEmailStandaloneView('/Mail/' . ucfirst($status) . '/', 'Buyer', 'html');

        if ($view->getTemplatePathAndFilename()) {
            $view->assign('settings', $this->pluginSettings['settings']);

            $view->assign('cart', $this->cart);
            $view->assign('orderItem', $orderItem);
            $view->assign('billingAddress', $billingAddress);
            $view->assign('shippingAddress', $shippingAddress);

            $mailBody = $view->render();

            $mail = $this->objectManager->get('TYPO3\\CMS\\Core\\Mail\\MailMessage');
            $mail->setFrom($this->buyerEmailFrom);
            $mail->setTo($billingAddress->getEmail());
            $mail->setSubject(
                \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('tx_cart.mail.buyer.subject', 'Cart')
            );
            $mail->setBody($mailBody, 'text/html', 'utf-8');
            //$mail->addPart(strip_tags($mailBody), 'text/plain', 'utf-8');

            // get and add attachments
            $attachments = $this->getAttachments('buyer');
            foreach ($attachments as $attachment) {
                $attachmentFile = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($attachment);
                if (file_exists($attachmentFile)) {
                    $mail->attach(\Swift_Attachment::fromPath($attachmentFile));
                }
            }

            $mail->send();
        }
    }

    /**
     * Send a Mail to Seller
     *
     * @param \Extcode\Cart\Domain\Model\Order\Item $orderItem Order Item
     * @param \Extcode\Cart\Domain\Model\Order\Address $billingAddress Billing Address
     * @param \Extcode\Cart\Domain\Model\Order\Address $shippingAddress Shipping Address
     *
     * @return void
     */
    public function sendSellerMail(
        \Extcode\Cart\Domain\Model\Order\Item $orderItem,
        \Extcode\Cart\Domain\Model\Order\Address $billingAddress,
        \Extcode\Cart\Domain\Model\Order\Address $shippingAddress = null
    ) {
        if (empty($this->sellerEmailFrom) &&
            empty($this->sellerEmailTo)
        ) {
            return;
        }

        $status = $orderItem->getPayment()->getStatus();
        $view = $this->getEmailStandaloneView('/Mail/' . ucfirst($status) . '/', 'Seller', 'html');

        if ($view->getTemplatePathAndFilename()) {
            $view->assign('settings', $this->pluginSettings['settings']);

            $view->assign('cart', $this->cart);
            $view->assign('orderItem', $orderItem);
            $view->assign('billingAddress', $billingAddress);
            $view->assign('shippingAddress', $shippingAddress);

            $mailToAddresses = explode(',', $this->sellerEmailTo);

            $mailBody = $view->render();

            $mail = $this->objectManager->get('TYPO3\\CMS\\Core\\Mail\\MailMessage');
            $mail->setFrom($this->sellerEmailFrom);
            $mail->setTo($mailToAddresses);
            $mail->setSubject(
                \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('tx_cart.mail.seller.subject', 'Cart')
            );
            $mail->setBody($mailBody, 'text/html', 'utf-8');
            //$mail->addPart(strip_tags($mailBody), 'text/plain', 'utf-8');

            // get and add attachments
            $attachments = $this->getAttachments('seller');
            foreach ($attachments as $attachment) {
                $attachmentFile = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($attachment);
                if (file_exists($attachmentFile)) {
                    $mail->attach(\Swift_Attachment::fromPath($attachmentFile));
                }
            }

            $mail->send();
        }
    }

    /**
     * @param string $to
     *
     * @return array
     */
    protected function getAttachments($to) {
        if ($this->pluginSettings['mail'] &&
            $this->pluginSettings['mail'][$to] &&
            $this->pluginSettings['mail'][$to]['attachments']
        ) {
            return $this->pluginSettings['mail'][$to]['attachments'];
        }

        return [];
    }

    /**
     * This creates another stand-alone instance of the Fluid StandaloneView
     * to render an e-mail template
     *
     * @param string $templatePath
     * @param string $templateFileName
     * @param string $format
     *
     * @return \TYPO3\CMS\Fluid\View\StandaloneView Fluid instance
     */
    protected function getEmailStandaloneView($templatePath = '/Mail/', $templateFileName = 'Default', $format = 'html')
    {
        $templatePathAndFileName = $templatePath . $templateFileName . '.' . $format;

        /** @var \TYPO3\CMS\Fluid\View\StandaloneView $view */
        $view = $this->objectManager->get('TYPO3\\CMS\\Fluid\\View\\StandaloneView');
        $view->setFormat($format);

        if ($this->pluginSettings['view']) {
            $view->setLayoutRootPaths($this->resolveRootPaths('layoutRootPaths'));
            $view->setPartialRootPaths($this->resolveRootPaths('partialRootPaths'));

            if ($this->pluginSettings['view']['templateRootPaths']) {
                foreach ($this->pluginSettings['view']['templateRootPaths'] as $pathNameKey => $pathNameValue) {
                    $templateRootPath = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName(
                        $pathNameValue
                    );

                    $completePath = $templateRootPath . $templatePathAndFileName;
                    if (file_exists($completePath)) {
                        $view->setTemplatePathAndFilename($completePath);
                    }
                }
            }
        }

        if (!$view->getTemplatePathAndFilename()) {
            $logger = $this->logManager->getLogger(__CLASS__);
            $logger->error(
                'Cannot find Template for MailHandler',
                [
                    'templateRootPaths' => $this->pluginSettings['view']['templateRootPaths'],
                    'templatePathAndFileName' => $templatePathAndFileName,
                ]
            );
        }

        // set controller extension name for translation
        $view->getRequest()->setControllerExtensionName('Cart');

        return $view;
    }

    /**
     * Returns the Partial Root Path
     *
     * For TYPO3 Version 6.2 it resolves the absolute file names
     *
     * @var string $type
     * @return array
     *
     * @deprecated will be removed with support for TYPO3 6.2
     */
    protected function resolveRootPaths($type)
    {
        $rootPaths = [];

        if ($this->pluginSettings['view'][$type]) {
            $rootPaths = $this->pluginSettings['view'][$type];

            if (\TYPO3\CMS\Core\Utility\GeneralUtility::compat_version('6.2')) {
                foreach ($rootPaths as $rootPathsKey => $rootPathsValue) {
                    $rootPaths[$rootPathsKey] = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName(
                        $rootPathsValue
                    );
                }
            }
        }

        return $rootPaths;
    }
}
