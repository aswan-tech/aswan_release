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
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */
class Mage_Catalog_Model_Convert_Adapter_Product extends Mage_Eav_Model_Convert_Adapter_Entity {
    const MULTI_DELIMITER = ' , ';
    const ENTITY = 'catalog_product_import';
    protected $_last_config_attributes = '';
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'catalog_product_import';
    /**
     * Product model
     *
     * @var Mage_Catalog_Model_Product
     */
    protected $_productModel;
    /**
     * product types collection array
     *
     * @var array
     */
    protected $_productTypes;
    /**
     * Product Type Instances singletons
     *
     * @var array
     */
    protected $_productTypeInstances = array();
    /**
     * product attribute set collection array
     *
     * @var array
     */
    protected $_productAttributeSets;
    protected $_stores;
    protected $_attributes = array();
    protected $_configs = array();
    protected $_requiredFields = array();
    protected $_ignoreFields = array();
    protected $_excludeRequiredFields = array();
    /**
     * @deprecated after 1.5.0.0-alpha2
     *
     * @var array
     */
    protected $_imageFields = array();
    /**
     * Inventory Fields array
     *
     * @var array
     */
    protected $_inventoryFields = array();
    /**
     * Inventory Fields by product Types
     *
     * @var array
     */
    protected $_inventoryFieldsProductTypes = array();
    protected $_toNumber = array();

    /**
     * Retrieve event prefix for adapter
     *
     * @return string
     */
    public function getEventPrefix() {
        return $this->_eventPrefix;
    }

    /**
     * Affected entity ids
     *
     * @var array
     */
    protected $_affectedEntityIds = array();
    /**
     * Custom fields to be validated as required while insert/update
     */
    protected $_contentRequiredFields = array('sku', 'status', 'name', 'description', 'info_care', 'delivery', 'returns', 'about_lecom_collection', 'image', 'gallery', 'color_swatch_image','hover_image');
    protected $_inventoryRequiredFields = array('sku', 'qty', 'is_in_stock', 'store', 'websites', 'attribute_set');
    protected $_priceRequiredFields = array('sku', 'price', 'store', 'websites', 'attribute_set');

    /**
     * Store affected entity ids
     *
     * @param  int|array $ids
     * @return Mage_Catalog_Model_Convert_Adapter_Product
     */
    protected function _addAffectedEntityIds($ids) {
        if (is_array($ids)) {
            foreach ($ids as $id) {
                $this->_addAffectedEntityIds($id);
            }
        } else {
            $this->_affectedEntityIds[] = $ids;
        }

        return $this;
    }

    /**
     * Retrieve affected entity ids
     *
     * @return array
     */
    public function getAffectedEntityIds() {
        return $this->_affectedEntityIds;
    }

    /**
     * Clear affected entity ids results
     *
     * @return Mage_Catalog_Model_Convert_Adapter_Product
     */
    public function clearAffectedEntityIds() {
        $this->_affectedEntityIds = array();
        return $this;
    }

    /**
     * Load product collection Id(s)
     */
    public function load() {
        $attrFilterArray = array();
        $attrFilterArray ['name'] = 'like';
        $attrFilterArray ['sku'] = 'startsWith';
        $attrFilterArray ['type'] = 'eq';
        $attrFilterArray ['attribute_set'] = 'eq';
        $attrFilterArray ['visibility'] = 'eq';
        $attrFilterArray ['status'] = 'eq';
        $attrFilterArray ['price'] = 'fromTo';
        $attrFilterArray ['qty'] = 'fromTo';
        $attrFilterArray ['store_id'] = 'eq';

        $attrToDb = array(
            'type' => 'type_id',
            'attribute_set' => 'attribute_set_id'
        );

        $filters = $this->_parseVars();

        if ($qty = $this->getFieldValue($filters, 'qty')) {
            $qtyFrom = isset($qty['from']) ? (float) $qty['from'] : 0;
            $qtyTo = isset($qty['to']) ? (float) $qty['to'] : 0;

            $qtyAttr = array();
            $qtyAttr['alias'] = 'qty';
            $qtyAttr['attribute'] = 'cataloginventory/stock_item';
            $qtyAttr['field'] = 'qty';
            $qtyAttr['bind'] = 'product_id=entity_id';
            $qtyAttr['cond'] = "{{table}}.qty between '{$qtyFrom}' AND '{$qtyTo}'";
            $qtyAttr['joinType'] = 'inner';

            $this->setJoinField($qtyAttr);
        }

        parent::setFilter($attrFilterArray, $attrToDb);

        if ($price = $this->getFieldValue($filters, 'price')) {
            $this->_filter[] = array(
                'attribute' => 'price',
                'from' => $price['from'],
                'to' => $price['to']
            );
            $this->setJoinAttr(array(
                'alias' => 'price',
                'attribute' => 'catalog_product/price',
                'bind' => 'entity_id',
                'joinType' => 'LEFT'
            ));
        }

        return parent::load();
    }

