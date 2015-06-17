<?php 

class Custom_Newarrival_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
		$this->loadLayout()->renderLayout();
    }
    
    public function addAction()
    {
		$newarrival_id =  $this->getRequest()->getParam('id');
		if($this->getRequest()->isPost()){
			$dataArr =  $this->getRequest()->getPost();
			$countArr = count(explode(',' ,$dataArr['sku']));
			$data = array('cat_id' 	=> $dataArr['cat_id'], 'sku'=> $dataArr['sku'], 'is_default'=>$dataArr['is_default']);
			$updateDataArr = array(
			
				'cat_id' 		=>$dataArr['cat_id'], 
				'sku'			=>$dataArr['sku'], 
				'is_default'	=>$dataArr['is_default'],
				'newarrival_id'	=>$newarrival_id 
			);
			
			try {
				
				if((empty($dataArr['cat_id']) ||  $dataArr['cat_id']== null) ){
					throw new Exception('Please select Category');
				}	
							
				else if($dataArr['is_default'] == '0' && (empty($dataArr['sku']) ||  $dataArr['sku']== null) ){
					throw new Exception('Please enter SKU');
				}	
							
				else if($dataArr['is_default'] == '0' && $countArr <= 4){
					throw new Exception('Please enter more than four SKU code');
				}
				
				else {
					if(isset($newarrival_id) && $newarrival_id != '') {
						$dataUpdate = Mage::getModel('newarrival/managenewarrival')->updateData($updateDataArr);
						Mage::getSingleton('core/session')->addSuccess('Data updated successfully');		
					}
					else {
						$checkUniqueCatId = Mage::getModel('newarrival/managenewarrival')->checkUnique($dataArr['cat_id']);
						if(isset($checkUniqueCatId) && $checkUniqueCatId!=''){
							Mage::getSingleton('core/session')->addError('Cat Id Already Exit.');
						}
						else{
								Mage::getModel('newarrival/managenewarrival')->saveData($data);
								Mage::getSingleton('core/session')->addSuccess('Data inserted successfully');
							}
					}		
				}
			} 
			catch (Exception $e) {
				Mage::getSingleton('core/session')->addError($e->getMessage());
			}
		}
		$this->loadLayout()->renderLayout();	
	}
}
	
