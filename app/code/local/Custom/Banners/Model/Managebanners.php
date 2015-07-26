<?php

/*
 * File name: Managebanners.php
 * Description: This file is used to manage module (add, edit, delete and listing) banners for backend
 * 
 * @created date:
 * @modify date:
 * @auther: Sanjay Kumar <sanjay.kumar@taslc.com>
 * @version: 1.0
 * @copyright: American Swan
 * 
 */
 
/*
 * class name: Aswan_Banners_Model_Managebanners
 * Description: It is used to manage model (add, edit, delete and list) banners for bakend
 * 
 * @created date:
 * @modify date:
 * @auther: Sanjay Kumar <sanjay.kumar@taslc.com>
 * @version: 1.0
 * @copyright: American Swan
 * @package Banner
 */
 class Custom_Banners_Model_Managebanners extends Mage_Core_Model_Abstract
 {
	var $_tableName = null;
	var $_readConn = null; 
	var $_writeConn = null;
	
    public function _construct()
    {
        parent::_construct();
        $this->_init('banners/managebanners');
        $this->_tableName = Mage::getSingleton( 'core/resource' )->getTableName('banners');
        $this->_readConn = Mage::getModel('core/resource')->getConnection('core_read');
        $this->_writeConn = Mage::getModel('core/resource')->getConnection('core_write');
    }
    
     /*
     * listingBanner() is used to show all banners for backend
     * @param Null
     * @return Null
     */
     
    public function listingBanner()
    {
		$query 			= "SELECT * FROM ". $this->_tableName ." ORDER BY banner_id DESC";
		return $this->_readConn->fetchAll($query);			
	}
	
	 /*
     * addBanners() is used to add banners form backend
     * @param Null
     * @return string
     */
     
    public function addBanners($data)
    {
		$this->_writeConn->beginTransaction();		
		$this->_writeConn->insert($this->_tableName, $data);
		$this->_writeConn->commit(); 
    }
    
    /*
     * editBanners() is used to update banners form backend
     * @param $data array()
     * @return string,int
     */
     
    public function editBanners($data)
    {
		if(isset($data['banner_id'])){
			$id = $data['banner_id'];
			unset($data['banner_id']);
			$this->_writeConn->beginTransaction();
			$where = $this->_writeConn->quoteInto('banner_id=?',$id);
			$this->_writeConn->update($this->_tableName, $data , $where);
			$this->_writeConn->commit(); 
		}		
    }
    
      /*
     * deleteBanners() is used to delete banners form backend
     * @param $bid Integer
     * @return int
     */
     
    public function deleteBanners($bid){		
		$sql 			= "DELETE FROM ".$this->_tableName." WHERE banner_id  = '$bid'";		
		return $this->_writeConn->query($sql);
	}
	
	  /*
     * getBannersById() is used to fetech banners throught bid
     * @param $bid Integer
     * @return int
     */
     
    public function getBannersById($bid){
		$query 			= "SELECT * FROM ".$this->_tableName ." WHERE banner_id  = $bid";
		return $this->_readConn->fetchAll($query);		
	}
	
	  /*
     * getHomePageBanners() is used to show banners for home page slider with time interval
     * @param null
     * @return null
     */
     
	public function getHomePageBanners(){
		$curDate 		= Mage::getModel('core/date')->date('Y-m-d H:i:s');
		$sql			= "SELECT * FROM ".$this->_tableName." WHERE 
							start_date <= '$curDate' AND 
							end_date >= '$curDate' AND
							banner_status = 1";
		
		return $this->_readConn->fetchAll($sql);
	}
 }
