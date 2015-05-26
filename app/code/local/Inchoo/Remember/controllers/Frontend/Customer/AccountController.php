<?php

require_once Mage::getModuleDir('controllers', 'Mage_Customer') . DS . 'AccountController.php';

class Inchoo_Remember_Frontend_Customer_AccountController extends Mage_Customer_AccountController {
    public function loginPostAction() {
        parent::loginPostAction();
        $login = $this->getRequest()->getPost('login');
        if ($this->_getSession()->isLoggedIn() && isset($login['remember'])) {
            $safe_pass = base64_encode( mcrypt_encrypt(MCRYPT_RIJNDAEL_256, 'taslc99274', $this->_getSession()->getCustomer()->getId(), MCRYPT_MODE_ECB) );
            setcookie('anastasia', $safe_pass, time() + 60 * 60 * 24 * 7, '/');
        } else {
            if (isset($_COOKIE['anastasia'])) setcookie('anastasia', $safe_pass, time() - 60 * 60 * 24 * 7, '/');
        }
    }

    public function logoutAction() {
        if (isset($_COOKIE['anastasia'])) setcookie('anastasia', '', time() - 60 * 60 * 24 * 7, '/');
        parent::logoutAction();
    }
    
     /**
     * Add welcome message and send new account email.
     * Returns success URL
     *
     * @param Mage_Customer_Model_Customer $customer
     * @param bool $isJustConfirmed
     * @return string
     */
    protected function _welcomeCustomer(Mage_Customer_Model_Customer $customer, $isJustConfirmed = false) {
        $this->_getSession()->addSuccess(
                $this->__('Thank you for registering with %s.', Mage::app()->getStore()->getFrontendName())
        );
        if ($this->_isVatValidationEnabled()) {
            // Show corresponding VAT message to customer
            $configAddressType = Mage::helper('customer/address')->getTaxCalculationAddressType();
            $userPrompt = '';
            switch ($configAddressType) {
                case Mage_Customer_Model_Address_Abstract::TYPE_SHIPPING:
                    $userPrompt = $this->__('If you are a registered VAT customer, please click <a href="%s">here</a> to enter you shipping address for proper VAT calculation', Mage::getUrl('customer/address/edit'));
                    break;
                default:
                    $userPrompt = $this->__('If you are a registered VAT customer, please click <a href="%s">here</a> to enter you billing address for proper VAT calculation', Mage::getUrl('customer/address/edit'));
            }
            $this->_getSession()->addSuccess($userPrompt);
        }

        $customer->sendNewAccountEmail(
                $isJustConfirmed ? 'confirmed' : 'registered',
                '',
                Mage::app()->getStore()->getId()
        );

        //$successUrl = Mage::getUrl('*/*/index', array('_secure'=>true));
        $successUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);

        if ($this->_getSession()->getBeforeAuthUrl()) {
            $successUrl = $this->_getSession()->getBeforeAuthUrl(true);
        }
        return $successUrl;
    }
    
    /**
     * Change customer password action
     */
    public function editPostAction()
    {
        $this->getRequest()->setPost('spouse_dob', $this->getRequest()->getPost('spouse_year') . '-' . $this->getRequest()->getPost('spouse_month') . '-' . $this->getRequest()->getPost('spouse_day'));
        if (!$this->_validateFormKey()) {
            return $this->_redirect('*/*/edit');
        }

        if ($this->getRequest()->isPost()) {
            /** @var $customer Mage_Customer_Model_Customer */
            $customer = $this->_getSession()->getCustomer();

            /** @var $customerForm Mage_Customer_Model_Form */
            $customerForm = Mage::getModel('customer/form');
            $customerForm->setFormCode('customer_account_edit')
                ->setEntity($customer);

            $customerData = $customerForm->extractData($this->getRequest());
            if ($customerData['spouse_dob'] != '--') {
                $customer->setSpouseDob($customerData['spouse_dob']);
            } else {
                $customer->setSpouseDob(NULL);
            }
            $errors = array();
            $customerErrors = $customerForm->validateData($customerData);
            if ($customerErrors !== true) {
                $errors = array_merge($customerErrors, $errors);
            } else {
                $customerForm->compactData($customerData);
                $errors = array();

                // If password change was requested then add it to common validation scheme
                if ($this->getRequest()->getParam('change_password')) {
                    $currPass   = $this->getRequest()->getPost('current_password');
                    $newPass    = $this->getRequest()->getPost('password');
                    $confPass   = $this->getRequest()->getPost('confirmation');

                    $oldPass = $this->_getSession()->getCustomer()->getPasswordHash();
                    if (Mage::helper('core/string')->strpos($oldPass, ':')) {
                        list($_salt, $salt) = explode(':', $oldPass);
                    } else {
                        $salt = false;
                    }

                    if ($customer->hashPassword($currPass, $salt) == $oldPass) {
                        if (strlen($newPass)) {
                            /**
                             * Set entered password and its confirmation - they
                             * will be validated later to match each other and be of right length
                             */
                            $customer->setPassword($newPass);
                            $customer->setConfirmation($confPass);
                        } else {
                            $errors[] = $this->__('New password field cannot be empty.');
                        }
                    } else {
                        $errors[] = $this->__('Invalid current password');
                    }
                }

                // Validate account and compose list of errors if any
                $customerErrors = $customer->validate();
                if (is_array($customerErrors)) {
                    $errors = array_merge($errors, $customerErrors);
                }
            }

            if (!empty($errors)) {
                $this->_getSession()->setCustomerFormData($this->getRequest()->getPost());
                foreach ($errors as $message) {
                    $this->_getSession()->addError($message);
                }
                $this->_redirect('*/*/edit');
                return $this;
            }

            try {
                $customer->setConfirmation(null);
              //  $customer->save(); /* commenting as 2 save functions were called consequtively */
                $this->_getSession()->setCustomer($customer)
                    ->addSuccess($this->__('The account information has been saved.'));


                try {
                    $customer->setStoreId(Mage::app()->getStore()->getId())
							->setIsSubscribed((boolean)$this->getRequest()->getParam('is_subscribed', false))
							->save();
                }
                catch (Exception $e) {
                    Mage::getSingleton('customer/session')->addError($this->__('An error occurred while saving your subscription.'));
                }
            

                $this->_redirect('customer/account/edit');
                return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->setCustomerFormData($this->getRequest()->getPost())
                    ->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->setCustomerFormData($this->getRequest()->getPost())
                    ->addException($e, $this->__('Cannot save the customer.'));
            }
            
            


        }

        $this->_redirect('*/*/edit');
    }

}
