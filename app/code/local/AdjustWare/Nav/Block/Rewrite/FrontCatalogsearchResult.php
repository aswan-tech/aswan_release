<?php
/**
 * Product:     Layered Navigation Pro for Enterprise Edition - 16/08/12
 * Package:     AdjustWare_Nav_10.4.9_10.0.0_557110
 * Purchase ID: hXgQU3oI8FOfJ8PDCk5s6h6XPM5oH2Kx0N6TCAPkqN
 * Generated:   2013-04-22 06:59:44
 * File path:   app/code/local/AdjustWare/Nav/Block/Rewrite/FrontCatalogsearchResult.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'AdjustWare_Nav')){ mREwjpoZaMTrjgEm('3de73cd6560d1ee09a184fde18168e2f'); ?><?php
class AdjustWare_Nav_Block_Rewrite_FrontCatalogsearchResult extends Mage_CatalogSearch_Block_Result
{
    /**
     * Retrieve Search result list HTML output, wrapped with <div>
     *
     * @return string
     */
    public function getProductListHtml()
    {
        $html = parent::getProductListHtml();
        $html = Mage::helper('adjnav')->wrapProducts($html);
        return $html;
    }
    
    /**
     * Set Search Result collection
     *
     * @return Mage_CatalogSearch_Block_Result
     */ 
    public function setListCollection()
    {
//        $this->getListBlock()
//           ->setCollection($this->_getProductCollection());
        return $this; 
    }
    
    /**
     * Retrieve loaded category collection
     *
     * @return Mage_CatalogSearch_Model_Mysql4_Fulltext_Collection
     */
    protected function _getProductCollection()
    {
        if (is_null($this->_productCollection)) 
        {
            $this->_productCollection = Mage::getSingleton('enterprise_search/search_layer')->getProductCollection();
        }
        return $this->_productCollection;
    }
    
    public function getAdditionalHtml() 
    {
        $html = parent::getAdditionalHtml();
        $html = Mage::helper('adjnav')->wrapProducts($html);
        return $html;
    }
    
} } 