<?php
/**
 * Product:     Layered Navigation Pro for Enterprise Edition - 16/08/12
 * Package:     AdjustWare_Nav_10.4.9_10.0.0_557110
 * Purchase ID: hXgQU3oI8FOfJ8PDCk5s6h6XPM5oH2Kx0N6TCAPkqN
 * Generated:   2013-04-22 06:59:44
 * File path:   app/code/local/AdjustWare/Nav/controllers/CategoryController.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'AdjustWare_Nav')){ mREwjpoZaMTrjgEm('dbfa91d9801a1b948e22b383b5fa927d'); ?><?php
class AdjustWare_Nav_CategoryController extends Mage_Core_Controller_Front_Action
{
    public function viewAction()
    {
        // init category
        $categoryId = (int) $this->getRequest()->getParam('id', false);
        if (!$categoryId) {
            $this->_forward('noRoute'); 
            return;
        }

        $category = Mage::getModel('catalog/category')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($categoryId);
        Mage::register('current_category', $category);         
        
        
        $this->getLayout()->createBlock('adjnav/catalog_layer_view');        
        $this->loadLayout();
        $this->renderLayout();
    }
} } 