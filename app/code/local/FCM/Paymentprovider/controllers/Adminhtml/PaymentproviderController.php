<?php

class FCM_Paymentprovider_Adminhtml_PaymentproviderController extends Mage_Adminhtml_Controller_action {

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu("system/paymentprovider")
                ->_addBreadcrumb(Mage::helper("adminhtml")->__("Payment Providers"),
                        Mage::helper("adminhtml")->__("Payment Providers")
        );
        return $this;
    }

    public function indexAction() {
        $this->_initAction()->renderLayout();        
    }

    public function editAction() {
        $paymentproviderId = $this->getRequest()->getParam("id");
        $paymentproviderModel = Mage::getModel("paymentprovider/paymentprovider")->load($paymentproviderId);

        if ($paymentproviderModel->getPaymentId() || $paymentproviderId == 0) {
            Mage::register("paymentprovider_data", $paymentproviderModel);

            $this->loadLayout();
            $this->_setActiveMenu("system/paymentprovider");

            $this->_addBreadcrumb(Mage::helper("adminhtml")->__("Payment Providers"),
                    Mage::helper("adminhtml")->__("Payment Providers"));
            $this->_addBreadcrumb(Mage::helper("adminhtml")->__("Payment Providers"),
                    Mage::helper("adminhtml")->__("Payment Providers"));

            $this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('paymentprovider/adminhtml_paymentprovider_edit'))
                    ->_addLeft($this->getLayout()->createBlock('paymentprovider/adminhtml_paymentprovider_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton("adminhtml/session")->addError(
                    Mage::helper("paymentprovider")->__("Payment Provider does not exist")
            );
            $this->_redirect("*/*/");
        }
    }

    public function newAction() {
        $this->_forward("edit");
    }

    public function saveAction() {
        if ($this->getRequest()->getPost()) {
            try {
                $postData = $this->getRequest()->getPost();
                $paymentproviderModel = Mage::getModel("paymentprovider/paymentprovider");

                $paymentproviderModel->setId($this->getRequest()->getParam("id"))
                        ->setPaymentMethodType($postData["payment_method_type"])
                        ->setPaymentMethodName($postData["payment_method_name"])
                        ->setPaymentMethodCode($postData["payment_method_code"])
                        ->save();

                Mage::getSingleton("adminhtml/session")->addSuccess(
                        Mage::helper("adminhtml")->__("Payment Provider was successfully saved")
                );
                Mage::getSingleton("adminhtml/session")->setPaymentData(false);

                $this->_redirect("*/*/");
                return;
            } catch (Exception $e) {
                Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
                Mage::getSingleton("adminhtml/session")->setPaymentData(
                        $this->getRequest()->getPost()
                );
                $this->_redirect("*/*/edit",
                        array("id" => $this->getRequest()->getParam("id"))
                );
                return;
            }
        }
        $this->_redirect("*/*/");
    }

    public function deleteAction() {
        if ($this->getRequest()->getParam("id") > 0) {
            try {
                $paymentproviderModel = Mage::getModel("paymentprovider/paymentprovider");

                $paymentproviderModel->setPaymentId($this->getRequest()->getParam("id"))->delete();

                Mage::getSingleton("adminhtml/session")->addSuccess(
                        Mage::helper("adminhtml")->__("Payment Provider was successfully deleted")
                );
                $this->_redirect("*/*/");
            } catch (Exception $e) {
                Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
                $this->_redirect("*/*/edit",
                        array("id" => $this->getRequest()->getParam("id"))
                );
            }
        }
        $this->_redirect("*/*/");
    }

}