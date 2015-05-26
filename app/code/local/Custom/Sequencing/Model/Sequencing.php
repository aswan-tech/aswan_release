<?php
 class Custom_Sequencing_Model_Sequencing extends Mage_Core_Model_Abstract
 {
    public function _construct()
    { 
        parent::_construct();
        $this->_init('sequencing/sequencing');
    }
    public function getSizeActionArray(){
        $size_array = array('eq'=>'Equal','gt'=>'Greater Then',
                'gteq'=>'Greater Than and Equal','lt'=>'Less Then','lteq'=>'Less Than and Equal');
        return $size_array;
    }
   public function getTypeActionArray(){
        $size_array = array('price_high'=>'Price High','discount'=>'Highest Discount','price_low'=>'Price Low',
                'updated_at'=>'Newest');
        return $size_array;
    }
    public function getCategoryAttributeArray(){
        $attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', 'product_category');
        $cat_attr_array = array();
        foreach ($attribute->getSource()->getAllOptions(true, true) as $instance) {
            $cat_attr_array[$instance['value']] = $instance['label'];
          }
          return $cat_attr_array;
    }
    public function getGenderAttributeArray(){
        $attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', 'gender');
        $gender_attr_array = array();
        foreach ($attribute->getSource()->getAllOptions(true, true) as $instance) {
            $gender_attr_array[$instance['value']] = $instance['label'];
          }
          return $gender_attr_array;
    }
}