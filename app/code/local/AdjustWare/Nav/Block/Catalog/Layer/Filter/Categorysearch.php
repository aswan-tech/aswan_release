<?php
/**
 * Product:     Layered Navigation Pro for Enterprise Edition - 16/08/12
 * Package:     AdjustWare_Nav_10.4.9_10.0.0_557110
 * Purchase ID: hXgQU3oI8FOfJ8PDCk5s6h6XPM5oH2Kx0N6TCAPkqN
 * Generated:   2013-04-22 06:59:44
 * File path:   app/code/local/AdjustWare/Nav/Block/Catalog/Layer/Filter/Categorysearch.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'AdjustWare_Nav')){ kUrqBiwjDyIZBmrk('2ecfeb20031ae6ab44f80d233ccbd5af'); ?><?php
class AdjustWare_Nav_Block_Catalog_Layer_Filter_Categorysearch extends AdjustWare_Nav_Block_Catalog_Layer_Filter_Category
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('adjnav/filter_category_search.phtml');
        $this->_filterModelName = 'adjnav/catalog_layer_filter_categorysearch'; 
    }
    
    public function addFacetCondition()
    {
        $this->_filter->addFacetCondition();
        return $this;
    }
    
} } 