    /**
     * Retrieve product model cache
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProductModel() {
        if (is_null($this->_productModel)) {
            $productModel = Mage::getModel('catalog/product');
            $this->_productModel = Mage::objects()->save($productModel);
        }
        return Mage::objects()->load($this->_productModel);
    }

    /**
     * Retrieve eav entity attribute model
     *
     * @param string $code
     * @return Mage_Eav_Model_Entity_Attribute
     */
    public function getAttribute($code) {
        if (!isset($this->_attributes[$code])) {
            $this->_attributes[$code] = $this->getProductModel()->getResource()->getAttribute($code);
        }
        if ($this->_attributes[$code] instanceof Mage_Catalog_Model_Resource_Eav_Attribute) {
            $applyTo = $this->_attributes[$code]->getApplyTo();
            if ($applyTo && !in_array($this->getProductModel()->getTypeId(), $applyTo)) {
                return false;
            }
        }
        return $this->_attributes[$code];
    }

    /**
     * Retrieve product type collection array
     *
     * @return array
     */
    public function getProductTypes() {
        if (is_null($this->_productTypes)) {
            $this->_productTypes = array();
            $options = Mage::getModel('catalog/product_type')
                            ->getOptionArray();
            foreach ($options as $k => $v) {
                $this->_productTypes[$k] = $k;
            }
        }
        return $this->_productTypes;
    }

    /**
     * ReDefine Product Type Instance to Product
     *
     * @param Mage_Catalog_Model_Product $product
     * @return Mage_Catalog_Model_Convert_Adapter_Product
     */
    public function setProductTypeInstance(Mage_Catalog_Model_Product $product) {
        $type = $product->getTypeId();
        if (!isset($this->_productTypeInstances[$type])) {
            $this->_productTypeInstances[$type] = Mage::getSingleton('catalog/product_type')
                            ->factory($product, true);
        }
        $product->setTypeInstance($this->_productTypeInstances[$type], true);
        return $this;
    }

    /**
     * Retrieve product attribute set collection array
     *
     * @return array
     */
    public function getProductAttributeSets() {
        if (is_null($this->_productAttributeSets)) {
            $this->_productAttributeSets = array();

            $entityTypeId = Mage::getModel('eav/entity')
                            ->setType('catalog_product')
                            ->getTypeId();
            $collection = Mage::getResourceModel('eav/entity_attribute_set_collection')
                            ->setEntityTypeFilter($entityTypeId);
            foreach ($collection as $set) {
                $this->_productAttributeSets[$set->getAttributeSetName()] = $set->getId();
            }
        }
        return $this->_productAttributeSets;
    }

    /**
     *  Init stores
     */
    protected function _initStores() {
        if (is_null($this->_stores)) {
            $this->_stores = Mage::app()->getStores(true, true);
            foreach ($this->_stores as $code => $store) {
                $this->_storesIdCode[$store->getId()] = $code;
            }
        }
    }

    /**
     * Retrieve store object by code
     *
     * @param string $store
     * @return Mage_Core_Model_Store
     */
    public function getStoreByCode($store) {
        $this->_initStores();
        /**
         * In single store mode all data should be saved as default
         */
        if (Mage::app()->isSingleStoreMode()) {
            return Mage::app()->getStore(Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID);
        }

        if (isset($this->_stores[$store])) {
            return $this->_stores[$store];
        }

        return false;
    }

    /**
     * Retrieve store object by code
     *
     * @param string $store
     * @return Mage_Core_Model_Store
     */
    public function getStoreById($id) {
        $this->_initStores();
        /**
         * In single store mode all data should be saved as default
         */
        if (Mage::app()->isSingleStoreMode()) {
            return Mage::app()->getStore(Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID);
        }

        if (isset($this->_storesIdCode[$id])) {
            return $this->getStoreByCode($this->_storesIdCode[$id]);
        }

        return false;
    }

    public function parse() {
        $batchModel = Mage::getSingleton('dataflow/batch');
        /* @var $batchModel Mage_Dataflow_Model_Batch */

        $batchImportModel = $batchModel->getBatchImportModel();
        $importIds = $batchImportModel->getIdCollection();
        foreach ($importIds as $importId) {
            $batchImportModel->load($importId);
            $importData = $batchImportModel->getBatchData();

            $this->saveRow($importData);
        }
    }

