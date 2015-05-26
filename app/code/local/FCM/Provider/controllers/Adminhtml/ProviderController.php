<?php

class FCM_Provider_Adminhtml_ProviderController extends Mage_Adminhtml_Controller_action {

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu("system/provider")
                ->_addBreadcrumb(Mage::helper("adminhtml")->__("Provider Manager"),
                        Mage::helper("adminhtml")->__("Provider Manager")
        );
        return $this;
    }

    public function indexAction() {
        $this->_initAction()->renderLayout();
    }

    public function editAction() {

        $providerId = $this->getRequest()->getParam("id");
        $providerModel = Mage::getModel("provider/provider")->load($providerId);
        if ($providerModel->getProviderId() || $providerId == 0) {
            Mage::register("provider_data", $providerModel);

            $this->loadLayout();
            $this->_setActiveMenu("provider");

            $this->_addBreadcrumb(Mage::helper("adminhtml")->__("Provider Manager"),
                    Mage::helper("adminhtml")->__("Provider Manager"));
            $this->_addBreadcrumb(Mage::helper("adminhtml")->__("Shipping provider"),
                    Mage::helper("adminhtml")->__("Shipping provider"));

            $this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('provider/adminhtml_provider_edit'))
                    ->_addLeft($this->getLayout()->createBlock('provider/adminhtml_provider_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton("adminhtml/session")->addError(
                    Mage::helper("provider")->__("Provider does not exist")
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
                $providerModel = Mage::getModel("provider/provider");
                $flag = $providerModel->getFlagProvider($postData["shippingprovider_name"]);

                if (!$this->getRequest()->getParam("id") && $flag) {

                    // add action

                    Mage::getSingleton("adminhtml/session")->addError('This Provider Already Exist.');

                    Mage::getSingleton("adminhtml/session")->setproviderData(
                            $this->getRequest()->getPost()
                    );
                    $this->_redirect("*/*/edit",
                            array("id" => $this->getRequest()->getParam("id"))
                    );
                    return;
                }


                $providerModel->setProviderId($this->getRequest()->getParam("id"))
                        ->setBlinkecarrierId($providerModel->getBlinkeCarrierId($postData["shippingprovider_name"]))
                        ->setShippingproviderName($postData["shippingprovider_name"])
                        ->setShippingproviderHovertext($postData["shippingprovider_hovertext"])
                        ->setShippingproviderAction($postData["shippingprovider_action"])
                        ->save();

                Mage::getSingleton("adminhtml/session")->addSuccess(
                        Mage::helper("adminhtml")->__("Provider was successfully saved")
                );
                Mage::getSingleton("adminhtml/session")->setproviderData(false);

                $this->_redirect("*/*/");
                return;
                
            } catch (Exception $e) {
                Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
                Mage::getSingleton("adminhtml/session")->setproviderData(
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
                $providerModel = Mage::getModel("provider/provider");

                $providerModel->setProviderId($this->getRequest()->getParam("id"))->delete();

                Mage::getSingleton("adminhtml/session")->addSuccess(
                        Mage::helper("adminhtml")->__("Provider was successfully deleted")
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