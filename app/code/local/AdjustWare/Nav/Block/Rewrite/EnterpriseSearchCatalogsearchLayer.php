<?php
/**
 * Product:     Layered Navigation Pro for Enterprise Edition - 16/08/12
 * Package:     AdjustWare_Nav_10.4.9_10.0.0_557110
 * Purchase ID: hXgQU3oI8FOfJ8PDCk5s6h6XPM5oH2Kx0N6TCAPkqN
 * Generated:   2013-04-22 06:59:44
 * File path:   app/code/local/AdjustWare/Nav/Block/Rewrite/EnterpriseSearchCatalogsearchLayer.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'AdjustWare_Nav')){ kUrqBiwjDyIZBmrk('99e818f16e7d8c672cdf58c0bc8f4011'); ?><?php
class AdjustWare_Nav_Block_Rewrite_EnterpriseSearchCatalogsearchLayer extends Enterprise_Search_Block_Catalogsearch_Layer
{
    protected function _construct()
    {
        Mage::unregister('current_layer'); 
        parent::_construct();
        
    }
} } 