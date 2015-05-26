<?php

/**
 * Productimport.php
 * Import configurable product, gallary image, simple product, tier price etc.
 *
 *  configurable product format (associated,	config_attributes,config_attribute_values)
 *  config_attribute_values : configurable attributes value format eg. "color:Red:0.00:Fixed|shirt_size:Small:0.00:Fixed"
 *  config_attributes : configurable attributes eg. "color, shirt_size"
 *  associated :  associated SKU eg. "AWSPAP-0020-Green-Small, AWSPAP-0020-Red-Large"
 *  Date: June 6, 2012					  
 *  @Author : Ajesh Prakash (ajesh.prakash@hcl.com)
 */
class Mage_Catalog_Model_Convert_Adapter_Productimport extends Mage_Catalog_Model_Convert_Adapter_Product {

    protected $_last_config_attributes = '';
    protected $_customFields = array('associated', 'config_attributes', 'config_attribute_values');

    private function _editTierPrices(&$product, $tier_prices_field = false) {

        if (($tier_prices_field) && !empty($tier_prices_field)) {

            if (trim($tier_prices_field) == 'REMOVE') {

                $product->setTierPrice(array());
            } else {

                //get current product tier prices
                $existing_tps = $product->getTierPrice();

                $etp_lookup = array();
                //make a lookup array to prevent dup tiers by qty
                foreach ($existing_tps as $key => $etp) {
                    $etp_lookup[intval($etp['price_qty'])] = $key;
                }

                //parse incoming tier prices string
                $incoming_tierps = explode('|', $tier_prices_field);
                $tps_toAdd = array();
                foreach ($incoming_tierps as $tier_str) {
                    if (empty($tier_str))
                        continue;

                    $tmp = array();
                    $tmp = explode('=', $tier_str);

                    if ($tmp[0] == 0 && $tmp[1] == 0)
                        continue;

                    $tps_toAdd[$tmp[0]] = array(
                        'website_id' => 0, // !!!! this is hard-coded for now Mage::app()->setCurrentStore('default');
                        'cust_group' => 32000, // !!! so is this
                        'price_qty' => $tmp[0],
                        'price' => $tmp[1],
                        'delete' => ''
                    );

                    //drop any existing tier values by qty
                    if (isset($etp_lookup[intval($tmp[0])])) {
                        unset($existing_tps[$etp_lookup[intval($tmp[0])]]);
                    }
                }
                //combine array
                $tps_toAdd = array_merge($existing_tps, $tps_toAdd);
                //save it
                $product->setTierPrice($tps_toAdd);
                //$product->setData('tier_price', $tps_toAdd);
            }
        }
    }

