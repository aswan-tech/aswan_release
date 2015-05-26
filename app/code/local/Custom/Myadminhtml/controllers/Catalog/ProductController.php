<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Catalog product controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
 
require_once 'Mage/Adminhtml/controllers/Catalog/ProductController.php';

class Custom_Myadminhtml_Catalog_ProductController extends Mage_Adminhtml_Catalog_ProductController
{
    /**
     * Initialize product before saving
     */
    protected function _initProductSave()
    {
        $product     = $this->_initProduct();
        $productData = $this->getRequest()->getPost('product');
		
        if ($productData) {
            $this->_filterStockData($productData['stock_data']);
        }

        /**
         * Websites
         */
        if (!isset($productData['website_ids'])) {
            $productData['website_ids'] = array();
        }

        $wasLockedMedia = false;
        if ($product->isLockedAttribute('media')) {
            $product->unlockAttribute('media');
            $wasLockedMedia = true;
        }
		$attributeId = Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product', 'is_sale');
		$attribute = Mage::getModel('catalog/resource_eav_attribute')->load($attributeId);
		$attributeOptions = $attribute->getSource()->getAllOptions();
		$setValue = '';
		$unsetValue = '';
		foreach ($attributeOptions as $option) {
			if ($option['label'] == 'Yes') {
				$setValue = $option['value'];
			}
			if ($option['label'] == 'No') {
				$unsetValue = $option['value'];
			}
		}
		if(isset($productData['special_price']) && $productData['special_price'] != ''){
			$currentDate = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
			$currentDate = date("Y-m-d h:m:s", $currentDate);
			$specialToDate = '';
			if(isset($productData['special_to_date'])){
				$specialToDate = $productData['special_to_date'];
			}
			$specialFromDate = '';
			if(isset($productData['special_from_date'])){
				$specialFromDate = $productData['special_from_date'];
			}
			if ($currentDate >= $specialFromDate && ($currentDate < $specialToDate || $specialToDate == "")) {
					$productData['is_sale'] = $setValue;
			}else{
				/* Check for any promotional price if available for product */
				
				/* Setting store as frontend to get any promotional price active for this product */
				
				if($product->getEntityId()){
					Mage::app()->setCurrentStore(Mage_Core_Model_App::DISTRO_STORE_ID);
					Mage::app()->loadAreaPart(Mage_Core_Model_App_Area::AREA_FRONTEND, Mage_Core_Model_App_Area::PART_EVENTS);
					$final_price = (float)Mage::getModel('catalog/product')->load($product->getEntityId())->getFinalPrice();
				
					$product_price = (float)$productData['price'];
				
					if($product_price > $final_price){
						$productData['is_sale'] = $setValue;
					}else{
						$productData['is_sale'] = $unsetValue;
					}
				
					/* Setting store back to admin for this product */
					Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);	
					Mage::app()->loadAreaPart(Mage_Core_Model_App_Area::AREA_ADMIN, Mage_Core_Model_App_Area::PART_EVENTS);
				}
			}
		}else{
				/* Check for any promotional price if available for product */
				
				/* Setting store as frontend to get any promotional price active for this product */
				
				if($product->getEntityId()){
					Mage::app()->setCurrentStore(Mage_Core_Model_App::DISTRO_STORE_ID);
					Mage::app()->loadAreaPart(Mage_Core_Model_App_Area::AREA_FRONTEND, Mage_Core_Model_App_Area::PART_EVENTS);
					$final_price = (float)Mage::getModel('catalog/product')->load($product->getEntityId())->getFinalPrice();
				
					$product_price = (float)$productData['price'];
				
					if($product_price > $final_price){
						$productData['is_sale'] = $setValue;
					}else{
						$productData['is_sale'] = $unsetValue;
					}
				
					/* Setting store back to admin for this product */
					Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);	
					Mage::app()->loadAreaPart(Mage_Core_Model_App_Area::AREA_ADMIN, Mage_Core_Model_App_Area::PART_EVENTS);
				}
		}
		
		$categoryIds_temp = $this->getRequest()->getPost('category_ids');
		
		/* Custom code to set the value of catalogname attribute for categories associated by a user */
		if (null !== $categoryIds_temp) {
           $_category_temp = explode(",",$categoryIds_temp);
		   $_stringStored = "";
			if(is_array($_category_temp)){
				foreach($_category_temp as $_catId){
					$_catobject = Mage::getModel('catalog/category')->load($_catId);
					if(is_object($_catobject) && $_catobject->getIsActive()==1){
						$_catName = $_catobject->getName();
						$_stringStored .= $_catName.',';
					}
				}
			}
			if($_stringStored != ""){
				$_stringStored = substr($_stringStored,0,strlen($_stringStored)-1);
			}
			$productData['catalogname'] = strtolower($_stringStored);
		}		
		/* custom code ends */
		
        $product->addData($productData);
		
        if ($wasLockedMedia) {
            $product->lockAttribute('media');
        }

        if (Mage::app()->isSingleStoreMode()) {
            $product->setWebsiteIds(array(Mage::app()->getStore(true)->getWebsite()->getId()));
        }

        /**
         * Create Permanent Redirect for old URL key
         */
        if ($product->getId() && isset($productData['url_key_create_redirect']))
        // && $product->getOrigData('url_key') != $product->getData('url_key')
        {
            $product->setData('save_rewrites_history', (bool)$productData['url_key_create_redirect']);
        }

        /**
         * Check "Use Default Value" checkboxes values
         */
        if ($useDefaults = $this->getRequest()->getPost('use_default')) {
            foreach ($useDefaults as $attributeCode) {
                $product->setData($attributeCode, false);
            }
        }

        /**
         * Init product links data (related, upsell, crosssel)
         */
        $links = $this->getRequest()->getPost('links');
        if (isset($links['related']) && !$product->getRelatedReadonly()) {
            $product->setRelatedLinkData(Mage::helper('adminhtml/js')->decodeGridSerializedInput($links['related']));
        }
        if (isset($links['upsell']) && !$product->getUpsellReadonly()) {
            $product->setUpSellLinkData(Mage::helper('adminhtml/js')->decodeGridSerializedInput($links['upsell']));
        }
        if (isset($links['crosssell']) && !$product->getCrosssellReadonly()) {
            $product->setCrossSellLinkData(Mage::helper('adminhtml/js')
                ->decodeGridSerializedInput($links['crosssell']));
        }
        if (isset($links['grouped']) && !$product->getGroupedReadonly()) {
            $product->setGroupedLinkData(Mage::helper('adminhtml/js')->decodeGridSerializedInput($links['grouped']));
        }

        /**
         * Initialize product categories
         */
        $categoryIds = $this->getRequest()->getPost('category_ids');
        if (null !== $categoryIds) {
            if (empty($categoryIds)) {
                $categoryIds = array();
            }
            $product->setCategoryIds($categoryIds);
        }
		
		

        /**
         * Initialize data for configurable product
         */
        if (($data = $this->getRequest()->getPost('configurable_products_data'))
            && !$product->getConfigurableReadonly()
        ) {
            $product->setConfigurableProductsData(Mage::helper('core')->jsonDecode($data));
        }
        if (($data = $this->getRequest()->getPost('configurable_attributes_data'))
            && !$product->getConfigurableReadonly()
        ) {
            $product->setConfigurableAttributesData(Mage::helper('core')->jsonDecode($data));
        }

        $product->setCanSaveConfigurableAttributes(
            (bool) $this->getRequest()->getPost('affect_configurable_product_attributes')
                && !$product->getConfigurableReadonly()
        );

        /**
         * Initialize product options
         */
        if (isset($productData['options']) && !$product->getOptionsReadonly()) {
            $product->setProductOptions($productData['options']);
        }

        $product->setCanSaveCustomOptions(
            (bool)$this->getRequest()->getPost('affect_product_custom_options')
            && !$product->getOptionsReadonly()
        );

        Mage::dispatchEvent(
            'catalog_product_prepare_save',
            array('product' => $product, 'request' => $this->getRequest())
        );

        return $product;
    }
}
