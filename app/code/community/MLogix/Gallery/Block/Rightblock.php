<?php

class MLogix_Gallery_Block_Rightblock extends MLogix_Gallery_Block_Week {

    public function _construct() {
        parent::_construct();
        return $this->setTemplate('gallery/rightblock.phtml');
    }

    public function getGalleryEditorsPick($week_title) {

        $galleryData = Mage::getModel('gallery/week')->loadByItemTitle($week_title)->getData();

        $allIDs = explode(",", $galleryData['related_products_sku']);
        $storeId = Mage::app()->getStore()->getId();
        $products = Mage::getModel('catalog/product')->getCollection()
                        ->addAttributeToFilter('sku', array('in' => $allIDs))
                        ->addAttributeToFilter('type_id', 'configurable')
                        ->addFieldToFilter("status", 1)
                        ->addStoreFilter($storeId)
                        ->addAttributeToFilter('visibility', 4);

        foreach ($products as $prod) {
            if ($prod->isSaleable()) {
                $mainIDs[] = trim($prod->getId());
            }
        }

        return $mainIDs;
    }

}
