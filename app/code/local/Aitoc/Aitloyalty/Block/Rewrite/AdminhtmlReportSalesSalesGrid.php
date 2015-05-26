<?php
/**
 * Product:     Loyalty Program for Enterprise Edition
 * Package:     Aitoc_Aitloyalty_10.0.10_574534
 * Purchase ID: INzRIwyyaNoeOLERhAgt4U28qVKIeEa3dfPrgaAN3C
 * Generated:   2013-05-13 06:36:55
 * File path:   app/code/local/Aitoc/Aitloyalty/Block/Rewrite/AdminhtmlReportSalesSalesGrid.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitloyalty')){ ZSegBBZTWMZrRmUW('88d0225952770a704a7927ff83e8a664'); ?><?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml sales report grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

if (version_compare(Mage::getVersion(), '1.4.1.0', 'ge'))
{
    class Aitoc_Aitloyalty_Block_Rewrite_AdminhtmlReportSalesSalesGrid extends Mage_Adminhtml_Block_Report_Sales_Sales_Grid
    {
        protected function _prepareColumns()
        {
            $return = parent::_prepareColumns();

            unset($this->_columns['period']);
            unset($this->_columns['orders_count']);
            unset($this->_columns['total_qty_ordered']);
            unset($this->_columns['total_qty_invoiced']);
            unset($this->_columns['total_income_amount']);
            unset($this->_columns['total_revenue_amount']);
            unset($this->_columns['total_profit_amount']);
            unset($this->_columns['total_invoiced_amount']);
            unset($this->_columns['total_paid_amount']);
            unset($this->_columns['total_refunded_amount']);
            unset($this->_columns['total_tax_amount']);
            unset($this->_columns['total_tax_amount_actual']);
            unset($this->_columns['total_shipping_amount']);
            unset($this->_columns['total_shipping_amount_actual']);
            unset($this->_columns['total_discount_amount']);
            unset($this->_columns['total_discount_amount_actual']);
            unset($this->_columns['total_canceled_amount']);

            if ($this->getFilterData()->getStoreIds()){
                $this->setStoreIds(explode(',', $this->getFilterData()->getStoreIds()));
            }

            $currencyCode = $this->getCurrentCurrencyCode();

            $this->addColumn('period', array(
                'header'        => Mage::helper('sales')->__('Period'),
                'index'         => 'period',
                'width'         => 100,
                'sortable'      => false,
                'period_type'   => $this->getPeriodType(),
                'renderer'      => 'adminhtml/report_sales_grid_column_renderer_date',
                'totals_label'  => Mage::helper('sales')->__('Total'),
                'html_decorators' => array('nobr'),
            ));

            $this->addColumn('orders_count', array(
                'header'    =>Mage::helper('reports')->__('Number of Orders'),
                'index'     => 'orders_count',
                'type'      => 'number',
                'total'     => 'sum',
                'sortable'  => false
            ));

            $this->addColumn('total_qty_ordered', array(
                'header'    =>Mage::helper('reports')->__('Items Ordered'),
                'index'     => 'total_qty_ordered',
                'type'      => 'number',
                'total'     => 'sum',
                'sortable'  => false
            ));

            $this->addColumn('total_tax_amount', array(
                'header'    =>Mage::helper('reports')->__('Tax'),
                'type'          => 'currency',
                'currency_code' => $currencyCode,
                'index'         => 'total_tax_amount',
                'total'         => 'sum',
                'sortable'      => false
            ));

            $this->addColumn('total_shipping_amount', array(
                'header'    =>Mage::helper('reports')->__('Shipping'),
                'type'          => 'currency',
                'currency_code' => $currencyCode,
                'index'         => 'total_shipping_amount',
                'total'         => 'sum',
                'sortable'      => false
            ));

            $this->addColumn('total_discount_amount', array(
                'header'    =>Mage::helper('reports')->__('Discounts(-)'),
                'type'          => 'currency',
                'currency_code' => $currencyCode,
                'index'         => 'total_discount_amount',
                'total'         => 'sum',
                'sortable'      => false
            ));

            $this->addColumn('total_income_amount', array(
                'header'    =>Mage::helper('reports')->__('Total'),
                'type'          => 'currency',
                'currency_code' => $currencyCode,
                'index'         => 'total_income_amount',
                'total'         => 'sum',
                'sortable'      => false
            ));

            $this->addColumn('total_invoiced_amount', array(
                'header'        => Mage::helper('sales')->__('Invoiced'),
                'type'          => 'currency',
                'currency_code' => $currencyCode,
                'index'         => 'total_invoiced_amount',
                'total'         => 'sum',
                'sortable'      => false
            ));

            $this->addColumn('total_canceled_amount', array(
                'header'    =>Mage::helper('reports')->__('Refunded'),
                'type'          => 'currency',
                'currency_code' => $currencyCode,
                'index'         => 'total_canceled_amount',
                'total'         => 'sum',
                'sortable'      => false
            ));

            $this->addColumn('total_canceled_amount', array(
                'header'    =>Mage::helper('reports')->__('Refunded'),
                'type'          => 'currency',
                'currency_code' => $currencyCode,
                'index'         => 'total_canceled_amount',
                'total'         => 'sum',
                'sortable'      => false
            ));

            return $return;
        }
    }
}
elseif (version_compare(Mage::getVersion(), '1.4.0.0', 'ge') && version_compare(Mage::getVersion(), '1.4.1.0', 'lt'))
{
    class Aitoc_Aitloyalty_Block_Rewrite_AdminhtmlReportSalesSalesGrid extends Mage_Adminhtml_Block_Report_Grid
    {
        public function __construct()
        {
            parent::__construct();
            $this->setId('gridSales');
        }

        protected function _prepareCollection()
        {
            parent::_prepareCollection();
            $this->getCollection()->initReport('reports/order_collection');
        }

        protected function _prepareColumns()
        {
            $this->addColumn('orders', array(
                'header'    =>Mage::helper('reports')->__('Number of Orders'),
                'index'     =>'orders',
                'total'     =>'sum',
                'type'      =>'number'
            ));

            $this->addColumn('items', array(
                'header'    =>Mage::helper('reports')->__('Items Ordered'),
                'index'     =>'items',
                'total'     =>'sum',
                'type'      =>'number'
            ));

            $currency_code = $this->getCurrentCurrencyCode();

            $this->addColumn('subtotal', array(
                'header'    =>Mage::helper('reports')->__('Subtotal'),
                'type'      =>'currency',
                'currency_code' => $currency_code,
                'index'     =>'subtotal',
                'total'     =>'sum',
                'renderer'  =>'adminhtml/report_grid_column_renderer_currency'
            ));

            $this->addColumn('tax', array(
                'header'    =>Mage::helper('reports')->__('Tax'),
                'type'      =>'currency',
                'currency_code' => $currency_code,
                'index'     =>'tax',
                'total'     =>'sum',
                'renderer'  =>'adminhtml/report_grid_column_renderer_currency'
            ));

            $this->addColumn('shipping', array(
                'header'    =>Mage::helper('reports')->__('Shipping'),
                'type'      =>'currency',
                'currency_code' => $currency_code,
                'index'     =>'shipping',
                'total'     =>'sum',
                'renderer'  =>'adminhtml/report_grid_column_renderer_currency'
            ));

            $this->addColumn('discount', array(
                'header'    =>Mage::helper('reports')->__('Discounts(-)'),
                'type'      =>'currency',
                'currency_code' => $currency_code,
                'index'     =>'discount',
                'total'     =>'sum',
                'renderer'  =>'adminhtml/report_grid_column_renderer_currency'
            ));

            $this->addColumn('total', array(
                'header'    =>Mage::helper('reports')->__('Total'),
                'type'      =>'currency',
                'currency_code' => $currency_code,
                'index'     =>'total',
                'total'     =>'sum',
                'renderer'  =>'adminhtml/report_grid_column_renderer_currency'
            ));

            $this->addColumn('invoiced', array(
                'header'    =>Mage::helper('reports')->__('Invoiced'),
                'type'      =>'currency',
                'currency_code' => $currency_code,
                'index'     =>'invoiced',
                'total'     =>'sum',
                'renderer'  =>'adminhtml/report_grid_column_renderer_currency'
            ));

            $this->addColumn('refunded', array(
                'header'    =>Mage::helper('reports')->__('Refunded'),
                'type'      =>'currency',
                'currency_code' => $currency_code,
                'index'     =>'refunded',
                'total'     =>'sum',
                'renderer'  =>'adminhtml/report_grid_column_renderer_currency'
            ));

            $this->addExportType('*/*/exportSalesCsv', Mage::helper('reports')->__('CSV'));
            $this->addExportType('*/*/exportSalesExcel', Mage::helper('reports')->__('Excel'));

            return parent::_prepareColumns();
        }
    }
} } 