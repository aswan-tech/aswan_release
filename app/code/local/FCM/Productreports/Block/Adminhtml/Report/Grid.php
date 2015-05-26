<?php

class FCM_Productreports_Block_Adminhtml_Report_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	protected $_filters = array();
	
	 /**
     * stores current currency code
     */
    protected $_currentCurrencyCode = null;

     public function __construct()
    {
        parent::__construct();
        $this->setFilterVisibility(false);
		$this->setUseAjax(false);
    }
 
    protected function _prepareCollection()
    {
		return parent::_prepareCollection();
    }
	
    protected function _prepareColumns()
    {		
        return parent::_prepareColumns();
    }

	protected function _setFilterValues($data)
    {
        foreach ($data as $name => $value) {
            //if (isset($data[$name])) {
                $this->setFilter($name, $data[$name]);
            //}
        }
        return $this;
    }
	
	/**
     * Retrieve correct currency code for select website, store, group
     *
     * @return string
     */
    public function getCurrentCurrencyCode()
    {
        if (is_null($this->_currentCurrencyCode)) {
            if ($this->getRequest()->getParam('store')) {
                $store = $this->getRequest()->getParam('store');
                $this->_currentCurrencyCode = Mage::app()->getStore($store)->getBaseCurrencyCode();
            } else if ($this->getRequest()->getParam('website')){
                $website = $this->getRequest()->getParam('website');
                $this->_currentCurrencyCode = Mage::app()->getWebsite($website)->getBaseCurrencyCode();
            } else if ($this->getRequest()->getParam('group')){
                $group = $this->getRequest()->getParam('group');
                $this->_currentCurrencyCode =  Mage::app()->getGroup($group)->getWebsite()->getBaseCurrencyCode();
            } else {
                $this->_currentCurrencyCode = Mage::app()->getStore()->getBaseCurrencyCode();
            }
        }
        return $this->_currentCurrencyCode;
    }
	
	public function setFilter($name, $value)
    {
        if ($name) {
            $this->_filters[$name] = $value;
        }
    }

    public function getFilter($name)
    {
        if (isset($this->_filters[$name])) {
            return $this->_filters[$name];
        } else {
            return ($this->getRequest()->getParam($name))
                    ?htmlspecialchars($this->getRequest()->getParam($name)):'';
        }
    }
	
	public function getRowUrl($row) {
        return false;
    }
}