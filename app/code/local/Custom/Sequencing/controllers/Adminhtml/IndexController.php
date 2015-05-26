<?php
class Custom_Sequencing_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action
{

    protected $_productCollectionByCategory= array();
    protected $_finalArrayList = array();
    protected $_productsInCat = array();
    protected $_post;
    protected $_message;
    protected $_category_attr_array;
    public function _construct(){
        $this->_model = Mage::getModel('sequencing/sequencing');
        $this->_category_attr_array = $this->_model->getCategoryAttributeArray();
    }
    public function indexAction()
    {
        $this->loadLayout();
        $this->_title($this->__("Category Sequencing"));
        $this->renderLayout();
    }
    public function getSubcategoryAction(){
        $_rootCatId = $this->getRequest()->getParam('category');
        $_rootCategory  = Mage::getModel('catalog/category')->load($_rootCatId);
        $collection = $_rootCategory->getChildrenCategories()->addAttributeToFilter('is_active', 1);        $data = array();
        $i=1;
        foreach ($collection as $cat) {
            $data[$i]['id'] = $cat->getId();
            $data[$i]['name'] = $cat->getName();
            $i++;
        }
        $data = json_encode($data);
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody($data);
    }
    public function setPositionAction(){
        try{
            $post = $this->getRequest()->getPost();
            if(isset($post['sub_category'])){
                $this->_post = $post;
                $sub_category = $post['sub_category'];
                $count = count($sub_category);
                $ordering_start_pos = $this->_getStartPosition($sub_category,array_sum($post['p_total']));
                $i=1;
                foreach($sub_category as $s_cat){
                    $cat_name = Mage::getModel('catalog/category')->load($s_cat)->getName();
                    $sort_attr_array= $this->_sortAttrArray($post['sorting_type'][$i]);  
                    $product_collection = $this->_getCategoryCollection($s_cat,$sort_attr_array,$post['category_attr'][$i],$post['gender_attr'][$i],$i);
                    $this->_getFinalArrayList($s_cat,$post['size_action'][$i],$post['size_total'][$i],$post['p_total'][$i],$i);
                    $this->_message  .=ucfirst(strtolower($cat_name)).' ('.$this->_category_attr_array[$post['category_attr'][$i]].') having total '.count($this->_productsInCat[$s_cat][$i]).' products position has been changed.'."</br>";
                    $i++;
                }
                $this->_changePositioning($ordering_start_pos);
                Mage::getSingleton('core/session')->addSuccess($this->_message);
                $this->_redirect('*/*/index');
            }
            else{
                $this->_redirect('*/*/index');
            }
            
        }catch(Exception $e){
            $this->_redirect('*/*/index');
        }
        
        
    }
    protected function _changePositioning($start_pos){
        if(count($this->_finalArrayList)>0){
            foreach($this->_finalArrayList as $cat=>$pids){
                $category = Mage::getModel('catalog/category')->load($cat);
                $products = $category->getProductsPosition();
                foreach ($pids as $id){
                    file_put_contents('/tmp/abc.txt',$id.' Position:'.$start_pos."\n",FILE_APPEND);
                    $products[$id] = $start_pos;
                    $start_pos = $start_pos-1;
                }
                $category->setPostedProducts($products);
                $category->save();
            }
        }

    }
    protected function _getFinalArrayList($cat,$size_action,$size_total,$p_total,$i){ 
        $total = 0;
        if(count($this->_productCollectionByCategory[$cat][$i])>0){
            foreach($this->_productCollectionByCategory[$cat][$i] as $c_product){
                $cal_total = 0;
                $id = $c_product->getId();
                $product = Mage::getModel('catalog/product')->load($id);
                if ($product->getIsInStock()==0) 
                    continue;
                else{
                    $childProducts = Mage::getModel('catalog/product_type_configurable')
                        ->getUsedProducts(null,$product);
                    foreach ($childProducts as $simple) {
                            $stock = round(Mage::getModel('cataloginventory/stock_item')->loadByProduct($simple)->getQty());
                            if($stock>0)
                                $cal_total++;

                    }
                    
                }
                if($size_action=='gteq' && $cal_total >= $size_total && $cal_total>0 && (int)$p_total > $total){
                   $this->_finalArrayList[$cat][] =  $id;
                   $this->_productsInCat[$cat][$i][] = $id;
                   $total++;
                }
                else if($size_action=='lteq' && $cal_total <= $size_total && $cal_total>0 && (int)$p_total > $total){
                   $this->_finalArrayList[$cat][] =  $id;
                   $this->_productsInCat[$cat][$i][] = $id;
                   $total++;
                }
                else if($size_action=='lt' && $cal_total < $size_total && $cal_total>0 && (int)$p_total > $total){
                   $this->_finalArrayList[$cat][] =  $id;
                   $this->_productsInCat[$cat][$i][] = $id;
                   $total++;
                }
                else if($size_action=='gt' && $cal_total > $size_total && $cal_total>0 && (int)$p_total > $total){
                   $this->_finalArrayList[$cat][] =  $id;
                   $this->_productsInCat[$cat][$i][] = $id;
                   $total++;
                }
                else if($size_action=='eq' && $cal_total == $size_total && $cal_total>0 && (int)$p_total > $total){
                   $this->_finalArrayList[$cat][] =  $id;
                   $this->_productsInCat[$cat][$i][] = $id;
                   $total++;
                }

            }
        }


        
    }

    protected function _getStartPosition($category,$total_products){
        $max_position = array();
        foreach($category as $cat){

            $_positions = Mage::getModel('catalog/category')->load($cat)
                    ->getProductsPosition();  
            $max_position[] =  max($_positions);
        }
         return max($max_position)+$total_products;     
    }
    protected function _getCategoryCollection($cat,$sort_attr_array,$cat_attr,$gender_attr,$i){
        $category = Mage::getModel('catalog/category')->load($cat);
        $collection = Mage::getResourceModel('catalog/product_collection')
                        ->setStoreId(Mage::app()->getStore()->getId())
                        ->addCategoryFilter($category)
                        ->addAttributeToFilter('type_id', array('eq' => 'configurable'))
                        ->addAttributeToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED);                              
        if($cat_attr) {
           $collection->addAttributeToFilter('product_category', array(array(
                array('attribute'=>'product_category', 'finset'=>$cat_attr)
           )));
        } 
        if($gender_attr) {
           $collection->addAttributeToFilter('gender', array('eq' =>$gender_attr));
        }               
            
        $collection->addAttributeToSort($sort_attr_array['attr'],$sort_attr_array['order']);                           
        $this->_productCollectionByCategory[$cat][$i] =  $collection;          
        return $collection;                
    }

    protected function _sortAttrArray($type){
        $sort_attr = array();
        switch ($type) {
            case "price_high":
                $sort_attr['attr'] ='price';
                $sort_attr['order'] = 'DESC';
                break;
            case "discount":
               $sort_attr['attr'] ='discount';
                $sort_attr['order'] = 'DESC';
                break;
            case "price_low":
                $sort_attr['attr'] ='price';
                $sort_attr['order'] = 'ASC';
                break;
            case "updated_at":
                $sort_attr['attr'] ='updated_at';
                $sort_attr['order'] = 'DESC';
                break;    
            default:
                $sort_attr['attr'] ='price';
                $sort_attr['order'] = 'DESC';
        }
        return $sort_attr;

    }

    
    
}