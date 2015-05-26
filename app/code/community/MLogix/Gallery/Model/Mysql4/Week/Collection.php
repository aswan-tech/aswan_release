<?php
/**
 * Magic Logix Gallery
 *
 * Provides an image gallery extension for Magento
 *
 * @category		MLogix
 * @package		Gallery
 * @author		Brady Matthews
 * @copyright		Copyright (c) 2008 - 2010, Magic Logix, Inc.
 * @license		http://creativecommons.org/licenses/by-nc-sa/3.0/us/
 * @link		http://www.magiclogix.com
 * @link		http://www.magentoadvisor.com
 * @since		Version 1.0
 *
 * Please feel free to modify or distribute this as you like,
 * so long as it's for noncommercial purposes and any
 * copies or modifications keep this comment block intact
 *
 * If you would like to use this for commercial purposes,
 * please contact me at brady@magiclogix.com
 *
 * For any feedback, comments, or questions, please post
 * it on my blog at http://www.magentoadvisor.com/plugins/gallery/
 *
 */
?><?php

class MLogix_Gallery_Model_Mysql4_Week_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('gallery/week');
    }
	
	public function addEnableFilter($status) {
        $this->getSelect()
                ->where('status = ?', $status);
        return $this;
    }
	
	public function addContentFilter($key = ''){
		$query_text_str = '';
		if(is_array($key)){
			foreach($key as $word){
				$query_text[] = '+'.$word;
			}
			$query_text_str = implode(' ',$query_text);
		}
		$bind = $query_text_str;
		
		$select = $this->getSelect();
		
		$tables_used = new Zend_Db_Expr('MATCH (main_table.heading,main_table.tags,main_table.description) AGAINST (:query IN BOOLEAN MODE)');
		
		$feild = new Zend_Db_Expr('(50 * (MATCH (main_table.heading) AGAINST (:query IN BOOLEAN MODE))) + (30 * (MATCH (main_table.tags) AGAINST (:query IN BOOLEAN MODE))) + (10 * (MATCH (main_table.description) AGAINST (:query IN BOOLEAN MODE)))');
						
		$this->getSelect()->columns(array('relevance' => $feild));
				
		$this->getSelect()->where($tables_used);
		
		$this->getSelect()->order(array('relevance desc','created_time desc'));
		
		$this->addBindParam(":query", $bind);
		
		return $this;
	}
	
	public function addPresentFilter() {
        $this->getSelect()
                ->where('created_time<=?', now());
        return $this;
    }
}