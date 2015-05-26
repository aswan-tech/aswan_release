<?php
/**
 * Product:     Layered Navigation Pro for Enterprise Edition - 16/08/12
 * Package:     AdjustWare_Nav_10.4.9_10.0.0_557110
 * Purchase ID: hXgQU3oI8FOfJ8PDCk5s6h6XPM5oH2Kx0N6TCAPkqN
 * Generated:   2013-04-22 06:59:44
 * File path:   app/code/local/AdjustWare/Nav/Block/Catalog/Layer/Filter/Category.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'AdjustWare_Nav')){ eAVqrcwyPjfMrDVe('779698b9fa2a0ea1391ab9e621faf22b'); ?><?php
class AdjustWare_Nav_Block_Catalog_Layer_Filter_Category extends Mage_Catalog_Block_Layer_Filter_Category
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('adjnav/filter_category.phtml');
        $this->_filterModelName = 'adjnav/catalog_layer_filter_category'; 
    }
   
    public function getVar(){
        return $this->_filter->getRequestVar();
    }

    public function addFacetCondition()
    {
        $this->_filter->addFacetCondition();
        return $this;
    }
    
    public function getClearUrl()
    {
        $url = '';
        $query = Mage::helper('adjnav')->getParams();
        if (!empty($query[$this->getVar()])){
            $query[$this->getVar()] = null;
            $url = Mage::getUrl('*/*/*', array(
                '_use_rewrite' => true, 
                '_query'       => $query,
             )); 
        }
        
        return $url;
    }

} } 