    protected $_productId = '';

    /**
     * Initialize convert adapter model for products collection
     *
     */
    public function __construct() {
        $fieldset = Mage::getConfig()->getFieldset('catalog_product_dataflow', 'admin');
        foreach ($fieldset as $code => $node) {
            /* @var $node Mage_Core_Model_Config_Element */
            if ($node->is('inventory')) {
                foreach ($node->product_type->children() as $productType) {
                    $productType = $productType->getName();
                    $this->_inventoryFieldsProductTypes[$productType][] = $code;
                    if ($node->is('use_config')) {
                        $this->_inventoryFieldsProductTypes[$productType][] = 'use_config_' . $code;
                    }
                }

                $this->_inventoryFields[] = $code;
                if ($node->is('use_config')) {
                    $this->_inventoryFields[] = 'use_config_' . $code;
                }
            }

            if ($node->is('required')) {
                $this->_requiredFields[] = $code;
            }
            if ($node->is('ignore')) {
                $this->_ignoreFields[] = $code;
            }
            if ($node->is('to_number')) {
                $this->_toNumber[] = $code;
            }
        }

        $this->setVar('entity_type', 'catalog/product');
        if (!Mage::registry('Object_Cache_Product')) {
            $this->setProduct(Mage::getModel('catalog/product'));
        }

        if (!Mage::registry('Object_Cache_StockItem')) {
            $this->setStockItem(Mage::getModel('cataloginventory/stock_item'));
        }
    }

    /**
     * Retrieve not loaded collection
     *
     * @param string $entityType
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
     */
    protected function _getCollectionForLoad($entityType) {
        $collection = parent::_getCollectionForLoad($entityType)
                        ->setStoreId($this->getStoreId())
                        ->addStoreFilter($this->getStoreId());
        return $collection;
    }

    public function setProduct(Mage_Catalog_Model_Product $object) {
        $id = Mage::objects()->save($object);
        //$this->_product = $object;
        Mage::register('Object_Cache_Product', $id);
    }

    public function getProduct() {
        return Mage::objects()->load(Mage::registry('Object_Cache_Product'));
    }

    public function setStockItem(Mage_CatalogInventory_Model_Stock_Item $object) {
        $id = Mage::objects()->save($object);
        Mage::register('Object_Cache_StockItem', $id);
    }

    public function getStockItem() {
        return Mage::objects()->load(Mage::registry('Object_Cache_StockItem'));
    }

    public function save() {
        $stores = array();
        foreach (Mage::getConfig()->getNode('stores')->children() as $storeNode) {
            $stores[(int) $storeNode->system->store->id] = $storeNode->getName();
        }

        $collections = $this->getData();
        if ($collections instanceof Mage_Catalog_Model_Entity_Product_Collection) {
            $collections = array($collections->getEntity()->getStoreId() => $collections);
        } elseif (!is_array($collections)) {
            $this->addException(
                    Mage::helper('catalog')->__('No product collections found.'),
                    Mage_Dataflow_Model_Convert_Exception::FATAL
            );
        }

        $stockItems = Mage::registry('current_imported_inventory');
        if ($collections)
            foreach ($collections as $storeId => $collection) {
                $this->addException(Mage::helper('catalog')->__('Records for "' . $stores[$storeId] . '" store found.'));

                if (!$collection instanceof Mage_Catalog_Model_Entity_Product_Collection) {
                    $this->addException(
                            Mage::helper('catalog')->__('Product collection expected.'),
                            Mage_Dataflow_Model_Convert_Exception::FATAL
                    );
                }
                try {
                    $i = 0;
                    foreach ($collection->getIterator() as $model) {
                        $new = false;
                        // if product is new, create default values first
                        if (!$model->getId()) {
                            $new = true;
                            $model->save();

                            // if new product and then store is not default
                            // we duplicate product as default product with store_id -
                            if (0 !== $storeId) {
                                $data = $model->getData();
                                $default = Mage::getModel('catalog/product');
                                $default->setData($data);
                                $default->setStoreId(0);
                                $default->save();
                                unset($default);
                            } // end
                            #Mage::getResourceSingleton('catalog_entity/convert')->addProductToStore($model->getId(), 0);
                        }
                        if (!$new || 0 !== $storeId) {
                            if (0 !== $storeId) {
                                Mage::getResourceSingleton('catalog_entity/convert')->addProductToStore(
                                        $model->getId(),
                                        $storeId
                                );
                            }
                            $model->save();
                        }

                        if (isset($stockItems[$model->getSku()]) && $stock = $stockItems[$model->getSku()]) {
                            $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($model->getId());
                            $stockItemId = $stockItem->getId();

                            if (!$stockItemId) {
                                $stockItem->setData('product_id', $model->getId());
                                $stockItem->setData('stock_id', 1);
                                $data = array();
                            } else {
                                $data = $stockItem->getData();
                            }

                            foreach ($stock as $field => $value) {
                                if (!$stockItemId) {
                                    if (in_array($field, $this->_configs)) {
                                        $stockItem->setData('use_config_' . $field, 0);
                                    }
                                    $stockItem->setData($field, $value ? $value : 0);
                                } else {

                                    if (in_array($field, $this->_configs)) {
                                        if ($data['use_config_' . $field] == 0) {
                                            $stockItem->setData($field, $value ? $value : 0);
                                        }
                                    } else {
                                        $stockItem->setData($field, $value ? $value : 0);
                                    }
                                }
                            }
                            $stockItem->save();
                            unset($data);
                            unset($stockItem);
                            unset($stockItemId);
                        }
                        unset($model);
                        $i++;
                    }
                    $this->addException(Mage::helper('catalog')->__("Saved %d record(s)", $i));
                } catch (Exception $e) {
                    if (!$e instanceof Mage_Dataflow_Model_Convert_Exception) {
                        $this->addException(
                                Mage::helper('catalog')->__(
                                        'An error occurred while saving the collection, aborting. Error message: %s',
                                        $e->getMessage()
                                ),
                                Mage_Dataflow_Model_Convert_Exception::FATAL
                        );
                    }
                }
            }
        unset($collections);

        return $this;
    }

