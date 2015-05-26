<?php
/**
* @copyright Amasty.
*/
class Amasty_Stockstatus_Block_Ranges extends Mage_Core_Block_Template
{
    protected $_optionsCollection;
    
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('amstockstatus/ranges.phtml');
    }
    
    public function getOptionsCollection()
    {
        if (!$this->_optionsCollection)
        {
            $this->_optionsCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
                                            ->setAttributeFilter(Mage::registry('entity_attribute')->getId())
                                            ->setPositionOrder('desc', true)
                                            ->load();
        }
        return $this->_optionsCollection;
    }
    
    public function getRanges()
    {
        $collection = Mage::getModel('amstockstatus/range')->getCollection();
        $collection->getSelect()->order('qty_from');
        return $collection;
    }
}