    /**
     * Save product (import)
     *
     * @param array $importData
     * @throws Mage_Core_Exception
     * @return bool
     */
    public function saveRow(array $importData) {
        $importData = array_map('trim', $importData);
        $product = $this->getProductModel();
        $product->setData(array());


        $product = $this->getProductModel()
                        ->reset();


        if ($stockItem = $product->getStockItem()) {
            $stockItem->setData(array());
        }

        if (empty($importData['store'])) {
            if (!is_null($this->getBatchParams('store'))) {
                $store = $this->getStoreById($this->getBatchParams('store'));
            } else {
                $message = Mage :: helper('catalog')->__('Skip import row, required field "%s" not defined', 'store');
                Mage :: throwException($message);
            }
        } else {
            $store = $this->getStoreByCode($importData['store']);
        }

        if ($store === false) {
            $message = Mage :: helper('catalog')->__('Skip import row, store "%s" field not exists', $importData['store']);
            Mage :: throwException($message);
        }

        if (empty($importData['sku'])) {
            $message = Mage :: helper('catalog')->__('Skip import row, required field "%s" not defined', 'sku');
            Mage :: throwException($message);
        }

        $product->setStoreId($store->getId());
        $productId = $product->getIdBySku($importData['sku']);
        $new = true; // fix for duplicating attributes error
        if ($productId) {
            $product->load($productId);
            $new = false; // fix for duplicating attributes error
        }
        $productTypes = $this->getProductTypes();
        $productAttributeSets = $this->getProductAttributeSets();

        // delete disabled products
        if ($importData['status'] == 'Disabled') {
//            $product = Mage :: getSingleton('catalog/product')->load($productId);
//            $this->_removeFile(Mage :: getSingleton('catalog/product_media_config')->getMediaPath($product->getData('image')));
//            $this->_removeFile(Mage :: getSingleton('catalog/product_media_config')->getMediaPath($product->getData('small_image')));
//            $this->_removeFile(Mage :: getSingleton('catalog/product_media_config')->getMediaPath($product->getData('thumbnail')));
//            $media_gallery = $product->getData('media_gallery');
//            foreach ($media_gallery['images'] as $image) {
//                $this->_removeFile(Mage :: getSingleton('catalog/product_media_config')->getMediaPath($image['file']));
//            }
//            $product->delete();
//            return true;
        }

        if (empty($importData['type']) || !isset($productTypes[strtolower($importData['type'])])) {
            $value = isset($importData['type']) ? $importData['type'] : '';
            $message = Mage :: helper('catalog')->__('Skip import row, is not valid value "%s" for field "%s"', $value, 'type');
            Mage :: throwException($message);
        }
        $product->setTypeId($productTypes[strtolower($importData['type'])]);

        if (empty($importData['attribute_set']) || !isset($productAttributeSets[$importData['attribute_set']])) {
            $value = isset($importData['attribute_set']) ? $importData['attribute_set'] : '';
            $message = Mage :: helper('catalog')->__('Skip import row, is not valid value "%s" for field "%s"', $value, 'attribute_set');
            Mage :: throwException($message);
        }
        $product->setAttributeSetId($productAttributeSets[$importData['attribute_set']]);

        foreach ($this->_requiredFields as $field) {
            $attribute = $this->getAttribute($field);
            if (!isset($importData[$field]) && $attribute && $attribute->getIsRequired()) {
                $message = Mage :: helper('catalog')->__('Skip import row, required field "%s" for new products not defined', $field);
                Mage :: throwException($message);
            }
            if ($importData[$field] == "") {
                $message = Mage :: helper('catalog')->__('Skip import row, required field "%s" for new products not defined', $field);
                Mage :: throwException($message);
            }
        }

        if($importData['product_type_id'] == 'configurable'){
            foreach ($this->_customFields as $field) {
                if (!isset($importData[$field])) {
                    $message = Mage :: helper('catalog')->__('Skip import row, required field "%s" for new products not defined', $field);
                    Mage :: throwException($message);
                }
                if ($importData[$field] == "") {
                    $message = Mage :: helper('catalog')->__('Skip import row, required field value "%s" for new products not defined', $field);
                    Mage :: throwException($message);
                }
            }
        }

        // check for attribute set any other than Default
        if ($importData['attribute_set'] != 'Default' && in_array($importData['attribute_set'], array_flip($productAttributeSets))) {

            if ($importData['config_attributes'] != '') {
                $this->_last_config_attributes = $importData['config_attributes'];
            }

            $config_attributes_array = explode(', ', $this->_last_config_attributes);
            foreach ($config_attributes_array as $config_attribute) {
                if ($config_attribute == 'size') {
                    $attribute_set_array[] = strtolower($importData['attribute_set']) . '_' . $config_attribute;
                    $importData['config_attribute_values'] = str_replace($config_attribute, strtolower($importData['attribute_set']) . '_' . $config_attribute, $importData['config_attribute_values']);

                    $importData[strtolower($importData['attribute_set']) . '_' . $config_attribute] = $importData[$config_attribute];
                    unset($importData[$config_attribute]);
                } else {
                    $attribute_set_array[] = $config_attribute;
                }
            }

            if ($importData['product_type_id'] == 'configurable') {
                $importData['config_attributes'] = implode(', ', $attribute_set_array);
            }
            array_values($importData);
        }

        if ($importData['product_type_id'] == 'configurable') {

            $product->setCanSaveConfigurableAttributes(true);
            $configAttributeCodes = $this->userCSVDataAsArray($importData['config_attributes']);

            $usingAttributeIds = array();
            foreach ($configAttributeCodes as $attributeCode) {
                $attribute = $product->getResource()->getAttribute($attributeCode);

                if ($attribute) {
                    if ($product->getTypeInstance()->canUseAttribute($attribute)) {
                        if ($new) { // fix for duplicating attributes error
                            $usingAttributeIds[] = $attribute->getAttributeId();
                        }
                    }
                }
            }


            if (!empty($usingAttributeIds)) {
                $product->getTypeInstance()->setUsedProductAttributeIds($usingAttributeIds);
                $product->setConfigurableAttributesData($product->getTypeInstance()->getConfigurableAttributesAsArray());
                $product->setCanSaveConfigurableAttributes(true);
                $product->setCanSaveCustomOptions(true);
            }

            if (isset($importData['associated'])) {

                $product->setConfigurableProductsData($this->skusToIds($importData['associated'], $product));
            }
        }

        if (isset($importData['related'])) {
            $linkIds = $this->skusToIds($importData['related'], $product);
            if (!empty($linkIds)) {
                $product->setRelatedLinkData($linkIds);
            }
        }

        if (isset($importData['upsell'])) {
            $linkIds = $this->skusToIds($importData['upsell'], $product);
            if (!empty($linkIds)) {
                $product->setUpSellLinkData($linkIds);
            }
        }

        if (isset($importData['crosssell'])) {
            $linkIds = $this->skusToIds($importData['crosssell'], $product);
            if (!empty($linkIds)) {
                $product->setCrossSellLinkData($linkIds);
            }
        }

        if (isset($importData['grouped'])) {
            $linkIds = $this->skusToIds($importData['grouped'], $product);
            if (!empty($linkIds)) {
                $product->setGroupedLinkData($linkIds);
            }
        }

        if (isset($importData['category_ids'])) {
            $product->setCategoryIds($importData['category_ids']);
			
			/* Custom code to set the value of catalogname attribute for categories associated by a user */
			if (null !== $importData['category_ids']) {
			   $_category_temp = explode(",",$importData['category_ids']);
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
				$_stringStored = strtolower($_stringStored);
				$product->setData('catalogname',$_stringStored);				
			}
        }
		
        if (isset($importData['tier_prices']) && !empty($importData['tier_prices'])) {
            $this->_editTierPrices($product, $importData['tier_prices']);
        }


        if (isset($importData['categories'])) {

            if (isset($importData['store'])) {
                $cat_store = $this->_stores[$importData['store']];
            } else {
                $message = Mage :: helper('catalog')->__('Skip import row, required field "store" for new products not defined', $field);
                Mage :: throwException($message);
            }

            $categoryIds = $this->_addCategories($importData['categories'], $cat_store);
            if ($categoryIds) {
                $product->setCategoryIds($categoryIds);
            }
        }

        foreach ($this->_ignoreFields as $field) {
            if (isset($importData[$field])) {
                unset($importData[$field]);
            }
        }

        if ($store->getId() != 0) {
            $websiteIds = $product->getWebsiteIds();
            if (!is_array($websiteIds)) {
                $websiteIds = array();
            }
            if (!in_array($store->getWebsiteId(), $websiteIds)) {
                $websiteIds[] = $store->getWebsiteId();
            }
            $product->setWebsiteIds($websiteIds);
        }

        if (isset($importData['websites'])) {
            $websiteIds = $product->getWebsiteIds();
            if (!is_array($websiteIds)) {
                $websiteIds = array();
            }
            $websiteCodes = explode(',', $importData['websites']);
            foreach ($websiteCodes as $websiteCode) {
                try {
                    $website = Mage :: app()->getWebsite(trim($websiteCode));
                    if (!in_array($website->getId(), $websiteIds)) {
                        $websiteIds[] = $website->getId();
                    }
                } catch (Exception $e) {

                }
            }
            $product->setWebsiteIds($websiteIds);
            unset($websiteIds);
        }

        foreach ($importData as $field => $value) {
            if (in_array($field, $this->_inventoryFields)) {
                continue;
            }
            if (in_array($field, $this->_imageFields)) {
                continue;
            }

            $attribute = $this->getAttribute($field);
            if (!$attribute) {
                continue;
            }

            $isArray = false;
            $setValue = $value;

            if ($attribute->getFrontendInput() == 'multiselect') {
                $value = explode(self :: MULTI_DELIMITER, $value);
                $isArray = true;
                $setValue = array();
            }

            if ($value && $attribute->getBackendType() == 'decimal') {
                $setValue = $this->getNumber($value);
            }

            if ($attribute->usesSource()) {
                $options = $attribute->getSource()->getAllOptions(false);

                if ($isArray) {
                    foreach ($options as $item) {
                        if (in_array($item['label'], $value)) {
                            $setValue[] = $item['value'];
                        }
                    }
                } else {
                    $setValue = null;
                    foreach ($options as $item) {
                        if ($item['label'] == $value) {
                            $setValue = $item['value'];
                        }
                    }
                }
            }

            $product->setData($field, $setValue);
        }

        if (!$product->getVisibility()) {
            $product->setVisibility(Mage_Catalog_Model_Product_Visibility :: VISIBILITY_NOT_VISIBLE);
        }

        $stockData = array();
        //$inventoryFields = $product -> getTypeId() == 'simple' ? $this -> _inventorySimpleFields : $this -> _inventoryOtherFields;
        $inventoryFields = isset($this->_inventoryFieldsProductTypes[$product->getTypeId()]) ? $this->_inventoryFieldsProductTypes[$product->getTypeId()] : array();
        foreach ($inventoryFields as $field) {
            if (isset($importData[$field])) {
                if (in_array($field, $this->_toNumber)) {
                    $stockData[$field] = $this->getNumber($importData[$field]);
                } else {
                    $stockData[$field] = $importData[$field];
                }
            }
        }
        $product->setStockData($stockData);

        $imageData = array();
        foreach ($this->_imageFields as $field) {
            if (!empty($importData[$field]) && $importData[$field] != 'no_selection') {
                if (!isset($imageData[$importData[$field]])) {
                    $imageData[$importData[$field]] = array();
                }
                $imageData[$importData[$field]][] = $field;
            }
        }

        foreach ($imageData as $file => $fields) {
            try {
                $product->addImageToMediaGallery(Mage :: getBaseDir('media') . DS . 'import/' . $file, $fields, false);
            } catch (Exception $e) {

            }
        }

        if (!empty($importData['gallery'])) {
            $galleryData = explode(',', $importData["gallery"]);
            foreach ($galleryData as $gallery_img) {
                try {
                    $product->addImageToMediaGallery(Mage :: getBaseDir('media') . DS . 'import' . $gallery_img, null, false, false);
                } catch (Exception $e) {
                    
                }
            }
        }

        $product->setIsMassupdate(true);
        $product->setExcludeUrlRewrite(true);
        $product->save();        
        // Save custom options prices for configurable product
        if (isset($importData['product_type_id']) && strtolower($importData['product_type_id']) == 'configurable') {

            $attribute_price_rows = array();
            if (isset($importData['config_attribute_values']) && trim($importData['config_attribute_values']) != '') {
                $attribute_price_rows = array();
                $main_attributes_sets = explode('|', $importData['config_attribute_values']);
                if (!empty($main_attributes_sets)) {
                    foreach ($main_attributes_sets as $main_attributes_set) {

                        $sub_attributes_sets = explode(':', $main_attributes_set);

                        if (!empty($sub_attributes_sets) && count($sub_attributes_sets) == 4) {

                            if ($attribute_id = $this->getProductAttributeId(strtolower(trim($sub_attributes_sets[0])))) {
                                if ($attribute_option_id = $this->getProductOptionIdFromAttributeId($attribute_id, trim($sub_attributes_sets[1]))) {

                                    $is_percent = (strtolower(trim($sub_attributes_sets[3])) == 'fixed') ? 0 : (strtolower(trim($sub_attributes_sets[3])) == 'percentage') ? 1 : 0;
                                    $attribute_price_rows[$attribute_id][] = array('attribute_id' => $attribute_id, 'value_index' => $attribute_option_id, 'pricing_value' => trim($sub_attributes_sets[2]), 'is_percent' => $is_percent);
                                } // END if($attribute_option_id = $this->getProductOptionIdFromAttributeId($attribute_id, trim($sub_attributes_sets[1])))
                            } // END if($attribute_id = $this->getProductAttributeId(strtolower(trim($sub_attributes_sets[0]))))
                        } // END if(!empty($sub_attributes_sets))
                    } // END foreach ($main_attributes_sets as $main_attributes_set)
                } // END if(!empty($main_attributes_sets))
            } // END if(isset($importData['config_attribute_values']) && trim($importData['config_attribute_values']) != '')

            if (!empty($attribute_price_rows)) {
                //print_r($attribute_price_rows);die;
                foreach ($attribute_price_rows as $attribute_id => $attribute_price_row) {
                    $read = Mage::getSingleton('core/resource')->getConnection('core_read');
                    $product_super_attribute_id = $read->fetchOne("SELECT `product_super_attribute_id` FROM catalog_product_super_attribute WHERE product_id = " . $product->getEntityId() . " AND attribute_id = " . $attribute_id);
                    $insert = Mage::getSingleton('core/resource')->getConnection('core_write');
                    if ($product_super_attribute_id) {

                        $insert->query("DELETE FROM catalog_product_super_attribute_pricing WHERE `product_super_attribute_id` = {$product_super_attribute_id}");

                        if (!empty($attribute_price_row) && count($attribute_price_row) > 0) {
                            foreach ($attribute_price_row as $row) {
                                $insert->query("INSERT INTO `catalog_product_super_attribute_pricing` (`value_id` ,`product_super_attribute_id` ,`value_index` ,`is_percent` ,`pricing_value` ,`website_id`) VALUES ( NULL , '{$product_super_attribute_id}', '" . $row['value_index'] . "', '" . $row['is_percent'] . "', '" . $row['pricing_value'] . "', '0')");
                            }
                        }
                    } // END if($product_super_attribute_id)
                } // END foreach ($attribute_price_rows as $attribute_id=>$attribute_price_row)
            } // END if(!empty($attribute_price_rows))
        }
        return true;
    }

