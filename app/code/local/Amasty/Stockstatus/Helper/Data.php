<?php
/**
* @copyright Amasty.
*/
class Amasty_Stockstatus_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function show($product)
    {
        return Mage::app()->getLayout()->createBlock('amstockstatus/status')->setProduct($product)->toHtml();
    }
    
    public function processViewStockStatus($product, $html)
    {
        $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);
        if ( (!Mage::getStoreConfig('catalog/general/displayforoutonly') || !$product->isSaleable()) || ($product->isInStock() && $stockItem->getData('qty') <= Mage::helper('amstockstatus')->getBackorderQnt() ) )
        {
            if (Mage::helper('amstockstatus')->getCustomStockStatusText($product))
            {
                // regexp
                $inStock   = Mage::helper('amstockstatus')->__('In stock') . '.?';
                $outStock  = Mage::helper('amstockstatus')->__('Out of stock') . '.?';

                                                                // leave empty space here
                                                                //            v
                $status = Mage::getStoreConfig('catalog/general/icononly') ? ' ' : Mage::helper('amstockstatus')->getCustomStockStatusText($product);

                if ($status)
                {
                    $status = '<span class="amstockstatus_' . Mage::helper('amstockstatus')->getCustomStockStatusId($product) . '">' . $status . '</span>';
                    if (Mage::getStoreConfig('catalog/general/icononly') || $product->getData('hide_default_stock_status') || (!$product->isConfigurable() && ('bundle' != $product->getTypeId()) && $product->isInStock() && $stockItem->getManageStock() && 0 == $stockItem->getData('qty')))
                    {
                        $html = preg_replace("@($inStock|$outStock)[\s]*<@", '' . $status . '<', $html);
                    }
                    else 
                    {
                        $html = preg_replace("@($inStock|$outStock)[\s]*<@", '$1 ' . $status . '<', $html);
                    }
                }
                
                // adding icon if any
                $availability = Mage::helper('amstockstatus')->__('Availability:');
                $html = str_replace($availability, $availability . $this->getStatusIconImage($product), $html);
            }
        }
        return $html;
    }
    
    public function getStatusIconImage($product)
    {
        $iconHtml = '';
        $altText  = '';
        if ($iconUrl = $this->getStatusIconUrl(Mage::helper('amstockstatus')->getCustomStockStatusId($product)))
        {
            if (!Mage::getStoreConfig('catalog/general/alt_text_loggedin') || Mage::getSingleton('customer/session')->isLoggedIn())
            {
                $altText  = Mage::getStoreConfig('catalog/general/alt_text');
            }
            if (false !== strpos($altText, '{qty}'))
            {
                $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);
                $altText   = str_replace('{qty}', intval($stockItem->getData('qty')), $altText);
            }
            $bubble       = Mage::getBaseUrl('js') . 'amasty/amstockstatus/bubble.gif';
            $bubbleFiller = Mage::getBaseUrl('js') . 'amasty/amstockstatus/bubble_filler.gif';
            if ($altText)
            {
                $iconHtml .= <<<INLINECSS
                <style type="text/css">
                /*---------- bubble tooltip -----------*/
                span.tt{
                    position:relative;
                    z-index:950;
                    color:#3CA3FF;
                	font-weight:bold;
                    text-decoration:none;
                }
                span.tt span{ display: none; }
                /*background:; ie hack, something must be changed in a for ie to execute it*/
                span.tt:hover{ z-index:25; color: #aaaaff; background:;}
                span.tt:hover span.tooltip{
                    display:block;
                    position:absolute;
                    top:0px; left:0;
                	padding: 15px 0 0 0;
                	width:200px;
                	color: #3f3f3f;
                	font-size: 12px;
                    text-align: center;
                	filter: alpha(opacity:95);
                	KHTMLOpacity: 0.95;
                	MozOpacity: 0.95;
                	opacity: 0.95;
                }
                span.tt:hover span.top{
                	display: block;
                	padding: 30px 8px 0;
                    background: url($bubble) no-repeat top;
                }
                span.tt:hover span.middle{ /* different middle bg for stretch */
                	display: block;
                	padding: 0 8px; 
                	background: url($bubbleFiller) repeat bottom; 
                }
                span.tt:hover span.bottom{
                	display: block;
                	padding:3px 8px 10px;
                	color: #548912;
                    background: url($bubble) no-repeat bottom;
                }
                </style>
INLINECSS;
            }
            if ($altText)
            {
                $altText = '<span class="tooltip"><span class="top"></span><span class="middle"><strong>' . $altText . '</strong></span><span class="bottom"></span></span>';
            }
            $iconHtml .= ' <span class="tt"><img src="' . $iconUrl . '" class="amstockstatus_icon" alt="" title="">' . $altText . '</span> ';
        }
        return $iconHtml;
    }
    
    public function getCustomStockStatusText(Mage_Catalog_Model_Product $product)
    {
        $status      = '';
        $rangeStatus = Mage::getModel('amstockstatus/range');
        $stockItem   = null;
        
        if ($product->getData('custom_stock_status_qty_based') || $product->getData('custom_stock_status_quantity_based'))
        {
            $stockItem   = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);
            $rangeStatus->loadByQty($stockItem->getData('qty'));
        }
        
        if ($rangeStatus->hasData('status_id'))
        {
            // gettins status for range
            $attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', 'custom_stock_status');
            foreach ( $attribute->getSource()->getAllOptions(true, false) as $option )
            {
                if ($rangeStatus->getData('status_id') == $option['value'])
                {
                    $status = $option['label'];
                    break;
                }
            }
        } elseif (!Mage::getStoreConfig('catalog/general/userangesonly')) 
        {
            $status = $product->getAttributeText('custom_stock_status');
        }
        
        if (false !== strpos($status, '{qty}'))
        {
        	if (!$stockItem)
        	{
        		$stockItem   = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);
        	}
        	$status = str_replace('{qty}', intval($stockItem->getData('qty')), $status);
        }
        
        // search for atttribute entries
        preg_match_all('@\{(.+?)\}@', $status, $matches);
        if (isset($matches[1]) && !empty($matches[1]))
        {
            foreach ($matches[1] as $match)
            {
                if ($value = $product->getData($match))
                {
                    if (preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})/", $value))
                    {
                        $format = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);
                        $value = Mage::getSingleton('core/locale')->date($value, null, null, false)->toString($format);
                    }
                    $status = str_replace('{' . $match . '}', $value, $status);
                }
            }
        }

        return $status;
    }
    
    public function getCustomStockStatusId(Mage_Catalog_Model_Product $product)
    {
        $statusId    = null;
        $rangeStatus = Mage::getModel('amstockstatus/range');
        
        if ($product->getData('custom_stock_status_qty_based') || $product->getData('custom_stock_status_quantity_based'))
        {
            $stockItem   = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);
            $rangeStatus->loadByQty($stockItem->getData('qty'));
        }
        
        if ($rangeStatus->hasData('status_id'))
        {
            $statusId = $rangeStatus->getData('status_id');
        } elseif (!Mage::getStoreConfig('catalog/general/userangesonly')) 
        {
            $statusId = $product->getData('custom_stock_status');
        }
        
        return $statusId;
    }
    
    public function getBackorderQnt()
    {
        return 0;
    }
    
    public function getStatusIconUrl($optionId)
    {
        $uploadDir = Mage::getBaseDir('media') . DIRECTORY_SEPARATOR . 
                                                    'amstockstatus' . DIRECTORY_SEPARATOR . 'icons' . DIRECTORY_SEPARATOR;
        if (file_exists($uploadDir . $optionId . '.jpg'))
        {
            return Mage::getBaseUrl('media') . '/' . 'amstockstatus' . '/' . 'icons' . '/' . $optionId . '.jpg';
        }
        return '';
    }
    
    public function getStockAlert($product)
    {
        if (!$product->getId() || !Mage::getStoreConfig('catalog/general/stockalert')) // this is the extension's setting.
        {
            return '';
        }
        
        $tempCurrentProduct = Mage::registry('current_product');
        Mage::unregister('product');
        Mage::unregister('current_product');
        Mage::register('current_product', $product);
        Mage::register('product', $product);
        
        $alertBlock = Mage::app()->getLayout()->createBlock('productalert/product_view', 'productalert.stock.'.$product->getId());
        
        if ($alertBlock)
        {
            $alertBlock->setTemplate('productalert/product/view.phtml');
            $alertBlock->prepareStockAlertData();
            $alertBlock->setHtmlClass('alert-stock link-stock-alert');
            $alertBlock->setSignupLabel($this->__('Sign up to get notified when this configuration is back in stock'));
            $html = $alertBlock->toHtml();

            Mage::unregister('product');
            Mage::unregister('current_product');
            Mage::register('current_product', $tempCurrentProduct);
            Mage::register('product', $tempCurrentProduct);
            
            return $html;
        }
        
        Mage::unregister('product');
        Mage::unregister('current_product');
        Mage::register('current_product', $tempCurrentProduct);
        Mage::register('product', $tempCurrentProduct);
            
        return '';
    }
}
