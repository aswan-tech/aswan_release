<?php

/**
 * FCM Logger Module
 *
 * Module for tracking Log and Cron Detail
 *
 * @category    FCM
 * @package     FCM_Logger
 * @author	Vikrant Kumar Mishra
 * @author_id	51402601
 * @company	HCL Technologies
 * @created Thursday, June 7, 2012
 */
class FCM_Logger_Model_Logger extends Mage_Core_Model_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('logger/logger');
    }

    /*
      This function will be called by any module in magento to register their log on custom
      log table
     */

    public function saveLogger($moduleKey, $status, $fileName, $description) {

        $currentTime = Mage::getModel('fulfillment/process');
        $time = $currentTime->getCurrentDateTime('Y-m-d H:i:s');

        if ($status == "Error" || $status == "Failure") {
            $description = "<font color='red'>" . $description . "</font>";
            /* $fileName = "<font color='red'>".$fileName."</font>";
              $status = "<font color='red'>".$status."</font>";
              $time = "<font color='red'>".$time."</font>";
              $moduleKey = "<font color='red'>".$moduleKey."</font>"; */
        }

        $logger = Mage::getModel('logger/logger')
                        ->setLogTime($time)
                        ->setStatus($status)
                        ->setModuleName($moduleKey)
                        ->setDescription($description)
                        ->setFilename($fileName)
                        ->save();
    }

    /*
      sendNotificationMail function will send mail to the admin for any log or cron update
     */

    public function sendNotificationMail($toEmail='', $cc='', $subject='', $message) {

        $mailTemplate = Mage::getModel('core/email_template');
        $translate = Mage::getSingleton('core/translate');
        $templateId = 'logger_email'; //template for sending customer data
        $template_collection = $mailTemplate->loadByCode($templateId);
        $template_data = $template_collection->getData();
        $store = Mage::app()->getStore();

        if (!empty($template_data)) {

            $fromEmail = Mage::getStoreConfig('trans_email/ident_general/email'); // sender email address
            $fromName = Mage::getStoreConfig('trans_email/ident_general/name'); // sender name

            if (!$toEmail)
                $toEmail = Mage::getStoreConfig('trans_email/ident_general/email');

            $body = str_replace('{{var message}}', $message, $template_data['template_text']); // body text
            $body = str_replace('{{var store.getFrontendName()}}', $store->getName(), $body);
            $body = str_replace('{{store url=""}}', Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB), $body);
            $body = str_replace('{{skin url="images/logo_email.gif"}}', Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN) . 'frontend/enterprise/lecom/images/logo_email.gif', $body);

            if (Mage::getStoreConfig('system/smtpsettings/username')) {
                $config = array(
                    'ssl' => Mage::getStoreConfig('system/smtpsettings/ssl'),
                    'port' => Mage::getStoreConfig('system/smtpsettings/port'),
                    'auth' => Mage::getStoreConfig('system/smtpsettings/authentication'),
                    'username' => Mage::getStoreConfig('system/smtpsettings/username'),
                    'password' => Mage::getStoreConfig('system/smtpsettings/password')
                );
            } else {
                $config = array('port' => Mage::getStoreConfig('system/smtpsettings/port'));
            }

            $smtpConnection = new Zend_Mail_Transport_Smtp(Mage::getStoreConfig('system/smtpsettings/host'), $config);

            $mail = new Zend_Mail();
            $mail->setFrom($fromEmail, $fromName);
            //$mail->addTo($toEmail);
            //$mail->addCc($cc);

            // add the TO email address(es)
            $toAddresses = explode(',', $toEmail);
            foreach ($toAddresses as $address) {
                $mail->addTo(trim($address));
            }
            
            // add the CC email address(es) (if any)
            if (isset($cc)) {
                $ccAddresses = explode(',', $cc);
                foreach ($ccAddresses as $address) {
                    $mail->addCc(trim($address));
                }
            }

            $mail->setSubject($subject);
            $mail->setBodyHtml($body);

            try {
                $mail->send($smtpConnection);
                return true;
            } catch (Exception $ex) {
                return false;
            }
        }
    }

}