<?php

/*
 * File name: TaggingController.php
 * Description: This file is used to manage actions (add, edit, delete and list) banners for home page
 * 
 * @created date: 22/may/2015
 * @modify date:
 * @auther: Sanjay Kumar <sanjay.kumar@taslc.com>
 * @version: 1.0
 * @copyright: American Swan
 * 
 */
 
/*
 * class name: Product_Tagging_Adminhtml_TaggingController
 * Description: It is used to manage actions (add, edit, delete and list) banners for home page
 * 
 * @created date: 22/may/2015
 * @modify date:
 * @auther: Sanjay Kumar <sanjay.kumar@taslc.com>
 * @version: 1.0
 * @copyright: American Swan
 * @package Product Tagging
 */
  
class Product_Taggs_Adminhtml_TaggsController extends Mage_Adminhtml_Controller_Action
{
    /*
     * indexAction() is default action
     * @param Null
     * @return Null
     */ 
    
    public function indexAction()
    {
		$this->loadLayout();
		$this->renderLayout(); 
		
		$write 	= Mage::getSingleton('core/resource')->getConnection('core_write');
		$read 	= Mage::getSingleton('core/resource')->getConnection('core_read');
		
		if($this->getRequest()->isPost())
		{
			$data = $this->getRequest()->getPost();
			$catId = $data['sub_category'];
			
			if(isset($_FILES['csv_file']['tmp_name']))
			{
				if (($handle = fopen($_FILES['csv_file']['tmp_name'], "r")) !== FALSE)
				{
					while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) 
					{
						$product_id = Mage::getModel('catalog/product')->getIdBySku($data[0]);
						$position = isset($data[1]) ? $data[1] : 1;
						if($product_id)
						{
							$sql = "select count(category_id) as category_id FROM catalog_category_product 
									WHERE category_id='".$catId."' AND product_id='".$product_id."'";				
							$countInfo = $read->query($sql);
							$countRes = $countInfo->fetch();
							if($countRes['category_id'] == 0)
							{
								$write->query("insert into catalog_category_product values ('".$catId."','".$product_id."','".$position."');");
							}
						}
					}
				}
				Mage::getSingleton('core/session')->addSuccess('Product tagged successfully.');
				$this->_redirect('*/*/index');
			}
		}
	}
 /*
     * getSubcategoryAction() is used to get subcategory id.
     * @param Null
     * @return Null
     */ 

    public function getSubcategoryAction(){
		$_rootCatId = $this->getRequest()->getParam('category');
        $_rootCategory  = Mage::getModel('catalog/category')->load($_rootCatId);
        $collection = $_rootCategory->getChildrenCategories()->addAttributeToFilter('is_active', 1);        
        $data = array();
        $i=1;
        foreach ($collection as $cat) {
            $data[$i]['id'] = $cat->getId();
            $data[$i]['name'] = $cat->getName();
            $i++;
        }
        $data = json_encode($data);
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody($data);
    }
    
     /*
     * exportAction() is used to export sku from subcategory.
     * @param Null
     * @return Null
     */ 
     
    public function exportAction() {
		
		$postArr = $this->getRequest()->getPost();
		$catid = $postArr['sub_category'];
		$catagory_model = Mage::getModel('catalog/category')->load($catid);		
		$collection  =  Mage::getModel('catalog/product')->getCollection();
		$collection->addCategoryFilter($catagory_model);
		$collection->addAttributeToFilter('type_id','configurable');
		
		header( 'Content-Type: text/csv' );
		header( 'Content-Disposition: attachment;filename='.$catid."_products.csv");
		$out = fopen('php://output', 'w');
		
		foreach($collection as $product) {
			fputcsv($out, array($product->getData('sku')));
		}
		fclose($out);
	}
}
