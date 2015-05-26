<?php

class MLogix_Gallery_Block_Dayrightblock extends MLogix_Gallery_Block_Day {

    public function _construct() {
        parent::_construct();
        return $this->setTemplate('gallery/dayrightblock.phtml');
    }

    public function getGalleryEditorsPick($day_title) {

        $galleryData = Mage::getModel('gallery/day')->loadByItemTitle($day_title)->getData();

        $allIDs = explode(",", $galleryData['related_products_sku']);
        $storeId = Mage::app()->getStore()->getId();
        $products = Mage::getModel('catalog/product')->getCollection()
                        ->addAttributeToFilter('sku', array('in' => $allIDs))
                        ->addAttributeToFilter('type_id', 'configurable')
                        ->addFieldToFilter("status", 1)
                        ->addStoreFilter($storeId)
                        ->addAttributeToFilter('visibility', 4);
        
        $mainIDs = array();
        foreach ($products as $prod) {            
            if ($prod->isSaleable()) {
                $mainIDs[] = trim($prod->getId());
            }
        }

        return $mainIDs;
    }

}
