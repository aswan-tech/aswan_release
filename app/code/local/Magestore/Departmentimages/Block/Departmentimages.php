<?php
class Magestore_DepartmentImages_Block_DepartmentImages extends Mage_Catalog_Block_Product_Abstract
{
	private $_display = '0';

	public function _prepareLayout()	{
		return parent::_prepareLayout();
	}

	public function getDepartmentImages() {
		if (!$this->hasData('departmentimages')) {
			$this->setData('departmentimages', Mage::registry('departmentimages'));
		}
		return $this->getData('departmentimages');
	}

	public function setDisplay($display){
		$this->_display = $display;
	}

	public function getDepartmentCollection() {
		
		$limit= (int) Mage::getStoreConfig('departmentimages/settings/images_display');
		
		if(!isset($limit) || $limit > 6) {
			$limit=6;
		}
		
		$collection = Mage::getModel('departmentimages/departmentimages')->getCollection();		
		$collection->addFieldToFilter('status',1);
		$collection->getSelect()->limit($limit);
		$collection->getSelect()->order('sort_id', 'asc');
		
		if ($this->_display == Magestore_Departmentimages_Helper_Data::DISP_CATEGORY){
			$current_category = Mage::registry('current_category')->getId();
			$collection->addFieldToFilter('categories',array('finset' => $current_category));
		}

		$current_store = Mage::app()->getStore()->getId();
		$categorys = array();
		
		$currentCategory = Mage::registry('current_category');
				
		foreach ($collection as $category) {
		if($currentCategory) {
			$catid = $currentCategory->getId();
				if($category->getcategory() == $catid) {
					$stores = explode(',',$category->getStores());
					if (in_array(0,$stores) || in_array($current_store,$stores))
					//if ($category->getStatus())
					$categorys[] = $category;
				}
		} else {
					$stores = explode(',',$category->getStores());
					if (in_array(0,$stores) || in_array($current_store,$stores))
					//if ($category->getStatus())
					$categorys[] = $category;		
		}
		}
		return $categorys;
	}

	public function getDelayTime() {
		$delay = (int) Mage::getStoreConfig('departmentimages/settings/time_delay');
		$delay = $delay * 1000;
		return $delay;
	}

	public function isShowDescription(){
		return (int)Mage::getStoreConfig('departmentimages/settings/show_description');
	}

	public function getListStyle(){
		return (int)Mage::getStoreConfig('departmentimages/settings/list_style');
	}

	public function getImageWidth() {
		return (int)Mage::getStoreConfig('departmentimages/settings/image_width');
	}

	public function getImageHeight() {
		return (int)Mage::getStoreConfig('departmentimages/settings/image_height');
	}

	public function getProductCollection(){
		if (is_null($this->_productCollection)) {
			$this->_productCollection = Mage::getResourceModel('catalogsearch/advanced_collection')
			->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
			->addMinimalPrice()
			->addStoreFilter();
			Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($this->_productCollection);
			Mage::getSingleton('catalog/product_visibility')->addVisibleInSearchFilterToCollection($this->_productCollection);

				
			//$this->_productCollection = Mage::getModel('catalog/product')->getCollection();
				
			/* include special price from and to date filtering */
			//if(isset($_GET['special_price'])) {
			$todayDate = date('m/d/y');
			$tomorrow = mktime(0, 0, 0, date('m'), date('d')+1, date('y'));
			$tomorrowDate = date('m/d/y', $tomorrow);

			$this->_productCollection->addAttributeToFilter('special_from_date', array('date' => true, 'to' => $todayDate))
			->addAttributeToFilter('special_to_date', array('or'=> array(
			0 => array('date' => true, 'from' => $tomorrowDate),
			1 => array('is' => new Zend_Db_Expr('null')))
			), 'left');
			//}
		}
		return $this->_productCollection;
	}
	
    public function getAddToCartUrl($product, $additional = array()) {
        if ($this->helper('icart')->isEnabled()) {
            return $this->helper('icart')->getAddUrl($product, $additional);
        } else {
            return parent::getAddToCartUrl($product, $additional);
        }
    }

}