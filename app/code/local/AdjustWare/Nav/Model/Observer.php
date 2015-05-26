<?php
/**
 * Product:     Layered Navigation Pro for Enterprise Edition - 16/08/12
 * Package:     AdjustWare_Nav_10.4.9_10.0.0_557110
 * Purchase ID: hXgQU3oI8FOfJ8PDCk5s6h6XPM5oH2Kx0N6TCAPkqN
 * Generated:   2013-04-22 06:59:44
 * File path:   app/code/local/AdjustWare/Nav/Model/Observer.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'AdjustWare_Nav')){ yTkheoiDZmUaeMky('f2d6ee076f9d72ce8513ac735a0b6fdc'); ?><?php

class AdjustWare_Nav_Model_Observer
{
    /** Add filters after all filters have applied for configurable products
     * 
     * @param Varien_Event_Observer $observer
     * @author ksenevich@aitoc.com
     */
    public function onCatalogProductCollectionLoadBefore(Varien_Event_Observer $observer)
    {
        /* @var $versionHelper AdjustWare_Nav_Helper_Version */
        $versionHelper = Mage::helper('adjnav/version');

        /* @var $collection Varien_Data_Collection_Db */
        $collection = $observer->getEvent()->getCollection();
        $adapter    = $collection->getConnection();
		$helper = Mage::helper('adjnav');

        $filterAttributes     = AdjustWare_Nav_Model_Catalog_Layer_Filter_Attribute::getFilterAttributes();
        $filterProducts       = AdjustWare_Nav_Model_Catalog_Layer_Filter_Attribute::getFilterProducts();
        $configurableProducts = array();
        $childByAttribute     = array();
        $child2parent         = array();
        $productModel         = Mage::getModel('catalog/product')->getResource();
        $attributesCount      = count($filterAttributes);

        if ($versionHelper->hasConfigurableFix())
        {
            foreach ($filterAttributes as $attributeId => $attributeValues)
            {
                $configurableQuery = new Varien_Db_Select($adapter);
                $configurableQuery
                    ->from(array('e' => $productModel->getTable('catalog/product')), 'entity_id')
                    ->join(array('l' => $productModel->getTable($versionHelper->getProductRelationTable())), 'l.parent_id = e.entity_id', array('child_id' => $versionHelper->getProductIdChildColumn()))
                    ->join(array('a' => Mage::getResourceModel('adjnav/catalog_product_indexer_configurable')->getMainTable()), 'a.entity_id = l.'.$versionHelper->getProductIdChildColumn(), array())
                    ->where('e.type_id = ?', Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE)
                    ->where('a.store_id = ?', Mage::app()->getStore()->getId())
                    ->where('a.attribute_id = ?', $attributeId)
                    ->where('a.value IN (?)', $attributeValues)
                    ->group(array('e.entity_id', 'l.'.$versionHelper->getProductIdChildColumn(), 'a.store_id'));

                $statement = $adapter->query($configurableQuery);
                while ($row = $statement->fetch(PDO::FETCH_ASSOC))
                {
                    $child2parent[$row['child_id']][] = $row['entity_id'];
                    $childByAttribute[$row['child_id']][$attributeId] = true;
                }
            }

            foreach ($childByAttribute as $childId => $attributeIds)
            {
                if (count($attributeIds) == $attributesCount)
                {
                    $configurableProducts[] = $childId;
                    foreach ($child2parent[$childId] as $parentId)
                    {
                        $configurableProducts[] = $parentId;
                    }
                }
            }
        }

        $configurableProducts = array_unique($configurableProducts);

		/* Commenting this section as it was causing issue */
		
		/*
        foreach ($filterProducts as $attributeId => $filterProducts)
        {
            $alias          = 'attr_index_'.$attributeId;
            $filterProducts = array_merge($filterProducts, $configurableProducts);

            if (empty($filterProducts))
            {
                $filterProducts = array(-1);
            }

            $collection->getSelect()->where($alias.'.entity_id IN (?)', $filterProducts);
        }
		*/

        AdjustWare_Nav_Model_Catalog_Layer_Filter_Attribute::cleanFilterAttributes();
    }

    public function collectAttributeStats()
    {
        if (!Mage::helper('adjnav/featured')->isAutoRange())
        {
            return false;
        }

        $cron = Mage::getModel('adjnav/cron');

        if (!$cron->canRunJob('collect_attribute_stats'))
        {
            //return false;
        }

        Mage::getModel('adjnav/eav_entity_attribute_option_stat')->recalculateStat();

        $cron->setLastRun(now())->save();
    }

    /** Add Layered navigation pro scripts to brand page
     * 
     * @param Varien_Event_Observer $observer
     * @author ksenevich
     */
    public function onAitmanufacturersRenderAdjnav($observer)
    {
        $action = $observer->getEvent()->getAction();

        //$action->getLayout()->getUpdate()->addHandle('adjnav_head');
        $action->getLayout()->getBlock('head')->addCss('css/adjnav.css');
        $action->getLayout()->getBlock('head')->addItem('skin_js', Mage::helper('adjnav/version')->getVersionSkinJs());
        //$action->getLayout()->getBlock('head')->addJs('jquery/jquery.min.js');
        $action->getLayout()->getBlock('head')->addJs('jquery/aitoc.js');
        $action->getLayout()->getBlock('head')->addJs('jquery/jquery.ba-bbq.min.js');
    }
    
    public function onCoreBlockAbstractToHtmlAfter($observer)
    {
        $block = $observer->getBlock();
        $transport = $observer->getTransport();
        $html = $transport->getHtml();
        
        if ($block instanceof Mage_Catalog_Block_Product_List) 
        {
            $additionalHtml = '';
            if (Mage::helper('adjnav')->isPageAutoload() && 
                ($block->getToolbarBlock()->getLastPageNum() > 1))
            {
                if ($block->getToolbarBlock()->getCurrentPage() < $block->getToolbarBlock()->getLastPageNum())
                { 
                    $additionalHtml .= '<div class="adjnav-page-autoload-pholder"></div>';
                }
                $additionalHtml .= '<div class="adjnav-page-autoload-progress" style="display:none"><img src="http://static.americanswan.com/Lecom_Magento/media/v3/loader-signup.gif" />
				<span style="left: 5px;position: relative;top:14px;">Loading More Products</span></div>';
                $category = Mage::registry('current_category');
                $columnCount = false;
                if ($category)
                {
                    $pageLayout = Mage::helper('adjnav')->getPageLayout($category);
                    $columnCount = $block->getColumnCountLayoutDepend($pageLayout);
                }
				
                $SBBStatus = Mage::helper('adjnav')->getShopByBrandsStatus();
                if ($SBBStatus->getIsInstalled()
                        && $SBBStatus->getIsEnabled()
                        && Mage::helper('aitmanufacturers')->canUseLayeredNavigation(Mage::registry('shopby_attribute'), true))
                {
                    $columnCount = $block->getData('column_count');
                }
                
                if (!$columnCount)
                {
                    $columnCount = $block->getData('column_count');
                }
                
                $additionalHtml .= '<input id="adjnav-page-column-count" type="hidden" value="' . $columnCount . '"/>';
                $additionalHtml .= '<input id="adjnav-page-product-limit" type="hidden" value="' . $block->getToolbarBlock()->getLimit() . '"/>';
            }
            $transport->setHtml($html . $additionalHtml);
        }
    }
} } 
