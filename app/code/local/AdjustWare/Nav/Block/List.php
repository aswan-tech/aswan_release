<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'AdjustWare_Nav')){ BfOpaChkEeNmajOB('48b5757d6dc8bbebb633d987aa77c419'); ?>
<?php
class AdjustWare_Nav_Block_List extends Mage_Core_Block_Template {
    protected $_productCollection;
    protected $_module='catalog';
    protected $_solrModel = 'enterprise_search';
    public $_category = 0;

    public function getListBlock() {
        return $this->getChild('product_list');
    }

    protected function _isCacheActive() {
        if (Mage::getSingleton('core/session')->getMessages(true)->count() > 0) {
            return false;
        }
        return true;
    }

    public function getCacheLifetime() {
        if ($this->_isCacheActive()) {
            return false;
        }
    }

    public function getCacheKey() {
        if (!$this->_isCacheActive()) {
            parent::getCacheKey();
        }
        $this->_category = Mage::getSingleton('catalog/layer')->getCurrentCategory();
        $total = $this->_getProductCollection()->getSize();
        Mage::getSingleton('core/session')->setSizeValue('');
        $toolbar = new Mage_Catalog_Block_Product_List_Toolbar();
        $cacheKey = 'ProductList_' . $this->_category->getId().'_' . $toolbar->getCurrentOrder().'_'. $toolbar->getCurrentDirection().'_'.
            $toolbar->getCurrentMode().'_'. $toolbar->getCurrentPage().'_'. $toolbar->getLimit().'_'.$total . '_' . Mage::App()->getStore()->getCode();
            
        foreach (Mage::app()->getRequest()->getParams() as $key => $value) {
            if( is_array( $value )) $value = implode('_', $value);
            $realValue = '';
            $valueArray = array();
            $valueArray = explode( '_', $value);
            asort( $valueArray );
            $value = implode( '_', $valueArray );
            if(is_array($value)) {
                foreach($value as $k => $v) {
                    $realValue .= '_' . $v;
                }
            } else {
                $realValue = $value;
            }

            if(!in_array(strtolower($key), array('utm_source', 'utm_content', 'utm_campaign', 'utm_medium', 'utm_term', 'gclid'))) {
                $cacheKey .= "_" . $key . '-' . $realValue;
            }
        }
        return $cacheKey;
    }

    public function getCacheTags() {
        if (!$this->_isCacheActive()) {
            return parent::getCacheTags();
        }
        $cacheTags = array( Mage_Catalog_Model_Category::CACHE_TAG, Mage_Catalog_Model_Category::CACHE_TAG.'_'.$this->_category->getId() );
        foreach ($this->_getProductCollection() as $_product) {
            $cacheTags[] = Mage_Catalog_Model_Product::CACHE_TAG."_".$_product->getId();
        }
        return $cacheTags;
    }

    public function setListOrders() {
        $category = Mage::getSingleton('catalog/layer')->getCurrentCategory();
        $availableOrders = $category->getAvailableSortByOptions();
        
        if ('catalogsearch' != $this->_module) {
            $sortBy = $this->getRequest()->getParam('sort', $category->getDefaultSortBy());
            $this->getListBlock()->setAvailableOrders($availableOrders)->setSortBy($sortBy);
        } else {
            unset($availableOrders['position']);
            $availableOrders = array_merge( array( 'relevance' => $this->__('Relevance') ), $availableOrders);
            $this->getListBlock()->setAvailableOrders($availableOrders)->setSortBy('relevance');
        }
        return $this;
    }

    public function setListModes() {
        $this->getListBlock()->setModes(array('grid' => $this->__('Grid'), 'list' => $this->__('List')));
        return $this;
    }
    
    public function setIsSearchMode() {
        $this->_module = 'catalogsearch';
        return $this;
    }

    public function setListCollection() {
        $this->getListBlock()->setCollection($this->_getProductCollection());
        return $this;
    }

    protected function _toHtml() {
        $this->setListOrders();
        $this->setListModes();
        $this->setListCollection();
        $html = $this->getChildHtml('product_list');
        $html = Mage::helper('adjnav')->wrapProducts($html);
        return $html;
    }

    protected function _getProductCollection() {
        if (is_null($this->_productCollection)) {
            $this->_productCollection = Mage::getSingleton(Mage::helper('adjnav')->getLayerModelName())->getProductCollection();
        }

        if($this->_module != 'catalogsearch') {
            Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($this->_productCollection);
            Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($this->_productCollection);
            if (!is_null($this->_productCollection)) {
                $visibleIds = Mage::getSingleton('catalog/product_visibility')->getVisibleInCatalogIds();
                $this->_productCollection->addAttributeToFilter('visibility',$visibleIds);
            }
        } else {
            Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($this->_productCollection);
            Mage::getSingleton('catalog/product_visibility')->addVisibleInSearchFilterToCollection($this->_productCollection);
            if (!is_null($this->_productCollection)) {
                $visibleIds = Mage::getSingleton('catalog/product_visibility')->getVisibleInSearchIds();
                $this->_productCollection->addAttributeToFilter('visibility',$visibleIds);
            }
        }
        return $this->_productCollection;
    }
} }