<?php

class FCM_Productreports_Helper_Data extends Mage_Core_Helper_Abstract {

    public function getProductCategories($cId="", $storeId="", $flag="stock_report") {
        if (empty($storeId)) {
            $storeId = Mage_Core_Model_App::DISTRO_STORE_ID;
        }

        if (empty($cId)) {
            $cId = Mage::app()->getStore($storeId)->getRootCategoryId();
        }

        $cts = array();

        if($flag=="stock_report"){
            $cts[] = Mage::helper('productreports')->__('Select');
        }elseif($flag=="order_report"){
            $cts[] = Mage::helper('productreports')->__('Select Department');
        }else{
            $cts[] = Mage::helper('productreports')->__('Select');
        }

        

        if (!empty($cId)) {
            $_categories = Mage::getModel('catalog/category')
                            ->getCollection()
                            ->addAttributeToSelect(array('id', 'name'))
                            ->addAttributeToFilter('parent_id', array('eq' => $cId))
                            ->addOrderField('name');

            if (count($_categories) > 0) {
                foreach ($_categories as $_category) {
                    $cts[$_category->getId()] = $_category->getName();
                }
            }
        }

        return $cts;
    }

    public function getAttributeOptions($attributeCode) {
        $attributeModel = Mage::getSingleton('eav/config')
                        ->getAttribute('catalog_product', $attributeCode);

        $_options = array();

        if ($attributeModel->usesSource()) {
            $allOptions = $attributeModel->getSource()->getAllOptions();

            foreach ($allOptions as $option) {
                $_options[$option['value']] = $option['label'];
            }
        }

        return $_options;
    }

    public function getAllZipCodes(){
        
        $collection = Mage::getModel('zipcodeimport/zipcodeimport')->getCollection();
        $collection->getSelect()->distinct(true);
    }

    public function getShippingProviders(){

        $collection = Mage::getModel('provider/provider')->getCollection();
        $collection->getSelect()->group('shippingprovider_name');
    }

}