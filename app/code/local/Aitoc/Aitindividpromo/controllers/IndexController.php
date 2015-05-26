<?php
/**
 * Product:     Individual Promotions for Magento Enterpise Edition
 * Package:     Aitoc_Aitindividpromo_10.0.7_574525
 * Purchase ID: UjgdLvjpFE0u1HHQEOk2KNCXazbZ9kQjUnTtO4dMb0
 * Generated:   2013-05-13 06:35:45
 * File path:   app/code/local/Aitoc/Aitindividpromo/controllers/IndexController.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitindividpromo')){ UIjBDajBZqqIsDaw('c031e6558dce5af6366ab3ad827293db'); ?><?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc.
 */


class Aitoc_Aitindividpromo_IndexController extends Mage_Adminhtml_Controller_Action
{
	public function customersAction()
	{
        //$this->getResponse()->setBody($this->getLayout()->createBlock('aitindividpromo/customers')->toHtml());
        $block = $this->getLayout()->createBlock('aitindividpromo/customers');
        $result['html'] = $block->toHtml();
        $result['aSearchCustomerHash'] = $block->getCustomers();
        $this->getResponse()->setBody(Zend_Json::encode($result));
	}
	
	public function sendmailAction(){
		$rule_id	=	$this->getRequest()->getParam('id');
		$storeId	=	Mage::app()->getStore()->getId();
		$webSiteId	=	Mage::app()->getStore()->getWebsiteId(); 
		
		$process	=	Mage::getModel('common/common')->createCouponForAlreadyRegistered($rule_id, $storeId, $webSiteId);
		
		//pr($process);
		
		if(!empty($process)){
			$err_msg = "";
			foreach($process as $msg){
				$err_msg .=	$msg."<br>";
			}
			
			Mage::getSingleton('adminhtml/session')->addError($this->__($err_msg));
		}else{
			Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Mail sent to users.'));
		}
		$this->_redirectReferer();//'admin/promo_quote/edit', array('id' => $rule_id)
		//$this->_redirect('*/*/edit', array('id' => $rule_id));
		return;
	}
}

 } ?>