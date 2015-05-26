<?php
class AW_Blog_Block_Navigation extends AW_Blog_Block_Abstract {

    public function _construct() {
        parent::_construct();
        return $this->setTemplate('aw_blog/menu.phtml');
    }

    public function getCategories() {

        $collection = Mage::getModel('blog/cat')->getCollection()->addStoreFilter(Mage::app()->getStore()->getId(), false)->setOrder('sort_order ', 'asc');
        $route = Mage::helper('blog')->getRoute();

        foreach ($collection as $item) {
            $item->setAddress($this->getUrl($route . "/cat/" . $item->getIdentifier()));
        }
        return $collection;
    }
}
