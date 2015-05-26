<?php
/**
* @copyright Amasty.
*/
class Amasty_Stockstatus_Block_Rewrite_Product_View_Type_Configurable extends Amasty_Stockstatus_Block_Rewrite_Product_View_Type_Configurable_Pure
{
    protected $_options;
    
    protected function _afterToHtml($html)
    {
        $html = parent::_afterToHtml($html);
        if ('product.info.options.configurable' == $this->getNameInLayout())
        {
            $html = '<script type="text/javascript" src="' . Mage::getBaseUrl('js') . 'amasty/amstockstatus/configurable.js"></script>'
                    . $html;
        
            $aStockStatus = array();
            $allProducts = $this->getProduct()->getTypeInstance(true)
                ->getUsedProducts(null, $this->getProduct());
            foreach ($allProducts as $product)
            {
                $key = array();
                for ($i = 0; $i < count($this->_options); $i++)
                {
                    foreach ($this->_options[$i] as $iOptionId => $productIds)
                    {
                        if (in_array($product->getId(), $productIds))
                        {
                            $key[] = $iOptionId;
                        }
                    }
                }
                
                $stockStatus = '';
                $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);
                if ( (!Mage::getStoreConfig('catalog/general/displayforoutonly') || !$product->isSaleable()) || ($product->isInStock() && $stockItem->getData('qty') <= Mage::helper('amstockstatus')->getBackorderQnt() ) )
                {
                    if ($product->getData('hide_default_stock_status') || ($product->isInStock() && 0 == $stockItem->getData('qty')))
                    {
                        $stockStatus = Mage::helper('amstockstatus')->getCustomStockStatusText($product);
                    } elseif (Mage::helper('amstockstatus')->getCustomStockStatusText($product))
                    {
                        if (!$product->isInStock())
                        {
                            $stockStatus = Mage::helper('amstockstatus')->__('Out of Stock') . ' - ' . Mage::helper('amstockstatus')->getCustomStockStatusText($product);
                        } else 
                        {
                            $stockStatus = Mage::helper('amstockstatus')->getCustomStockStatusText($product);
                        }
                    }
                }
                if ($key)
                {
                    $aStockStatus[implode(',', $key)] = array(
                        'is_in_stock'   => $product->isSaleable(),
                        'custom_status' => $stockStatus,
                        'is_qnt_0'      => (int)($product->isInStock() && $stockItem->getData('qty') <= Mage::helper('amstockstatus')->getBackorderQnt()),
                        'product_id'    => $product->getId(),
                        'stockalert'	=> Mage::helper('amstockstatus')->getStockAlert($product),
                    );
                }
            }
            foreach ($aStockStatus as $k=>$v){
                if (!$v['is_in_stock'] && !$v['custom_status']){
                    $v['custom_status'] = Mage::helper('amstockstatus')->__('Out of Stock');
                    $aStockStatus[$k] = $v;
                }   
            }
            $html .= '<script type="text/javascript">var stStatus = new StockStatus(' . Zend_Json::encode($aStockStatus) . '); spConfig.loadStatus();</script>';
        }
        $html = $this->helper('amstockstatus')->processViewStockStatus($this->getProduct(), $html);
        return $html;
    }
    
    public function getAllowProducts()
    {
        if (!$this->hasAllowProducts()) {
            $products = array();
            $allProducts = $this->getProduct()->getTypeInstance(true)
                ->getUsedProducts(null, $this->getProduct());
            foreach ($allProducts as $product) {
                /**
                * Should show all products (if setting set to Yes), but not allow "out of stock" to be added to cart
                */
                if ($product->isSaleable() || Mage::getStoreConfig('catalog/general/outofstock')) {
                    if ($product->getStatus() != Mage_Catalog_Model_Product_Status::STATUS_DISABLED)
                    {
                        $products[] = $product;
                    }
                }
            }
            $this->setAllowProducts($products);
        }
        return $this->getData('allow_products');
    }
    
    public function getJsonConfig()
    {
        $jsonConfig = parent::getJsonConfig();
        $config = Zend_Json::decode($jsonConfig);
        $i = 0;
        foreach ($config['attributes'] as $key1 => $attr)
        {
            foreach ($attr['options'] as $key2 => $options)
            {
                $this->_options[$i][$options['id']] = $options['products'];
                
                /**
                * this block is to handle 1-attribute products
                */
                if (1 == count($options['products']))
                {
                    $product = Mage::getModel('catalog/product')->load($options['products'][0]);
                    if (!$product->isSaleable())
                    {
                        if ($product->getData('hide_default_stock_status') && Mage::helper('amstockstatus')->getCustomStockStatusText($product))
                        {
                            $statusText = Mage::helper('amstockstatus')->getCustomStockStatusText($product);
                        } else 
                        {
                            $statusText = Mage::helper('catalog')->__('Out of Stock');
                            if (Mage::helper('amstockstatus')->getCustomStockStatusText($product))
                            {
                                $statusText .= ' - ' . Mage::helper('amstockstatus')->getCustomStockStatusText($product);
                            }
                        }
                        $config['attributes'][$key1]['options'][$key2]['label'] .= ' (' . $statusText . ')';
                    }
                }
                
            }
            $i++;
        }
        return Zend_Json::encode($config);
    }
}