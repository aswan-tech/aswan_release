<?php

/**
 * Newsletter subscribe controller
 *
 * @category    Mage
 * @package     Mage_Newsletter
 * @author      Magento Core Team <core@magentocommerce.com>
 */
require_once 'Mage/Newsletter/controllers/SubscriberController.php';

class FCM_Newsletters_SubscriberController extends Mage_Newsletter_SubscriberController {

    /**
     * New subscription action
     * appiled the duplicate email check
     */
    public function newAction() {
        if ($this->getRequest()->isPost() && $this->getRequest()->getPost('email')) {
            $session = Mage::getSingleton('core/session');
            $customerSession = Mage::getSingleton('customer/session');
            $email = (string) $this->getRequest()->getPost('email');

            try {
                if (!Zend_Validate::is($email, 'EmailAddress')) {
                    Mage::throwException($this->__('Please enter a valid email address.'));
                }

                if (Mage::getStoreConfig(Mage_Newsletter_Model_Subscriber::XML_PATH_ALLOW_GUEST_SUBSCRIBE_FLAG) != 1 &&
                        !$customerSession->isLoggedIn()) {
                    Mage::throwException($this->__('Sorry, but administrator denied subscription for guests. Please <a href="%s">register</a>.', Mage::helper('customer')->getRegisterUrl()));
                }

                $load = Mage::getModel('newsletter/subscriber')->loadByEmail($email);
                $sub_status = $load->getSubscriberStatus();

                $ownerId = Mage::getModel('customer/customer')
                                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                                ->loadByEmail($email)
                                ->getId();
                $guestId = Mage::getModel('newsletter/subscriber')
                                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                                ->loadByEmail($email)
                                ->getId();
                
                if ($guestId !== null && $sub_status != '3') {
                    Mage::throwException($this->__('This email address is already assigned to another user.'));
                }
                if ($ownerId !== null && $ownerId != $customerSession->getId() && $sub_status != '3') {
                    Mage::throwException($this->__('This email address is already assigned to another user.'));
                }

                $status = Mage::getModel('newsletter/subscriber')->subscribe($email);
                if ($status == Mage_Newsletter_Model_Subscriber::STATUS_NOT_ACTIVE) {
                    $session->addSuccess($this->__('Confirmation request has been sent.'));
                } else {
                    $session->addSuccess($this->__('Thank you for your subscription.'));
                }
            } catch (Mage_Core_Exception $e) {
                $session->addException($e, $this->__('There was a problem with the subscription: %s', $e->getMessage()));
            } catch (Exception $e) {
                $session->addException($e, $this->__('There was a problem with the subscription.'));
            }
        }
        $this->_redirectReferer();
    }

}
