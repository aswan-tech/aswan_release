<?php

/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */
require_once("Mage/Adminhtml/controllers/Sales/Order/EditController.php");

/**
 * Adminhtml sales order edit controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class FCM_Lockorder_Adminhtml_Sales_Order_EditController extends Mage_Adminhtml_Sales_Order_EditController {

    /**
     * Additional initialization
     *
     */
    protected function _construct() {
        $this->setUsedModuleName('Mage_Sales');
    }

    /**
     * Start edit order initialization
     */
    public function startAction() {
        $this->_getSession()->clear();
        $orderId = $this->getRequest()->getParam('order_id');
        $order = Mage::getModel('sales/order')->load($orderId);

        if ($order->getId()) {
            $this->_getSession()->setUseOldShippingMethod(true);
            $this->_getOrderCreateModel()->initFromOrder($order);

            /**
             * Lock Order Here
             */
            $write = Mage::getSingleton('core/resource')->getConnection('core_write');

            $condition = array($write->quoteInto('order_id=?', $order->getIncrementId()));
            $write->delete('lockorder', $condition);

            $sql = "INSERT INTO lockorder values (?,?,?,?,?)"; //insert query
            $write->query($sql, array('', $order->getIncrementId(), '1', Varien_Date::now(), '')); //write to database

            /**
             * End of Lock Order Here
             */
            $this->_redirect('*/*');
        } else {
            $this->_redirect('*/sales_order/');
        }
    }

    /**
     * Index page
     */
    public function indexAction() {
        $this->_title($this->__('Sales'))->_title($this->__('Orders'))->_title($this->__('Edit Order'));
        $this->loadLayout();

        $this->_initSession()
                ->_setActiveMenu('sales/order')
                ->renderLayout();
    }

    /**
     * Acl check for admin
     *
     * @return bool
     */
    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/edit');
    }
	
	/**
     * Loading page block
     */
    public function loadBlockAction()
    {
        $request = $this->getRequest();
        try {
            $this->_initSession()
                ->_processData();
        }
        catch (Mage_Core_Exception $e){
            $this->_reloadQuote();
            $this->_getSession()->addError($e->getMessage());
        }
        catch (Exception $e){
            $this->_reloadQuote();
            $this->_getSession()->addException($e, $e->getMessage());
        }
		
		$billingError = '';
			$data = $request->getPost();
			if(isset($data['order']['billing_address'])){
				$state = $data['order']['billing_address']['region_id'];
				$zip = $data['order']['billing_address']['postcode'];
				if($zip != '' && $state != ''){
					$regionModel = Mage::getModel('directory/region')->load($state);
					$stateName = strtolower($regionModel->getName());
					$read =  Mage::getSingleton('core/resource')->getConnection('core_read');
					$query = "SELECT state FROM fcm_zipcodeimport WHERE zip_code like '".$zip."' AND state like '".$stateName."'";
					$result = $read->fetchAll($query);
					if(count($result) < 1){
						$billingError = array('error' => 2,'message' => 'State/PostalCode combination for Billing Address is not valid.');
					}
				}
			}
			$ShippingError = '';
			if(isset($data['order']['shipping_address'])){
				$state = $data['order']['shipping_address']['region_id'];
				$zip = $data['order']['shipping_address']['postcode'];
				if($zip != '' && $state != ''){
					$regionModel = Mage::getModel('directory/region')->load($state);
					$stateName = strtolower($regionModel->getName());
					$read =  Mage::getSingleton('core/resource')->getConnection('core_read');
					$query = "SELECT state FROM fcm_zipcodeimport WHERE zip_code like '".$zip."' AND state like '".$stateName."'";
					$result = $read->fetchAll($query);
					if(count($result) < 1){
						 $ShippingError = array('error' => 2,'message' => 'State/PostalCode combination for Shipping Address is not valid.');
					}
				}
			}


        $asJson= $request->getParam('json');
        $block = $request->getParam('block');

        $update = $this->getLayout()->getUpdate();
        if ($asJson) {
            $update->addHandle('adminhtml_sales_order_create_load_block_json');
        } else {
            $update->addHandle('adminhtml_sales_order_create_load_block_plain');
        }

        if ($block) {
            $blocks = explode(',', $block);
            if ($asJson && !in_array('message', $blocks)) {
                $blocks[] = 'message';
            }

            foreach ($blocks as $block) {
                $update->addHandle('adminhtml_sales_order_create_load_block_' . $block);
            }
        }
        $this->loadLayoutUpdates()->generateLayoutXml()->generateLayoutBlocks();
        $result = $this->getLayout()->getBlock('content')->toHtml();
		if($billingError!=''){			
			$result = json_decode($result);
			$key = 'billing_error';
			$result->$key = $billingError;
			$result = json_encode($result);
		}
		if($ShippingError!=''){
			$result = json_decode($result);
			$key1 = 'shipping_error';
			$result->$key1 = $ShippingError;
			$result = json_encode($result);
		}
			
        if ($request->getParam('as_js_varname')) {
            Mage::getSingleton('adminhtml/session')->setUpdateResult($result);
            $this->_redirect('*/*/showUpdateResult');
        } else {
            $this->getResponse()->setBody($result);
        }
    }

}
