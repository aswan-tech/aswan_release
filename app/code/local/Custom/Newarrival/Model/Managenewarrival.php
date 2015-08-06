<?php

/*
 * File name: Managenewarrival.php
 * Description: This file is used to manage module (add, edit, delete and listing) new arrival men and women for backend
 * 
 * @created date:
 * @modify date:
 * @auther: Sanjay Kumar <sanjay.kumar@taslc.com>
 * @version: 1.0
 * @copyright: American Swan
 * 
 */
 
/*
 * class name: As_Newarrival_Model_Managenewarrival
 * Description: It is used to manage model (add, edit, delete and list) As_Newarrival_Model_Managenewarrival
 * 
 * @created date:
 * @modify date:
 * @auther: Sanjay Kumar <sanjay.kumar@taslc.com>
 * @version: 1.0
 * @copyright: American Swan
 * @package new arrival
 */
 
class Custom_Newarrival_Model_Managenewarrival extends Mage_Core_Model_Abstract
{
	/*
     * _construct():- This function is used to define table name, get connection read and write and define module name 
     * @param null
     * @return null
     */
     
	var $_tableName = null;
	var $_readConn 	= null; 
	var $_writeConn = null;
	
    public function _construct()
    {
        parent::_construct();
        $this->_init('newarrival/managenewarrival');
        $this->_tableName = Mage::getSingleton( 'core/resource' )->getTableName('menwomen_newarrival');
		$this->_readConn = Mage::getModel('core/resource')->getConnection('core_read');
        $this->_writeConn = Mage::getModel('core/resource')->getConnection('core_write');
    }
    
     /*
     * saveData():- This function is used to insert data in database
     * @param $data: string
     * @return null
     */
     
    public function saveData($data)
    {
		$this->_writeConn->beginTransaction();
		$this->_writeConn->insert($this->_tableName, $data);
		$this->_writeConn->commit(); 
    }
    
     /*
     * updateData() :- This function is used to update data in database
     * @param $data: string and integer
     * @return null
     */
     
    public function updateData($data) {
		if(isset($data['newarrival_id'])){
			$id = $data['newarrival_id'];
			unset($data['newarrival_id']);
			$this->_writeConn->beginTransaction();
			$where = $this->_writeConn->quoteInto('newarrival_id=?',$id);
			$this->_writeConn->update($this->_tableName, $data, $where);
			$this->_writeConn->commit(); 
		}	
	}
	
	 /*
     * checkUnique() :- This function is used to check unique cat id from database
     * @param $cat_id : integer
     * @return object
     */
     
	public function checkUnique($cat_id) {
		
		 $query 	= "SELECT cat_id FROM ". $this->_tableName ." WHERE cat_id = '$cat_id'";
		 return $this->_readConn->fetchRow($query);
	}
	
	 /*
     * newArrivalListing() :- This function is used to select data from dataabase
     * @param null
     * @return object
     */
     
	public function newArrivalListing()
    {
		$sql = $this->_readConn->select()
			->from($this->_tableName , array('*'))
			->order('newarrival_id DESC');
		return $this->_readConn->fetchAll($sql); 
	}

	/*
     * getNewArrivalMenWomenById() :- This function is used to select data by newarrival_id from dataabase
     * @param $newarrival_id integer
     * @return object
     */
     
	public function getNewArrivalMenWomenById($newarrival_id)
    {
		$sql = $this->_readConn->select()
			->from($this->_tableName , array('*')) 
			->where('newarrival_id=?',$newarrival_id) ;             
		return $this->_readConn->fetchAll($sql); 	
	}
	
	/*
     * getProductByCatId() :- This function is used to get random product of topwear For MEN  and bottomwear for women and 
     * custom product through sku code for men and women from dataabase
     * @param $catid integer
     * @return object
     */
     
	public function getProductByCatId($catid) {
		$sql = $this->_readConn->select()
			->from($this->_tableName , array('is_default', 'sku')) 
			->where('cat_id=?', $catid) ;
		$data = $this->_readConn->fetchRow($sql);
		
		if($data['is_default'] == 1) {
			return $this->getRondomProductByCatId($catid);
		}
		else if($data['is_default'] == 0) {
			return $this->getCustomProductByCatId($catid);
		}
	}
	
	/*
     * getCustomProductByCatId() :- This function is used to get product of men and women from sku code from dataabase
     * @param  $catid integer
     * @return object
     */
     
	public function getCustomProductByCatId($catid) {
		
		$sql = $this->_readConn->select()
			->from($this->_tableName , array('*')) 
			->where('is_default=?', '0')
			->where('cat_id=?', $catid) ;
		$getData = $this->_readConn->fetchAll($sql); 
		$explode = explode(',', $getData[0]['sku']);
		$productCollection = Mage::getResourceModel('catalog/product_collection');
		$productCollection->addAttributeToSelect('*');
		$productCollection->addAttributeToFilter('sku', array('in' => $explode));
		return $productCollection;		
	}
	
	/*
     * getRondomProductByCatId() :- This function is used to get random product of men and women 
     * @param  $catid integer
     * @return object
     */
     
	public function getRondomProductByCatId($catid) {
		$catIDArr = array();
		if($catid == 6) {
			$catIDArr = array(344,158);
		}
		else if($catid == 8) {
			$catIDArr = array(348,349);
		}
		if(count($catIDArr)) {
			$collection = Mage::getModel('catalog/product')->getCollection();
			$collection->addAttributeToFilter('status', 1);
			$collection->addAttributeToFilter('type_id', array('eq' => 'configurable'));
			$collection->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id = entity_id', null, 'left');
			$collection->addAttributeToSelect('*');
			$collection->getSelect()->limit(5);
			$collection->addAttributeToFilter('category_id', array('in' => $catIDArr));
			$collection->getSelect()->order(new Zend_Db_Expr('RAND()'));
			$collection->getSelect()->group('entity_id');
			return $collection;
		}
	} 
 }
