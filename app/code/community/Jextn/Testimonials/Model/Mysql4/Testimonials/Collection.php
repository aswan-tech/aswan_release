<?php

class Jextn_Testimonials_Model_Mysql4_Testimonials_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('testimonials/testimonials');
    }
	public function addIsActiveFilter()
    {
        $this->addFilter('status', 1);
        return $this;
    }
	public function addSidebarFilter()
    {
        $this->addFilter('sidebar', 1);
        return $this;
    }
	
	public function addFooterFilter()
    {
        $this->addFilter('footer', 1);
        return $this;
    }
}