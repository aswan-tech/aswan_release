<?php 
class FCM_Shortlist_Model_Category_Values 
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $option_array = array();
        $topLevelCategory = $this->getAllTopLevelCategories();
        $i=0;
        foreach($topLevelCategory  as $top_cat){
            $cat_id =$top_cat->getId();
            $name = $top_cat->getName();
            $sub_cat_data = $this->getSubcategory($cat_id,$name);
            $option_array[$i] = array(
                                        'label'=>$name,
                                        'value'=>$sub_cat_data
                                    );
            $i++;
        }
        return  $option_array;
    }
    public function getAllTopLevelCategories(){
            $categories = Mage::getModel('catalog/category')->getCollection()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('level', 2)//2 is actually the first level
                ->addAttributeToFilter('is_active', 1);//if you want only active categories

                return $categories;
    }
    public function getSubcategory($_rootCatId,$name){
        $data = array();
        $_rootCategory  = Mage::getModel('catalog/category')->load($_rootCatId);
        $collection = $_rootCategory->getChildrenCategories()->addAttributeToFilter('is_active', 1);
        $data[0]['value'] = $_rootCatId;
        $data[0]['label'] = $name;
        $i=1;
        foreach ($collection as $cat) {
            $data[$i]['value'] = $cat->getId();
            $data[$i]['label'] = $cat->getName();
            $i++;
        }
        return $data;
    }
}    