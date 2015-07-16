<?php
/**
* aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-ENTERPRISE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento ENTERPRISE edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento ENTERPRISE edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Blog
 * @version    1.1.1
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-ENTERPRISE.txt
 */


class AW_Blog_Model_Mysql4_Blog_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('blog/blog');
    }

    public function addEnableFilter($status) {
        $this->getSelect()
                ->where('status = ?', $status);
        return $this;
    }

    public function addPresentFilter() {
        $this->getSelect()
                ->where('created_time<=?', now());
        return $this;
    }

    public function addCatFilter($catId) {
        $this->getSelect()->join(
                        array('cat_table' => $this->getTable('post_cat')), 'main_table.post_id = cat_table.post_id', array()
                )
                ->where('cat_table.cat_id = ?', $catId);

        return $this;
    }

    public function addCatsFilter($catIds) {
        $this->getSelect()->join(
                        array('cat_table' => $this->getTable('post_cat')), 'main_table.post_id = cat_table.post_id', array()
                )
                ->where('cat_table.cat_id IN (' . $catIds . ')')
                ->group('cat_table.post_id')
        ;

        return $this;
    }

    /**
     * Add Filter by store
     *
     * @param int|Mage_Core_Model_Store $store
     * @return Mage_Cms_Model_Mysql4_Page_Collection
     */
    public function addStoreFilter($store = null, $withAdmin = true) {
        if ($store === null)
            $store = Mage::app()->getStore()->getId();
        if (!Mage::app()->isSingleStoreMode()) {
            if ($store instanceof Mage_Core_Model_Store) {
                $store = array($store->getId());
            }
			
			if ($withAdmin) {
				$this->getSelect()->joinLeft(
								array('store_table' => $this->getTable('store')), 'main_table.post_id = store_table.post_id', array()
						)
						->where('store_table.store_id in (?)', array(0, $store));
            } else {
				$this->getSelect()->joinLeft(
								array('store_table' => $this->getTable('store')), 'main_table.post_id = store_table.post_id', array()
						)
						->where('store_table.store_id = (?)', $store);
			}
			
            return $this;
        }
        return $this;
    }

    public function addTagFilter($tag) {
        if ($tag = trim($tag)) {
            $whereString = sprintf("main_table.tags = %s OR main_table.tags LIKE %s OR main_table.tags LIKE %s OR main_table.tags LIKE %s", $this->getConnection()->quote($tag), $this->getConnection()->quote($tag . ',%'), $this->getConnection()->quote('%,' . $tag), $this->getConnection()->quote('%,' . $tag . ',%')
            );
            $this->getSelect()->where($whereString);
        }
        return $this;
    }
	
	public function addContentFilter($key){
		$query_text_str = '';
		if(is_array($key)){
			foreach($key as $word){
				$query_text[] = '+'.$word;
			}
			$query_text_str = implode(' ',$query_text);
		}
		$bind = $query_text_str;
		
		$select = $this->getSelect();
		
		$tables_used = new Zend_Db_Expr('MATCH (main_table.post_content,main_table.title,main_table.tags) AGAINST (:query IN BOOLEAN MODE)');
		
		$feild = new Zend_Db_Expr('(50 * (MATCH (main_table.title) AGAINST (:query IN BOOLEAN MODE))) + (30 * (MATCH (main_table.tags) AGAINST (:query IN BOOLEAN MODE))) + (10 * (MATCH (main_table.post_content) AGAINST (:query IN BOOLEAN MODE)))');
						
		$this->getSelect()->columns(array('relevance' => $feild));
				
		$this->getSelect()->where('('.$tables_used.')');
		
		$this->getSelect()->order('relevance desc');
		
		$this->addBindParam(":query", $bind);
		
		return $this;
	}

}
