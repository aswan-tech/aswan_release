<?php

class Webshopapps_Matrixrate_Block_Adminhtml_Shipping_Carrier_Matrixrate_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    /**
     * Prepare table columns
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns() {
        $this->addColumn('website_id', array(
            'header' => Mage::helper('adminhtml')->__('Website Id'),
            'index' => 'website_id',
            'default' => '*',
        ));
		
		$this->addColumn('zone', array(
            'header' => Mage::helper('adminhtml')->__('Zone'),
            'index' => 'zone',
            'default' => '*',
        ));
		
        $label = Mage::getSingleton('matrixrate/carrier_matrixrate')
                        ->getCode('condition_name_short', $this->getConditionName());

        $this->addColumn('condition_from_value', array(
            'header' => $label . ' From',
            'index' => 'condition_from_value',
        ));

        $this->addColumn('condition_to_value', array(
            'header' => $label . ' To',
            'index' => 'condition_to_value',
        ));

        $this->addColumn('shipping_charge', array(
            'header' => Mage::helper('adminhtml')->__('Shipping Charge(%)'),
            'index' => 'shipping_charge',
        ));

        return Mage_Adminhtml_Block_Widget_Grid::_prepareColumns();
    }

    protected function _prepareCollection() {
        $collection = Mage::getResourceModel('matrixrate_shipping/carrier_matrixrate_collection');
        $collection->setConditionFilter($this->getConditionName())
                ->setWebsiteFilter($this->getWebsiteId());

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
        $this->setId('shippingTablerateGrid');
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