<?php

/*
 * File name: BannersController.php
 * Description: This file is used to manage actions (add, edit, delete and list) banners for home page
 * 
 * @created date:
 * @modify date:
 * @auther: Sanjay Kumar <sanjay.kumar@taslc.com>
 * @version: 1.0
 * @copyright: American Swan
 * 
 */
 
/*
 * class name: Aswan_Banners_Adminhtml_BannersController
 * Description: It is used to manage actions (add, edit, delete and list) banners for home page
 * 
 * @created date:
 * @modify date:
 * @auther: Sanjay Kumar <sanjay.kumar@taslc.com>
 * @version: 1.0
 * @copyright: American Swan
 * @package Banner
 */
  
class Custom_Banners_Adminhtml_BannersController extends Mage_Adminhtml_Controller_Action
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
    }
    
    /*
     * addAction() is used to upload banner
     * @param Null
     * @return Null
     */
      
    public function addAction()
    {
		
        $this->loadLayout();
		$this->renderLayout();

		if($this->getRequest()->isPost()){
			
			$dataArr = $this->getRequest()->getPost();
			
			$temp 					= explode(".",$_FILES['banner_upload']['name']);			
			$filepath 				= Mage::getBaseDir('media')."/banner/";	
			$target_file 			= $filepath.$temp['0'].time().".".$temp['1'];
			
			$data = array (
		
				'banner_title'		=>$dataArr['banner_title'],
				'start_date'		=>$dataArr['news_from_date'],
				'end_date'			=>$dataArr['news_to_date'],
				'banner_path'		=>$temp['0'].time().".".$temp['1'],
				'banner_url'		=>$dataArr['banner_url'],
				'banner_type'		=>$dataArr['banner_type'],
				'banner_status'		=>$dataArr['banner_status']
			
			);
			 
			Mage::getModel('banners/managebanners')->addBanners($data);
			
			if(isset($_FILES['banner_upload']['name'])) {
				
				move_uploaded_file($_FILES["banner_upload"]["tmp_name"], $target_file);
			}
			$this->_redirect('banners/adminhtml_banners/');
		}		
    }
    
    /*
     * editAction() is used to update uploaded banner
     * @param Null
     * @return Null
     */
     
    public function editAction()
    {
        $this->loadLayout();
		$this->renderLayout();

		$bannerId = $this->getRequest()->get('bid');
		if($this->getRequest()->isPost())
		{
			
			$postData = $this->getRequest()->getPost();
			
			$temp 					= explode(".",$_FILES['banner_upload']['name']);			
			$filepath 				= Mage::getBaseDir('media')."/banner/";	
			$target_file 			= $filepath.$temp['0'].time().".".$temp['1'];
			
			$data = array (
				'banner_title'		=>$postData['banner_title'],
				'start_date'		=>$postData['news_from_date'],
				'end_date'			=>$postData['news_to_date'],
				'banner_url'		=>$postData['banner_url'],
				'banner_type'		=>$postData['banner_type'],
				'banner_status'		=>$postData['banner_status'],
				'banner_id'			=>$bannerId
			);
			
			if(isset($_FILES["banner_upload"]["tmp_name"]) && $_FILES["banner_upload"]["tmp_name"] !=''){
				move_uploaded_file($_FILES["banner_upload"]["tmp_name"], $target_file);
				$data['banner_path']  = $temp['0'].time().".".$temp['1'];				
			}
			
			Mage::getModel('banners/managebanners')->editBanners($data);
			$this->_redirect('banners/adminhtml_banners/');
		}
    }
    
    /*
     * deleteAction() is used to update uploaded banner
     * @param Null
     * @return Null
     */
    
    public function deleteAction()
    {	
		$this->loadLayout();
		$this->renderLayout();	
		$bannerId = $this->getRequest()->get('bid');
		$bannerListArr = Mage::getModel('banners/managebanners')->deleteBanners($bannerId);
		if(isset($bannerListArr)){			
			$this->_redirect('banners/adminhtml_banners/');
		}
    }
}