    /**
     * Save product (import)
     *
     * @param  array $importData
     * @throws Mage_Core_Exception
     * @return bool
     */
    public function saveRow(array $importData) {
        $cronName = Mage::app()->getRequest()->getParam('cronname');
		
		$use_file = false;
		$new_arrival_contentfeed = false;
		$filepath = Mage::getBaseDir('var').'/content_feed_errorlog.csv';
		
        if (!isset($cronName)) {
            $server = $_SERVER['argv'][1];
            $server = explode("=", $server);
            if (isset($server[1])) {
                $cronName = $server[1];
            }
        }
		
		Mage::getSingleton('core/session', array('name'=>'adminhtml'));

		//verify if the user is logged in to the backend
		if(Mage::getSingleton('admin/session')->isLoggedIn()){
			//check whether it is an inventory run
			if(!isset($cronName)){
				$use_file = true;
				$new_arrival_contentfeed = true;
			}
		}
		
		if($use_file){
			$handle = fopen($filepath, "a");
		}
		
        $importData = array_map('trim', $importData);
        $product = $this->getProductModel()->reset();

        if (empty($importData['store'])) {
            if (!is_null($this->getBatchParams('store'))) {
                $store = $this->getStoreById($this->getBatchParams('store'));
            } else {
                $message = Mage::helper('catalog')->__(
                                'Skipping import row, required field "%s" is not defined.',
                                'store'
                );
                Mage::throwException($message);
            }
        } else {
            $store = $this->getStoreByCode($importData['store']);
        }

        if ($store === false) {
            $message = Mage::helper('catalog')->__(
                            'Skipping import row, store "%s" field does not exist.',
                            $importData['store']
            );
            Mage::throwException($message);
        }
		
		$_skuforuse = '';
		
        if (empty($importData['sku'])) {
				$message = Mage::helper('catalog')->__('Skipping import row, required field "%s" is not defined.', 'sku');
				
				if($use_file){
					fputcsv($handle, array('','','','','SKU not defined'));
				
					fclose($handle);
				}
				Mage::throwException($message);
        }else{
			$_skuforuse = $importData['sku'];
		}
        $product->setStoreId($store->getId());
        $productId = $product->getIdBySku($importData['sku']);

        if ($productId) {
            $product->load($productId);
        } else {
            $productTypes = $this->getProductTypes();
            $productAttributeSets = $this->getProductAttributeSets();

            /**
             * Check product define type
             */
            if (empty($importData['type']) || !isset($productTypes[strtolower($importData['type'])])) {
                $value = isset($importData['type']) ? $importData['type'] : '';
                $message = Mage::helper('catalog')->__('Skipping import row, following SKU: "%s" is not present in Magento.', $_skuforuse);
				
				if($use_file){
					fputcsv($handle, array($_skuforuse,'','','',$message));
				
					fclose($handle);
				}
               
                Mage::throwException($message);
            }

            $product->setTypeId($productTypes[strtolower($importData['type'])]);
            /**
             * Check product define attribute set
             */
            if (empty($importData['attribute_set']) || !isset($productAttributeSets[$importData['attribute_set']])) {
                $value = isset($importData['attribute_set']) ? $importData['attribute_set'] : '';
                $message = Mage::helper('catalog')->__(
                                'Skip import row, the value "%s" is invalid for field "%s"',
                                $value,
                                'attribute_set'
                );
                Mage::throwException($message);
            }
            $product->setAttributeSetId($productAttributeSets[$importData['attribute_set']]);

            foreach ($this->_requiredFields as $field) {
                $attribute = $this->getAttribute($field);
                if (!isset($importData[$field]) && $attribute && $attribute->getIsRequired()) {
                    $message = Mage::helper('catalog')->__(
                                    'Skipping import row, required field "%s" for new products is not defined.',
                                    $field
                    );
                    Mage::throwException($message);
                }

                if (isset($importData[$field]) && $attribute && $attribute->getIsRequired() && trim($importData[$field]) == '') {
                    $message = Mage::helper('catalog')->__(
                                    'Skipping import row, value for required field "%s" for new products is not available.',
                                    $field
                    );
                    Mage::throwException($message);
                }
            }
        }

        if (!isset($cronName)){
			$_feilds_left_blank = '';
			$_Images_not_exist = '';
			$_productType = $product->getTypeId();
            foreach ($this->_contentRequiredFields as $field) {
				if($field == 'color_swatch_image'){
					if($_productType == 'simple'){
						if (isset($importData[$field])) {
							if (@trim($importData[$field]) == "") {
								$_feilds_left_blank .= $field.',';
							}
						}
					}
				}
				elseif($field == 'gallery'){
					if (isset($importData[$field])) {
						if (@trim($importData[$field]) == "") {
							$_feilds_left_blank .= $field.',';
						}else{
							$galleryDataCheck = explode('|', $importData["gallery"]);
							
							foreach ($galleryDataCheck as $gallery_img) {
								if(!file_exists(Mage :: getBaseDir('media') . DS . 'import' . $gallery_img)){
									$_Images_not_exist .= $gallery_img.',';
								}							
							}
						}
					}
				}else{
					if (isset($importData[$field])) {
						if (@trim($importData[$field]) == "") {
							$_feilds_left_blank .= $field.',';
						}
					}
				}
            }
			
			if($_feilds_left_blank != ''){
					$_feilds_left_blank = substr($_feilds_left_blank,0,strlen($_feilds_left_blank)-1);
					
					if($_Images_not_exist != ''){
						$_Images_not_exist = substr($_Images_not_exist,0,strlen($_Images_not_exist)-1);
						
						$message = Mage::helper('catalog')->__('Skipping import row, required field "%s" are not defined , Following images are not found on server "%s" for following SKU "%s".', $_feilds_left_blank, $_Images_not_exist, $_skuforuse);
					}else{
						$message = Mage::helper('catalog')->__('Skipping import row, required field "%s" are not defined for following SKU "%s".', $_feilds_left_blank, $_skuforuse);
					}
										
					$_stockckeck = $product->getStockItem()->getIsInStock();
					
					$_productType = $product->getTypeId();
					
					if($_productType == 'simple'){
						$parent_ids = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($product->getId());
						$parent_collection = Mage::getResourceModel('catalog/product_collection')->addFieldToFilter('entity_id', array('in'=>$parent_ids))->addAttributeToSelect('sku');
						$parentSkus = '';
						$parent_skus = $parent_collection->getColumnValues('sku');
						if(is_array($parent_skus) && !empty($parent_skus)){
							foreach($parent_skus as $skus){
								$parentSkus .= $skus.',';
							}
						}
						if($parentSkus != ''){
							$parentSkus = substr($parentSkus,0,strlen($parentSkus)-1);
						}
						if($_stockckeck == 1){
							if($use_file){
								fputcsv($handle, array($_skuforuse,$_productType,$parentSkus,'In Stock',$message));
							}
						}else{
							if($use_file){
								fputcsv($handle, array($_skuforuse,$_productType,$parentSkus,'Out Of Stock',$message));
							}
						}
					}else{
						if($_stockckeck == 1){
							if($use_file){
								fputcsv($handle, array($_skuforuse,$_productType,$_skuforuse,'In Stock',$message));
							}
						}else{
							if($use_file){
								fputcsv($handle, array($_skuforuse,$_productType,$_skuforuse,'Out Of Stock',$message));
							}
						}
					}
					if($use_file){
						fclose($handle);
					}
					
					Mage::throwException($message);
			}else{
				if($_Images_not_exist != ''){
						$_Images_not_exist = substr($_Images_not_exist,0,strlen($_Images_not_exist)-1);
						
						$message = Mage::helper('catalog')->__('Following images are not found on server "%s" for following SKU "%s".',$_Images_not_exist, $_skuforuse);
						
						$_stockckeck = $product->getStockItem()->getIsInStock();
					
					$_productType = $product->getTypeId();
					
					if($_productType == 'simple'){
						$parent_ids = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($product->getId());
						$parent_collection = Mage::getResourceModel('catalog/product_collection')->addFieldToFilter('entity_id', array('in'=>$parent_ids))->addAttributeToSelect('sku');
						$parentSkus = '';
						$parent_skus = $parent_collection->getColumnValues('sku');
						if(is_array($parent_skus) && !empty($parent_skus)){
							foreach($parent_skus as $skus){
								$parentSkus .= $skus.',';
							}
						}
						if($parentSkus != ''){
							$parentSkus = substr($parentSkus,0,strlen($parentSkus)-1);
						}
						if($_stockckeck == 1){
							if($use_file){
								fputcsv($handle, array($_skuforuse,$_productType,$parentSkus,'In Stock',$message));
							}
						}else{
							if($use_file){
								fputcsv($handle, array($_skuforuse,$_productType,$parentSkus,'Out Of Stock',$message));
							}
						}
					}else{
						if($_stockckeck == 1){
							if($use_file){
								fputcsv($handle, array($_skuforuse,$_productType,$_skuforuse,'In Stock',$message));
							}
						}else{
							if($use_file){
								fputcsv($handle, array($_skuforuse,$_productType,$_skuforuse,'Out Of Stock',$message));
							}
						}
					}
					if($use_file){
						fclose($handle);
					}
					
					//Mage::throwException($message);
					}	
			}
        } elseif ($cronName == 'product_inventory') {
            foreach ($this->_inventoryRequiredFields as $field) {
                if (!isset($importData[$field])) {
                    $message = Mage::helper('catalog')->__('Skipping import row, required field "%s" is not defined.', $field);
                    Mage::throwException($message);
                }

                if (@trim($importData[$field]) == "") {
                    $message = Mage::helper('catalog')->__('Skipping import row, value for required field "%s" is not available.', $field);
                    Mage::throwException($message);
                }
            }
        } elseif ($cronName == 'price_update') {
            foreach ($this->_priceRequiredFields as $field) {
                if (!isset($importData[$field])) {
                    $message = Mage::helper('catalog')->__('Skipping import row, required field "%s" is not defined.', $field);
                    Mage::throwException($message);
                }

                if (@trim($importData[$field]) == "") {
                    $message = Mage::helper('catalog')->__('Skipping import row, value for required field "%s" is not available.', $field);
                    Mage::throwException($message);
                }
            }
        }

        $this->setProductTypeInstance($product);

        /*
         * New arrival CR Start
        */

        if ($cronName == 'product_inventory') {

            $qtyStock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product)->getQty();

            if ($importData['qty'] > $qtyStock) {

                //$config_days = Mage::getStoreConfig('inventory/date_setting/old_after'); //value

                //$news_from_date = date('Y-m-d H:i:s', time());
                //$news_to_date = date('Y-m-d H:i:s', mktime('23', '59', '59', date('m'), date('d') + $config_days, date('Y')));

                $objConfigurableProduct = Mage::getModel('catalog/product_type_configurable');
                $arrConfigurableProductIds = $objConfigurableProduct->getParentIdsByChild($product->getId());

                foreach ($arrConfigurableProductIds as $configurableProductId) {

                    $config_product = Mage::getModel('catalog/product')->load($configurableProductId);
                    
                    if ($config_product) {                       

                        $categoryIds = $config_product->getCategoryIds();
                        
                        foreach ($categoryIds as $categoryId) {
                            $category = Mage::getModel('catalog/category')->setStoreId(Mage::app()->getStore()->getId())->load($categoryId);

                            if ($category->getLevel() == '2') {
                                $collection = Mage::getModel('catalog/category')
                                                ->getCollection()
                                                ->addAttributeToFilter('parent_id', $categoryId)
                                                ->addAttributeToFilter('url_key', 'new-arrivals')
                                                ->load();

                                $nacat_arr = $collection->getData();

                                $categoryIds[] = $nacat_arr['0']['entity_id'];
                            }
                        }
                        $category_ids = implode(',', $categoryIds);

                        $config_product->setCategoryIds($category_ids);
                        //$config_product->setNewsFromDate($news_from_date);
						//$config_product->setNewsToDate($news_to_date);
                        $config_product->save();
						
						unset($config_product);
                    }
                }
            }
        }

