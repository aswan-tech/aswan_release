<?php 
class FCM_Shortlist_Model_Category_Categoryfiltervalues 
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $option_array = array();
        $product_category_array = $this->getCategoryAttributeArray();
        foreach($product_category_array  as $value=>$label){
            $option_array[] = array(
                                        'label'=>$label,
                                        'value'=>$value
                                    );
        }
        return  $option_array;
    }
    public function getCategoryAttributeArray(){
        $attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', 'product_category');
        $cat_attr_array = array();
        foreach ($attribute->getSource()->getAllOptions(true, true) as $instance) {
            if(isset($instance['value']) && $instance['value']!='')
                $cat_attr_array[$instance['value']] = $instance['label'];
          }
          return $cat_attr_array;
    }
}    