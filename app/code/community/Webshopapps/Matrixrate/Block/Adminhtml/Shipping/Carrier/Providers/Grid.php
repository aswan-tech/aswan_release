<?php

class Webshopapps_Matrixrate_Block_Adminhtml_Shipping_Carrier_Providers_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    /**
     * Prepare table columns
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns() {
        $this->addColumn('country_code', array(
            'header' => Mage::helper('adminhtml')->__('Country Code'),
            'index' => 'country_code',
            'default' => '*',
        ));
		
		$this->addColumn('zone', array(
            'header' => Mage::helper('adminhtml')->__('Zone'),
            'index' => 'zone',
            'default' => '*',
        ));
		
		$this->addColumn('delivery_type', array(
            'header' => Mage::helper('adminhtml')->__('Delivery Type'),
            'index' => 'delivery_type',
            'default' => '*',
        ));
		
		$this->addColumn('shipping_provider', array(
            'header' => Mage::helper('adminhtml')->__('Shipping Provider'),
            'index' => 'shipping_provider',
            'default' => '*',
        ));

        return Mage_Adminhtml_Block_Widget_Grid::_prepareColumns();
    }

    protected function _prepareCollection() {
        $collection = Mage::getResourceModel('matrixrate_shipping/carrier_providers_collection');
        $collection->setConditionFilter($this->getConditionName());
                //->setWebsiteFilter($this->getWebsiteId());
		
        $this->setCollection($collection);
		
        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }

    /**
     * Website filter
     *
     * @var int
     */
    protected $_websiteId;
    /**
     * Condition filter
     *
     * @var string
     */
    protected $_conditionName;

    /**
     * Define grid properties
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
        $this->setId('shippingProviderGrid');
        $this->_exportPageSize = 10000;
    }

    /**
     * Set current website
     *
     * @param int $websiteId
     * @return Mage_Adminhtml_Block_Shipping_Carrier_Tablerate_Grid
     */
    public function setWebsiteId($websiteId) {
        $this->_websiteId = Mage::app()->getWebsite($websiteId)->getId();
        return $this;
    }

    /**
     * Retrieve current website id
     *
     * @return int
     */
    public function getWebsiteId() {
        if (is_null($this->_websiteId)) {
            $this->_websiteId = Mage::app()->getWebsite()->getId();
        }
        return $this->_websiteId;
    }

    /**
     * Set current website
     *
     * @param int $websiteId
     * @return Mage_Adminhtml_Block_Shipping_Carrier_Tablerate_Grid
     */
    public function setConditionName($name) {
        $this->_conditionName = $name;
        return $this;
    }

    /**
     * Retrieve current website id
     *
     * @return int
     */
    public function getConditionName() {
        return $this->_conditionName;
    }

}