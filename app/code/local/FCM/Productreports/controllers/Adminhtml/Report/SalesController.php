<?php

class FCM_Productreports_Adminhtml_Report_SalesController extends Mage_Adminhtml_Controller_Report_Abstract {

    public function _initAction() {
        $this->loadLayout()
                ->_addBreadcrumb(Mage::helper('productreports')->__('Order'), Mage::helper('productreports')->__('Detailed Report'));
        return $this;
    }

    public function detailedordersAction() {
        $this->_title($this->__('Reports'))->_title($this->__('Order'))->_title($this->__('Detailed Report'));

        $this->_initAction()
                ->_setActiveMenu('report/productreport/sales')
                ->_addBreadcrumb(Mage::helper('productreports')->__('Order'), Mage::helper('productreports')->__('Detailed Report'));

        $gridBlock = $this->getLayout()->getBlock('productreports.ordersdetailed.grid.container');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction(array(
            $gridBlock,
            $filterFormBlock
        ));

        $this->renderLayout();
    }

    public function exportWorkflowCsvAction() {
        $fileName = 'detailedorders.csv';
        $grid = $this->getLayout()->createBlock('productreports/adminhtml_report_ordersdetailed_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile($fileName));
    }

    public function exportWorkflowXmlAction() {
        //throwing the error in generating the XML because of rupee symbol (` sign)
        $fileName = 'detailedorders.xml';
        $grid = $this->getLayout()->createBlock('productreports/adminhtml_report_ordersdetailed_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }

    public function reloadcategoriesAction() {
        $ctId = $this->getRequest()->getParam('ctgid');

        $categories = Mage::helper('productreports')->getProductCategories($ctId);

        $options = "";

        if (!empty($ctId)) {
            foreach ($categories as $id => $name) {
                $options .= "<option value='" . $id . "'>" . $name . "</option>";
            }
        } else {
            $options = "<option value=''>" . Mage::helper('productreports')->__('Select') . "</option>";
        }

        echo $options;       
    }

}