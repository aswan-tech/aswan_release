<?php
class FCM_Nosql_IndexController extends Mage_Core_Controller_Front_Action {
    function sizeAction() {
        $pid = Mage::app()->getRequest()->getParam('product_id');
        if(isset($pid) && !empty($pid)) {
            $product_id = unserialize($pid);
            $productSizes = Mage::helper('nosql/product')->getProductAllSizes($product_id);
            echo $json = json_encode($productSizes);
        }
    }

    function mobileAction() {
        $mobileArr = array();
        $mobile = Mage::app()->getRequest()->getParam('mobile');

        if(isset($mobile) && !empty($mobile)) {
            $resource = Mage::getSingleton('core/resource');
            $readConnection = $resource->getConnection('core_read');
            $query = 'SELECT entity_id FROM customer_entity_varchar WHERE value = "' . $mobile . '"';
            $entity_id = $readConnection->fetchOne($query);
            if ( $entity_id > 0 ) {
                echo $this->__('Mobile Exists, Please register with new mobile number.');
            }
        }
    }
	
    public function codsuccessAction()
    {	
        $codsuccess = Mage::app()->getRequest()->getParam('codsuccess');
        $codOid = Mage::app()->getRequest()->getParam('codOid');
        $incrementId = Mage::app()->getRequest()->getParam('incrementId');
        if ($codsuccess != '' && $codOid != '') {
            $codVarification = Mage::helper('nosql/product')->cod_varify($codOid,$codsuccess,$incrementId);
        } 
    }
	
    public function regenerateAction()
    {	
		$incrementId = Mage::app()->getRequest()->getParam('incrementId');
		$mobile = Mage::app()->getRequest()->getParam('mobile');
		if ($incrementId) {
			$regenerateCode = Mage::helper('nosql/product')->new_Otp($incrementId,$mobile);
		} 
    }
}