    protected function userCSVDataAsArray($data) {
        return explode(',', str_replace(" ", "", $data));
    }

    /* protected function userCSVDataAsArray( $data ){
      return explode( ',', trim( $data ," ") );
      } */

    protected function skusToIds($userData, $product) {
        $productIds = array();
        foreach ($this->userCSVDataAsArray($userData) as $oneSku) {
            if (( $a_sku = (int) $product->getIdBySku($oneSku) ) > 0) {
                parse_str("position=", $productIds[$a_sku]);
            }
        }
        //print_r($productIds);exit;
        return $productIds;
    }

    protected $_categoryCache = array();

    protected function _addCategories($categories, $store) {
        // $rootId = $store->getRootCategoryId();
        // $rootId = Mage::app()->getStore()->getRootCategoryId();
        //$rootId = 2; // our store's root category id

        $rootId1 = Mage::app()->getStore()->getRootCategoryId(); // our store's root category id
        $rootId = isset($rootId1) ? $rootId1 : 2; // our store's root category id
        if (!$rootId) {
            return array();
        }
        $rootPath = '1/' . $rootId;
        if (empty($this->_categoryCache[$store->getId()])) {
            $collection = Mage :: getModel('catalog/category')->getCollection()
                            ->setStore($store)
                            ->addAttributeToSelect('name');
            $collection->getSelect()->where("path like '" . $rootPath . "/%'");

            foreach ($collection as $cat) {
                try {
                    $pathArr = explode('/', $cat->getPath());
                    $namePath = '';
                    for ($i = 2, $l = sizeof($pathArr); $i < $l; $i++) {
                        $name = $collection->getItemById($pathArr[$i])->getName();
                        $namePath .= ( empty($namePath) ? '' : '/' ) . trim($name);
                    }
                    $cat->setNamePath($namePath);
                } catch (Exception $e) {
                    echo "ERROR: Cat - ";
                    print_r($cat);
                    continue;
                }
            }

            $cache = array();
            foreach ($collection as $cat) {
                $cache[strtolower($cat->getNamePath())] = $cat;
                $cat->unsNamePath();
            }
            $this->_categoryCache[$store->getId()] = $cache;
        }
        $cache = &$this->_categoryCache[$store->getId()];

        $catIds = array();
        foreach (explode(',', $categories) as $categoryPathStr) {
            $categoryPathStr = preg_replace('#s*/s*#', '/', trim($categoryPathStr));
            if (!empty($cache[$categoryPathStr])) {
                $catIds[] = $cache[$categoryPathStr]->getId();
                continue;
            }
            $path = $rootPath;
            $namePath = '';
            foreach (explode('/', $categoryPathStr) as $catName) {
                $namePath .= ( empty($namePath) ? '' : '/' ) . strtolower($catName);
                if (empty($cache[$namePath])) {
                    $cat = Mage :: getModel('catalog/category')
                                    ->setStoreId($store->getId())
                                    ->setPath($path)
                                    ->setName($catName)
                                    ->setIsActive(1)
                                    ->save();
                    $cache[$namePath] = $cat;
                }
                $catId = $cache[$namePath]->getId();
                $path .= '/' . $catId;
            }
            if ($catId) {
                $catIds[] = $catId;
            }
        }
        return join(',', $catIds);
    }