        /*
         * New arrival CR End
        */
		


        if (isset($importData['category_ids'])) {
            $product->setCategoryIds($importData['category_ids']);
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
            if (!is_array($websiteIds) || !$store->getId()) {
                $websiteIds = array();
            }
            $websiteCodes = explode(',', $importData['websites']);
            foreach ($websiteCodes as $websiteCode) {
                try {
                    $website = Mage::app()->getWebsite(trim($websiteCode));
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
            if (is_null($value)) {
                continue;
            }

            $attribute = $this->getAttribute($field);
            if (!$attribute) {
                continue;
            }

            $isArray = false;
            $setValue = $value;

            if ($attribute->getFrontendInput() == 'multiselect') {
                $value = explode(self::MULTI_DELIMITER, $value);
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
                        if (in_array(strtolower($item['label']), array_map('strtolower', $value))) {
                            $setValue[] = $item['value'];
                        }
                    }
                } else {
                    $setValue = false;
                    foreach ($options as $item) {
                        if (strtolower($item['label']) == strtolower($value)) {
                            $setValue = $item['value'];
                        }
                    }
                }
            }

            $product->setData($field, $setValue);
        }

        if (!$product->getVisibility()) {
            $product->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE);
        }

        $stockData = array();
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
            $galleryData = explode('|', $importData["gallery"]);

            $attributes = $product->getTypeInstance()->getSetAttributes();
            $gallery = $attributes['media_gallery'];

            $galleryRemoveData = $product->getMediaGallery();
            foreach ($galleryRemoveData['images'] as $image) {
                $gallery->getBackend()->removeImage($product, $image['file']);
            }

            foreach ($galleryData as $gallery_img) {
                try {
                    $media_flag = 0;
                    if ($gallery_img == $importData["image"]) {
                        $media_flag = 1;
                    }

                    if ($media_flag) {
                        $product->addImageToMediaGallery(Mage :: getBaseDir('media') . DS . 'import' . $gallery_img, array('image', 'small_image', 'thumbnail'), false, false);
                    } else {
                        $product->addImageToMediaGallery(Mage :: getBaseDir('media') . DS . 'import' . $gallery_img, null, false, false);
                    }
                } catch (Exception $e) {
                }
            }
        }
        // check for attribute set any other than Default
        $productId = $product->getIdBySku($importData['sku']);
        $new = true; // fix for duplicating attributes error
        if ($productId) {
            $product->load($productId);
            $new = false; // fix for duplicating attributes error
        }
        if (trim($importData[' product_type_id']) == 'configurable') {
            
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
        // color swatch images
        if (!empty($importData['color_swatch_image']) && file_exists(Mage :: getBaseDir('media') . DS . 'import' . $importData['color_swatch_image'])) {
            $product->addImageToMediaGallery(Mage :: getBaseDir('media') . DS . 'import' . $importData['color_swatch_image'], array('color_swatch_image'), false, true);
        }
		// hover images
        if (!empty($importData['hover_image']) && file_exists(Mage :: getBaseDir('media') . DS . 'import' . $importData['hover_image'])) {
            $product->addImageToMediaGallery(Mage :: getBaseDir('media') . DS . 'import' . $importData['hover_image'], array('hover_image'), false, true);
        }

		/* Content feed now being used to run-add products in new arrival */
		// first check is made to check if status feild is coming in import data to see its a content feed or not.
		if(isset($importData['status'])){
			//second check is that to see if it is run by admin
			if($new_arrival_contentfeed){
				if ($product && $product->getTypeId() == "configurable") {
					$categoryIds = $product->getCategoryIds();
					$news_from_date = date('Y-m-d H:i:s', time());             
                    foreach ($categoryIds as $categoryId) {
                        $category = Mage::getModel('catalog/category')->setStoreId(Mage::app()->getStore()->getId())->load($categoryId);
							if ($category->getLevel() == '2') {
								$collection = Mage::getModel('catalog/category')
                                                ->getCollection()
                                                ->addAttributeToFilter('parent_id', $categoryId)
                                                ->addAttributeToFilter('url_key', 'new-arrivals')
                                                ->load();

                                $nacat_arr = $collection->getData();

                                $categoryIds[] = $nacat_arr['0']['entity_id'];
							}
					}
					if(isset($importData['category_ids'])){
						$category_ids_sheet = explode(",",$importData['category_ids']);
						$merged_arrays = array_unique(array_merge($category_ids_sheet,$categoryIds));
						$category_ids = implode(',', $merged_arrays);
					}else{
						$category_ids = implode(',', $categoryIds);
					}

                    $product->setCategoryIds($category_ids);
                    $product->setNewsFromDate($news_from_date);
					//$config_product->setNewsToDate($news_to_date);
                    //$config_product->save();
					//unset($config_product);
                }
			}
		}
		/* Content feed now being used to run-add products in new arrival */
		
        $product->setIsMassupdate(true);
        $product->setExcludeUrlRewrite(true);
		
		$product->save();
		
		if($use_file){
                try{
                    $_stockckeck =  Mage::getModel('cataloginventory/stock_item')->loadByProduct($product)->getIsInStock();
                }catch(Exception $e){
                   $_stockckeck =0; 
                }
                
                $_productType = $product->getTypeId();
                if($_productType == 'simple'){
                    $parent_ids = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($product->getId());
                    $parent_collection = Mage::getResourceModel('catalog/product_collection')->addFieldToFilter('entity_id', array('in'=>$parent_ids))->addAttributeToSelect('sku');
                    $parentSkus = '';
                    $parent_skus = $parent_collection->getColumnValues('sku');
                    if(is_array($parent_skus) && !empty($parent_skus)){
                        foreach($parent_skus as $skus){
                            $parentSkus .= $skus.',';
                        }
                    }
                    if($parentSkus != ''){
                        $parentSkus = substr($parentSkus,0,strlen($parentSkus)-1);
                    }
                    if($_stockckeck == 1){
                        fputcsv($handle, array($_skuforuse,$_productType,$parentSkus,'In Stock','No Error Occured'));
                    }else{
                        fputcsv($handle, array($_skuforuse,$_productType,$parentSkus,'Out Of Stock','No Error Occured'));
                    }
                }else{
                    if($_stockckeck == 1){
                        fputcsv($handle, array($_skuforuse,$_productType,$_skuforuse,'In Stock','No Error Occured'));
                    }else{
                        fputcsv($handle, array($_skuforuse,$_productType,$_skuforuse,'Out Of Stock','No Error Occured'));
                    }
                }   
            
                fclose($handle);
		}
		
        // Store affected products ids
        $this->_addAffectedEntityIds($product->getId());
		
		/* Section added to position products in various categories programatically */
			//First check added to whether the product is configurable or not
			$_type = $product->getTypeId();
			if($_type == "configurable"){
				// Second check is made to see whether position has been specified in sheet for change or not
				if(isset($importData['position']) && $importData['position'] == "Yes"){
					// Now we find the categories that product he is assigned to
					$categoryIds = $product->getCategoryIds();
					$cat_api = new Mage_Catalog_Model_Category_Api;
					$_write_connection = Mage::getSingleton('core/resource')->getConnection('core_write');
					$_read_connection = Mage::getSingleton('core/resource')->getConnection('core_read');
					foreach ($categoryIds as $categoryId) {
						$_query_read = 'SELECT MAX(`position`) AS position FROM `catalog_category_product` WHERE `category_id=`'.$categoryId;
                        //$category = Mage::getModel('catalog/category')->load($categoryId);
						$_get_values_in_DB = $_read_connection->fetchAll($_query_read);
						if(count($_get_values_in_DB) > 0){
							$position = $_get_values_in_DB['position'];							
							$cat_api->assignProduct($categoryId, $product->getId(), $position+1);
						}				
					}
					unset($cat_api);
				}
			}
		/* Section added to position products in various categories programatically */

        // Save custom options prices for configurable product
        if ($_type == "configurable") {
            $attribute_price_rows = array();
            if (isset($importData['config_attribute_values']) && trim($importData['config_attribute_values']) != '') {
                $attribute_price_rows = array();
                $main_attributes_sets = explode('|', $importData['config_attribute_values']);
                $output1 = print_r($main_attributes_sets, true);
                file_put_contents('/tmp/output.txt','Output1'.$output1,FILE_APPEND);
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

    protected function getProductAttributeId($attribute_code) {
        Mage::app()->setCurrentStore('default');
        $attributeId = Mage::getResourceModel('eav/entity_attribute')
                        ->getIdByCode('catalog_product', $attribute_code);
        return $attributeId;
    }
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


     protected function userCSVDataAsArray($data) {
        return explode(',', str_replace(" ", "", $data));
    }

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
    /**
     * Silently save product (import)
     *
     * @param  array $importData
     * @return bool
     */
    public function saveRowSilently(array $importData) {
        try {
            $result = $this->saveRow($importData);
            return $result;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Process after import data
     * Init indexing process after catalog product import
     */
    public function finish() {
        /**
         * Back compatibility event
         */
        Mage::dispatchEvent($this->_eventPrefix . '_after', array());

        $entity = new Varien_Object();
        Mage::getSingleton('index/indexer')->processEntityAction(
                $entity, self::ENTITY, Mage_Index_Model_Event::TYPE_SAVE
        );
    }

}
