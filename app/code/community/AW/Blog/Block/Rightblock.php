<?php

class AW_Blog_Block_Rightblock extends AW_Blog_Block_Abstract {

    public function _construct() {
        parent::_construct();
        return $this->setTemplate('aw_blog/rightblock.phtml');
    }

    public function getRecent() {

        if (Mage::getStoreConfig(AW_Blog_Helper_Config::XML_RECENT_SIZE) != 0) {
            $collection = Mage::getModel('blog/blog')->getCollection()
                            ->addPresentFilter()
                            ->addStoreFilter(Mage::app()->getStore()->getId())
                            ->setOrder('created_time ', 'desc');

            $route = Mage::helper('blog')->getRoute();

            Mage::getSingleton('blog/status')->addEnabledFilterToCollection($collection);
            $collection->setPageSize(Mage::getStoreConfig(AW_Blog_Helper_Config::XML_RECENT_SIZE));
            $collection->setCurPage(1);
            foreach ($collection as $item) {
                $item->setAddress($this->getUrl($route . "/" . $item->getIdentifier()));
            }
            return $collection;
        } else {
            return false;
        }
    }

    public function getPopular() {

        if (Mage::getStoreConfig(AW_Blog_Helper_Config::XML_POPULAR_SIZE) != 0) {
//            $collection = Mage::getModel('blog/blog')->getCollection()
//                            ->addFieldToFilter('comments', array('neq' => array('0')))
//                            ->addStoreFilter(Mage::app()->getStore()->getId())
//                            ->setOrder('comments', 'desc');

            $collection = Mage::getModel('blog/blog')->getCollection()
                            ->addFieldToSelect(array('title','identifier'))
                            ->addFieldToFilter('main_table.status', array('eq' => array('1')))
                            ->addFieldToFilter('cmnt.status', array('eq' => array('2')))
                            ->addStoreFilter(Mage::app()->getStore()->getId(), false)
                            ->setOrder('count(main_table.post_id)', 'desc');
            $collection->getSelect()->join(array('cmnt' => Mage::getSingleton('core/resource')->getTableName('blog/comment')), 'main_table.post_id = cmnt.post_id', array());
            $collection->getSelect()->group('main_table.post_id');
            
            $collection->setPageSize(Mage::getStoreConfig(AW_Blog_Helper_Config::XML_POPULAR_SIZE));
            $collection->setCurPage(1);

            $route = Mage::helper('blog')->getRoute();
            foreach ($collection as $item) {
                $item->setAddress($this->getUrl($route . "/" . $item->getIdentifier()));
            }
            return $collection;
        } else {
            return false;
        }
    }

    public function getBlogEditorsPick() {
        $identifier = Mage::app()->getRequest()->getParam('identifier');
        $blogData = Mage::getModel('blog/post')->loadByIdentifier($identifier)->getData();

        //"editors_pick_frontend" (is the extra column) which is updated while saving "," seperated SKUs from admin, it contains the IDs (NOT SKUs)
        $allIDs = explode(",", $blogData['editors_pick_frontend']);

        $storeId = Mage::app()->getStore()->getId();
        $products = Mage::getModel('catalog/product')->getCollection()
                        ->addAttributeToFilter('entity_id', array('in' => $allIDs))
                        ->addAttributeToFilter('type_id', 'configurable')
                        ->addFieldToFilter("status", 1)
                        ->addStoreFilter($storeId)
                        ->addAttributeToFilter('visibility', 4);
                        
        //print $products->getSelect();

        $i = 0;
        foreach ($products as $prod) {
            if ($i == 4) {
                //break;
            }

            if ($prod->isSaleable()) {
                $mainIDs[] = trim($prod->getId());

                $i++;
            }
        }

        return $mainIDs;
    }

}