    protected function _removeFile($file) {
        if (file_exists($file)) {
            if (unlink($file)) {
                return true;
            }
        }
        return false;
    }

    /*
     * This function returns attribute id for product
     */

    protected function getProductAttributeId($attribute_code) {
        Mage::app()->setCurrentStore('default');
        $attributeId = Mage::getResourceModel('eav/entity_attribute')
                        ->getIdByCode('catalog_product', $attribute_code);

        //$attribute = Mage::getModel('catalog/resource_eav_attribute')->load($attributeId);
        //print_r($attributeId);

        return $attributeId;
    }

    /*
     * This function returns option id from attribute id
     */

    protected function getProductOptionIdFromAttributeId($attributeId, $optValue) {
        Mage::app()->setCurrentStore('default');
        $attribute = Mage::getModel('catalog/resource_eav_attribute')->load($attributeId);

        $attributeOptions = $attribute->getSource()->getAllOptions();

        foreach ($attributeOptions as $att_opt) {
            if (strtolower($att_opt['label']) == strtolower($optValue)) {
                return $att_opt['value'];
            }
        }
    }

    /*
     * This function returns dropdown options value for product
     */

    protected function getProductOptionId($attribute_code, $optValue) {

        $attributeId = Mage::getResourceModel('eav/entity_attribute')
                        ->getIdByCode('catalog_product', $attribute_code);

        $attribute = Mage::getModel('catalog/resource_eav_attribute')->load($attributeId);
        $attributeOptions = $attribute->getSource()->getAllOptions();

        foreach ($attributeOptions as $att_opt) {
            if (strtolower($att_opt['label']) == strtolower($optValue))
                return $att_opt['value'];
        }
    }

}

