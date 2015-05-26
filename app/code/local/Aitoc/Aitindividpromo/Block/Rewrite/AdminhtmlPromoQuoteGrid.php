<?php
/**
 * Product:     Individual Promotions for Magento Enterpise Edition
 * Package:     Aitoc_Aitindividpromo_10.0.7_574525
 * Purchase ID: UjgdLvjpFE0u1HHQEOk2KNCXazbZ9kQjUnTtO4dMb0
 * Generated:   2013-05-13 06:35:45
 * File path:   app/code/local/Aitoc/Aitindividpromo/Block/Rewrite/AdminhtmlPromoQuoteGrid.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitindividpromo')){ RTZjakZjrwCTsako('ec57b280fcd7bf894658a1629c77b94b'); ?><?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */

class Aitoc_Aitindividpromo_Block_Rewrite_AdminhtmlPromoQuoteGrid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('promo_quote_grid');
        $this->setDefaultSort('sort_order');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
		$oResource = Mage::getSingleton('core/resource');
		$sTable = $oResource->getTableName('aitoc_salesrule_assign_cutomer');        
        
        $collection = Mage::getResourceModel('salesrule/rule_collection');

        $collection->getSelect()->joinLeft(array('rc' => $sTable), 'main_table.rule_id = rc.entity_id', 'rc.customer_id');
        $collection->getSelect()->group('main_table.rule_id');
        
        if (version_compare(Mage::getVersion(), '1.12.0.0', '>='))
        {        
            $collection->getSelect()->joinLeft(array('scg' => $oResource->getTableName('salesrule_customer_group')), 'main_table.rule_id = scg.rule_id', 'scg.customer_group_id');
            $this->getColumn('customer_group_ids')->setFilterIndex('customer_group_id');
        }
        
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('rule_id', array(
            'header'    => Mage::helper('salesrule')->__('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'rule_id',
        ));

        $this->addColumn('name', array(
            'header'    => Mage::helper('salesrule')->__('Rule Name'),
            'align'     =>'left',
            'index'     => 'name',
        ));

        $this->addColumn('coupon_code', array(
            'header'    => Mage::helper('salesrule')->__('Coupon Code'),
            'align'     => 'left',
            'width'     => '150px',
            'index'     => 'code',
        ));

        $this->addColumn('from_date', array(
            'header'    => Mage::helper('salesrule')->__('Date Start'),
            'align'     => 'left',
            'width'     => '120px',
            'type'      => 'date',
            'index'     => 'from_date',
        ));

        $this->addColumn('to_date', array(
            'header'    => Mage::helper('salesrule')->__('Date Expire'),
            'align'     => 'left',
            'width'     => '120px',
            'type'      => 'date',
            'default'   => '--',
            'index'     => 'to_date',
        ));

        $this->addColumn('is_active', array(
            'header'    => Mage::helper('salesrule')->__('Status'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'is_active',
            'type'      => 'options',
            'options'   => array(
                1 => 'Active',
                0 => 'Inactive',
            ),
        ));

        // start aitoc add
        
        $oAitindividpromo = Mage::getModel('aitindividpromo/aitindividpromo');
        
        $aCustomerGroupHash = $oAitindividpromo->getCustomerGroups(false);
        
        $this->addColumn('customer_group_ids', array(
            'header'    => Mage::helper('salesrule')->__('Groups'),
            'align'     => 'left',
            'width'     => '180px',
            'index'     => 'customer_group_ids',
            'renderer'  => 'aitindividpromo/widget_gridColumnRendererGroup',
            'filter'    => 'aitindividpromo/widget_gridColumnFilterGroup',
            'options'   => $aCustomerGroupHash,
        ));
        
        $aCustomerHash = $this->getCustomerHash();
        
        $this->addColumn('customer_ids', array(
            'header'    => Mage::helper('salesrule')->__('Customers'),
            'align'     => 'left',
            'width'     => '270px',
            'index'     => 'rc.customer_id',
            'renderer'  => 'aitindividpromo/widget_gridColumnRendererCustomer',
            'filter'    => 'adminhtml/widget_grid_column_filter_select',
            'options'   => $aCustomerHash,
        ));
        
        // finish aitoc add
        
        $this->addColumn('sort_order', array(
            'header'    => Mage::helper('salesrule')->__('Priority'),
            'align'     => 'right',
            'index'     => 'sort_order',
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getRuleId()));
    }

    protected function _afterLoadCollection()
    {
        $oCollection = $this->getCollection();
        
        $aIdHash = array();
        $aRuleCustomerHash = array();
        
        foreach ($oCollection->load() as $oCustomer)
        {
            if ($oCustomer->getId())
            {
                $aIdHash[] = $oCustomer->getId();
            }
        }
        
        if ($aIdHash)
        {
            $aCustomerHash = $this->getCustomerHash();
            
    		$oResource = Mage::getSingleton('core/resource');
    		$sTable = $oResource->getTableName('aitoc_salesrule_assign_cutomer');        
    
            $oDb = Mage::getSingleton('core/resource')->getConnection('core_write');
        
            $oSelect    = $oDb  ->select()
                                ->from($sTable, array('entity_id', 'customer_id'))
                                ->where('entity_id IN (' . implode(',', $aIdHash) . ')');
            
            $aCustomerValues = $oDb->fetchAll($oSelect); 
            
            if ($aCustomerValues)
            {
                foreach ($aCustomerValues as $aData)
                {
                    if (isset($aCustomerHash[$aData['customer_id']]))
                    {
                        $aRuleCustomerHash[$aData['entity_id']][$aData['customer_id']] = $aCustomerHash[$aData['customer_id']];
                    }
                }
            }
        }

		Mage::register('aitindividpromo_data', $aRuleCustomerHash);
        
        return parent::_afterLoadCollection();
    }

    protected $_aCustomerHash = array();
    
    protected function getCustomerHash()
    {
        if ($this->_aCustomerHash) return $this->_aCustomerHash;

		$oResource = Mage::getSingleton('core/resource');
		$sTable = $oResource->getTableName('aitoc_salesrule_assign_cutomer');        

        $oDb = Mage::getSingleton('core/resource')->getConnection('core_write');
    
        $oSelect    = $oDb  ->select()
                            ->from($sTable, array('customer_id'))
                            ->group('customer_id');
        
        $aCustomerIds = $oDb->fetchCol($oSelect);       
       
        if ($aCustomerIds)
        {
            $oCustomerCollection = Mage::getResourceModel('customer/customer_collection')
                ->addNameToSelect()
                ->addAttributeToFilter('entity_id', $aCustomerIds)
                ->load();
                                    
            foreach ($oCustomerCollection as $aCustomer)
            {
                $sCustomerFullName = $aCustomer->getName() . ' (' . $aCustomer->getEmail() .  ')';
                $this->_aCustomerHash[$aCustomer->getId()] = $sCustomerFullName;
            }
        }
        
        return $this->_aCustomerHash;
    }
    
} } 