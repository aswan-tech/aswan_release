<?php
class FCM_Productreports_Block_Adminhtml_Report_Stock extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'productreports';
        $this->_controller = 'adminhtml_report_stock';
        $this->_headerText = Mage::helper('productreports')->__('Stock Report');
        $this->setTemplate('report/grid/container.phtml');
        parent::__construct();
        $this->_removeButton('add');
        $this->addButton('filter_form_submit', array(
            'label'     => Mage::helper('productreports')->__('Show Report'),
            'onclick'   => 'filterFormSubmit()'
        ));
		$this->_addButton('clear_filter', array(
            'label'     => Mage::helper('adminhtml')->__('Clear Filter'),
            'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/stock') . '\')',
            'class'     => 'delete',
        ),1,1);
    }
    public function getFilterUrl()
    {
        $this->getRequest()->setParam('filter', null);
        return $this->getUrl('*/*/stock', array('_current' => true));
    }
}