<?php
class FCM_Productreports_Block_Adminhtml_Report_Stockprice extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'productreports';
        $this->_controller = 'adminhtml_report_stockprice';
        $this->_headerText = Mage::helper('productreports')->__('Stock Price Report');
        $this->setTemplate('report/grid/container.phtml');
        parent::__construct();
        $this->_removeButton('add');
        $this->addButton('filter_form_submit', array(
            'label'     => Mage::helper('productreports')->__('Show Report'),
            'onclick'   => 'filterFormSubmit()'
        ));
		$this->_addButton('clear_filter', array(
            'label'     => Mage::helper('adminhtml')->__('Clear Filter'),
            'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/stockprice') . '\')',
            'class'     => 'delete',
        ),1,1);
    }
    public function getFilterUrl()
    {
        $this->getRequest()->setParam('filter', null);
        return $this->getUrl('*/*/stockprice', array('_current' => true));
    }
}