<?php
/**
 * Product:     Layered Navigation Pro for Enterprise Edition - 16/08/12
 * Package:     AdjustWare_Nav_10.4.9_10.0.0_557110
 * Purchase ID: hXgQU3oI8FOfJ8PDCk5s6h6XPM5oH2Kx0N6TCAPkqN
 * Generated:   2013-04-22 06:59:44
 * File path:   app/code/local/AdjustWare/Nav/Block/Rewrite/FrontCatalogCategoryView.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'AdjustWare_Nav')){ yTkheoiDZmUaeMky('a16de3e97af56196315e65cbaf148024'); ?><?php
class AdjustWare_Nav_Block_Rewrite_FrontCatalogCategoryView extends Mage_Catalog_Block_Category_View
{
    public function getProductListHtml()
    {        
        $html = parent::getProductListHtml();
        if (parent::getCurrentCategory()->getIsAnchor()){
            $html = Mage::helper('adjnav')->wrapProducts($html);
        }
        return $html;
    }   
    
    public function getCmsBlockHtml()
    {
        if (Mage::helper('adjnav')->bNeedClearAll())
        {
            $html = parent::getProductListHtml();    
        } else {
            $html = parent::getCmsBlockHtml();         
        }
        
        if ($this->getCurrentCategory()->getIsAnchor() && $this->isContentMode()){
            $html = Mage::helper('adjnav')->wrapProducts($html);
        }
        return $html;
    }
    
    /**
     * Check if category display mode is "Static Block Only"
     * For anchor category with applied filter Static Block Only mode not allowed
     *
     * @return bool
     */
    public function isContentMode()
    {
        $res = parent::isContentMode();
        $category = $this->getCurrentCategory();
        $filters = Mage::helper('adjnav')->getParams();
        if ($res && $category->getIsAnchor() && sizeof($filters)>0) {
            $res = false;
        }
        return $res;
    }    


     /**
     * Retrieve current category model object
     *
     * @return Mage_Catalog_Model_Category
     */
    public function getCurrentCategory()
    {
        $categoryId =(int)$this->getRequest()->getQuery('cat');
        if(!$categoryId)
        {
            return parent::getCurrentCategory();
        }
        else
        {
            return Mage::getModel('catalog/category')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($categoryId);
        }
    }
    
} } 