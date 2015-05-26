<?php

class FCM_Productreports_Adminhtml_Report_ProductController extends Mage_Adminhtml_Controller_Report_Abstract
{
 
    public function _initAction()
    {
        $this->loadLayout()
        ->_addBreadcrumb(Mage::helper('productreports')->__('Reports'), Mage::helper('productreports')->__('Products'));
        return $this;
    }
	
	public function workflowAction()
    {
        $this->_title($this->__('Reports'))->_title($this->__('Products'))->_title($this->__('WorkFlow Stage Report'));
 
        $this->_initAction()
        ->_setActiveMenu('report/productreport/workflow')
        ->_addBreadcrumb(Mage::helper('productreports')->__('WorkFlow Stage Report'), Mage::helper('productreports')->__('WorkFlow Stage Report'));
 
        $gridBlock = $this->getLayout()->getBlock('productreports.workflow.grid.container');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');
 
        $this->_initReportAction(array(
        $gridBlock,
        $filterFormBlock
        ));
		
        $this->renderLayout();
 
    }
	
	public function exportWorkflowCsvAction()
    {
        $fileName   = 'workflow.csv';
        $grid       = $this->getLayout()->createBlock('productreports/adminhtml_report_workflow_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile($fileName));
    }
	
	public function exportWorkflowXmlAction()
    {
		//throwing the error in generating the XML because of rupee symbol (` sign)
        $fileName   = 'workflow.xml';
        $grid       = $this->getLayout()->createBlock('productreports/adminhtml_report_workflow_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }
	
	public function stockpriceAction()
    {
        $this->_title($this->__('Reports'))->_title($this->__('Products'))->_title($this->__('Stock Price Report'));
 
        $this->_initAction()
        ->_setActiveMenu('report/productreport/price')
        ->_addBreadcrumb(Mage::helper('productreports')->__('Stock Price Report'), Mage::helper('productreports')->__('Stock Price Report'));
 
        $gridBlock = $this->getLayout()->getBlock('productreports.stockprice.grid.container');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');
 
        $this->_initReportAction(array(
        $gridBlock,
        $filterFormBlock
        ));
		
        $this->renderLayout();
 
    }
	
	public function exportStockpriceCsvAction()
    {
        $fileName   = 'stockprice.csv';
        $content       = $this->getLayout()->createBlock('productreports/adminhtml_report_stockprice_grid')->getCsvFile();
        //$this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $content);
    }
	
	public function exportStockpriceXmlAction()
    {
		//throwing the error in generating the XML because of rupee symbol (` sign)
        $fileName   = 'stockprice.xml';
        $content    = $this->getLayout()->createBlock('productreports/adminhtml_report_stockprice_grid')->getExcelFile();
        //$this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $content);
    }
	
	public function stockAction()
    {
        $this->_title($this->__('Reports'))->_title($this->__('Products'))->_title($this->__('Stock Report'));
 
        $this->_initAction()
        ->_setActiveMenu('report/productreport/stock')
        ->_addBreadcrumb(Mage::helper('productreports')->__('Stock Report'), Mage::helper('productreports')->__('Stock Report'));
 
        $gridBlock = $this->getLayout()->getBlock('productreports.stock.grid.container');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');
 
        $this->_initReportAction(array(
        $gridBlock,
        $filterFormBlock
        ));
		
        $this->renderLayout();
 
    }
	
	public function exportStockCsvAction()
    {
        $fileName   = 'stock.csv';
        $grid       = $this->getLayout()->createBlock('productreports/adminhtml_report_stock_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile($fileName));
    }
	
	public function exportStockXmlAction()
    {
		//throwing the error in generating the XML because of rupee symbol (` sign)
        $fileName   = 'stock.xml';
        $grid       = $this->getLayout()->createBlock('productreports/adminhtml_report_stock_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }
	
	public function reloadcategoriesAction()
	{
		$ctId = $this->getRequest()->getParam('ctgid');
		
		$categories = Mage::helper('productreports')->getProductCategories($ctId);
		
		$options = "";
		
		if (!empty($ctId)) {
			foreach ( $categories as $id => $name ){
				$options .=	"<option value='". $id ."'>". $name ."</option>";
			}
		} else {
				$options = "<option value=''>". Mage::helper('productreports')->__('Select') ."</option>";
		}
		
		echo $options;
		//$options =  Mage::helper('core')->jsonEncode($options);	
		//$this->getResponse()->setHeader('Content-type', 'application/json');
		//$this->getResponse()->setBody($options);
	}
}